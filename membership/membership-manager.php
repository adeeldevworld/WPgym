<?php

class MembershipManager {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        //register_activation_hook(__FILE__, array($this, 'activate_plugin'));
       // add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_add_membership', array($this, 'ajax_add_membership'));
        add_action('wp_ajax_edit_membership', array($this, 'ajax_edit_membership'));
        add_action('wp_ajax_get_membership', array($this, 'ajax_get_membership'));
        add_action('wp_ajax_delete_membership', array($this, 'ajax_delete_membership'));
        add_action('admin_notices', array($this, 'display_admin_notices'));
        add_shortcode('gym_memberships', array($this, 'gym_memberships_shortcode'));
    }
    
    public function gym_memberships_shortcode($atts) {
        wp_enqueue_script('gym-management-frontend');
    
        global $wpdb;
            $table_name = $wpdb->prefix . 'memberships';
            $memberships = $wpdb->get_results("SELECT * FROM $table_name");

        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/frontend-memberships.php';
        return ob_get_clean();
    }

    public function render_admin_notice($message, $type = 'success') {
        return sprintf(
            '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
            esc_attr($type),
            esc_html($message)
        );
    }
    public function set_admin_notice($message, $type = 'success') {
        $notices = get_transient('gym_management_admin_notices') ?: array();
        $notices[] = array(
            'message' => $message,
            'type' => $type
        );
        set_transient('gym_management_admin_notices', $notices, 60);
    }

    public function display_admin_notices() {
        $notices = get_transient('gym_management_admin_notices');
        if ($notices) {
            foreach ($notices as $notice) {
                echo '<div class="notice notice-' . esc_attr($notice['type']) . ' is-dismissible"><p>' . esc_html($notice['message']) . '</p></div>';
            }
            delete_transient('gym_management_admin_notices');
        }
    }
    public function enqueue_admin_scripts($hook) {
        if ('memberships_page_add-new-membership' !== $hook && 'toplevel_page_membership-manager' !== $hook) {
          //  return;
        }
        wp_enqueue_media();
        wp_enqueue_script('membership-manager-js', plugin_dir_url(__FILE__) . '../assets/js/membership-manager.js', array('jquery'), '1.0', true);
        wp_localize_script('membership-manager-js', 'membershipManager', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('membership-manager-nonce')
        ));
    }
    public function ajax_add_membership() {
        error_log('ajax_add_membership called');
        error_log('POST data: ' . print_r($_POST, true));
    
        try {
            check_ajax_referer('membership-manager-nonce', 'nonce');
    
            if (!current_user_can('manage_options')) {
                throw new Exception('Unauthorized');
            }
    
            global $wpdb;
            $table_name = $wpdb->prefix . 'memberships';
    
            $data = array(
                'photo_url' => isset($_POST['photo_url']) ? sanitize_text_field($_POST['photo_url']) : '',
                'name' => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
                'type' => sanitize_text_field($_POST['type']), // Yearly or Monthly
                'price' => isset($_POST['price']) ? sanitize_text_field($_POST['price']) : '',
                'billing_type' => isset($_POST['billing_type']) ? sanitize_text_field($_POST['billing_type']) : '',
                'dashboard_access' => isset($_POST['dashboard_access']) ? sanitize_text_field($_POST['dashboard_access']) : '',
                'access_hours' => isset($_POST['access_hours']) ? sanitize_text_field($_POST['access_hours']) : '',
                'benefits' => isset($_POST['benefits']) ? sanitize_textarea_field($_POST['benefits']) : '',
            );
    
            error_log('Sanitized data: ' . print_r($data, true));
    
            $result = $wpdb->insert($table_name, $data);
    
            if ($result === false) {
                throw new Exception('Failed to add membership: ' . $wpdb->last_error);
            } else {
                wp_send_json_success(array(
                    'message' => 'Membership added successfully',
                    'notice' => $this->render_admin_notice('Member added successfully', 'success')
                ));
            }
        } catch (Exception $e) {
            error_log('Error in ajax_add_membership: ' . $e->getMessage());
            wp_send_json_error(array(
                'message' => $e->getMessage(),
                'notice' => $this->render_admin_notice($e->getMessage(), 'error')
            ));
        }
    }
    
    
    
    
    public function ajax_edit_membership() {
        check_ajax_referer('membership-manager-nonce', 'nonce');
    
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';
    
        $id = intval($_POST['id']);
        $data = array(
            'photo_url' => isset($_POST['photo_url']) ? sanitize_text_field($_POST['photo_url']) : '',
            'name' => sanitize_text_field($_POST['name']),
            'type' => sanitize_text_field($_POST['type']), // Yearly or Monthly
            'price' => sanitize_text_field($_POST['price']),
            'billing_type' => sanitize_text_field($_POST['billing_type']),
            'dashboard_access' => sanitize_text_field($_POST['dashboard_access']),
            'access_hours' => sanitize_text_field($_POST['access_hours']),
            'benefits' => sanitize_textarea_field($_POST['benefits'])
        );
    
        $wpdb->update($table_name, $data, array('id' => $id));
    
        if ($wpdb->last_error) {
            wp_send_json_error(array(
                'message' => 'Failed to update membership',
                'notice' => $this->render_admin_notice('Failed to update membership', 'error')
            )
                );
        } else {
            wp_send_json_success(array(
                'message' => 'Membership updated successfully',
                'notice' => $this->render_admin_notice('Membership updated successfully', 'success')
            ));
        }
    }
    
    
    
    public function ajax_get_membership() {
        check_ajax_referer('membership-manager-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $id = intval($_POST['id']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';

        $membership = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);

        if ($membership) {
            // Ensure all numeric values are properly formatted
            // $membership['one_time_fee'] = number_format((float)$membership['one_time_fee'], 2, '.', '');
            // $membership['signup_fee'] = number_format((float)$membership['signup_fee'], 2, '.', '');
            // $membership['quarterly_amount'] = number_format((float)$membership['quarterly_amount'], 2, '.', '');
            // $membership['yearly_amount'] = number_format((float)$membership['yearly_amount'], 2, '.', '');
            $membership['price'] = number_format((float)$membership['price'], 2, '.', '');

            wp_send_json_success($membership);
        } else {
            wp_send_json_error('Membership not found');
        }
    }

    public function ajax_delete_membership() {
        check_ajax_referer('membership-manager-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';

        $id = intval($_POST['id']);

        $wpdb->delete($table_name, array('id' => $id));

        if ($wpdb->last_error) {
            wp_send_json_error(array(
                'message' => 'Failed to delete membership',
                'notice' => $this->render_admin_notice('Failed to delte membership', 'error')
            ));
        } else {
            wp_send_json_success(array(
                'message' => 'Membership deleted successfully',
                'notice' => $this->render_admin_notice('Membership deleted successfully', 'success')
            ));
        }
    }
}

MembershipManager::get_instance();