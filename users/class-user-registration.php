<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class GymMembershipRegistration
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('init', array($this, 'create_gym_member_role'));
        // add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_shortcode('gym_registration_form', array($this, 'registration_form_shortcode'));

        // AJAX hooks
        add_action('wp_ajax_add_gym_member', array($this, 'ajax_add_gym_member'));
        add_action('wp_ajax_edit_gym_member', array($this, 'ajax_edit_gym_member'));
        add_action('wp_ajax_delete_gym_member', array($this, 'ajax_delete_gym_member'));
        add_action('wp_ajax_get_gym_members', array($this, 'ajax_get_gym_members'));
        add_action('wp_ajax_get_gym_member', array($this, 'ajax_get_gym_member'));
        add_action('wp_ajax_nopriv_register_gym_member', array($this, 'ajax_register_gym_member'));
    }

    public function create_gym_member_role()
    {
        add_role('gym_member', 'Gym Member', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false
        ));
    }

    public function add_admin_menu()
    {
        add_menu_page('Gym Members', 'Gym Members', 'manage_options', 'gym-members', array($this, 'admin_page'), 'dashicons-groups', 6);
    }

    public function admin_page()
    {
        include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook != 'toplevel_page_gym-members') {
            //return;
        }
        wp_enqueue_script('gym-members-admin', plugin_dir_url(__DIR__) . '/assets/js/user-ajax-handler.js', array('jquery'), '1.0', true);
        wp_localize_script('gym-members-admin', 'gymMembersAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gym_members_nonce')
        ));
    }

    public function enqueue_frontend_scripts()
    {
        wp_enqueue_script('gym-members-frontend', plugin_dir_url(__FILE__) . 'js/frontend.js', array('jquery'), '1.0', true);
        wp_localize_script('gym-members-frontend', 'gymMembersFrontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gym_members_nonce')
        ));
    }

    public function registration_form_shortcode()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/registration-form.php';
        return ob_get_clean();
    }

    // AJAX methods will be implemented here
    // Add these methods to the GymMembershipRegistration class

    public function ajax_get_gym_members()
    {
        if (!check_ajax_referer('gym_members_nonce', 'nonce', false)) {
            $this->log_error('Nonce verification failed');
            wp_send_json_error('Nonce verification failed', 400);
        }

        if (!current_user_can('manage_options')) {
            $this->log_error('Unauthorized access attempt');
            wp_send_json_error('Unauthorized', 403);
        }

        $users = get_users(array(
            'role' => 'gym_member',
            'orderby' => 'user_registered',
            'order' => 'DESC'
        ));

        if (empty($users)) {
            $this->log_error('No gym members found');
            wp_send_json_error('No gym members found', 404);
        }

        ob_start();
        foreach ($users as $user) {
            $unique_id = get_user_meta($user->ID, 'unique_id', true);
            $membership_type_id = get_user_meta($user->ID, 'membership_type', true);
            $membership_type = $this->get_membership_type_name($membership_type_id);
            $status = get_user_meta($user->ID, 'status', true);
            $registration_date = get_user_meta($user->ID, 'registration_date', true);
            $end_date = get_user_meta($user->ID, 'end_date', true);
?>
            <tr>
                <td class="text-center"><?php echo esc_html($unique_id); ?></td>
                <td><?php echo esc_html($user->user_login); ?></td>
                <td><?php echo esc_html($user->user_email); ?></td>
                <td><?php echo esc_html($membership_type); ?></td>
                <td class="text-center">
                    <span class="status-badge <?php echo ($status === 'Active') ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo esc_html($status); ?>
                    </span>
                </td>
                <td class="text-center"><?php echo esc_html($registration_date); ?></td>
                <td class="text-center"><?php echo esc_html($end_date); ?></td>
                <td class="text-center">
                    <button class="edit-member button btn-sm btn-edit" data-id="<?php echo esc_attr($user->ID); ?>">
                        <?= __('Edit', 'wpgym') ?>
                    </button>
                    <button class="delete-member button btn-sm btn-delete" data-id="<?php echo esc_attr($user->ID); ?>">
                        <?= __('Delete', 'wpgym') ?>
                    </button>
                </td>
            </tr>
<?php
        }
        $html = ob_get_clean();

        if (empty($html)) {
            $this->log_error('No gym members data generated');
            wp_send_json_error('No gym members data generated', 500);
        }

        wp_send_json_success($html);
    }
    public function ajax_get_gym_member()
    {
        check_ajax_referer('gym_members_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $user_id = intval($_POST['user_id']);
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error('User not found');
        }

        $member_data = array(
            'ID' => $user->ID,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'unique_id' => get_user_meta($user->ID, 'unique_id', true),
            'membership_type' => get_user_meta($user->ID, 'membership_type', true),
            'status' => get_user_meta($user->ID, 'status', true),
            'registration_date' => get_user_meta($user->ID, 'registration_date', true),
            'end_date' => get_user_meta($user->ID, 'end_date', true)
        );

        wp_send_json_success($member_data);
    }


    public function ajax_add_gym_member()
    {
        check_ajax_referer('gym_members_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access');
        }

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $membership_type = sanitize_text_field($_POST['membership_type']);
        $status = sanitize_text_field($_POST['status']);

        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error('Username, email, and password are required');
        }

        if (!is_email($email)) {
            wp_send_json_error('Invalid email address');
        }

        // Check if username or email already exists
        if (username_exists($username)) {
            wp_send_json_error('Username already exists');
        }

        if (email_exists($email)) {
            wp_send_json_error('Email address already exists');
        }

        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            error_log('Gym Membership Registration - Error creating user: ' . $user_id->get_error_message());
            wp_send_json_error('Error creating user: ' . $user_id->get_error_message());
        }

        // $user_id = wp_create_user($username, $password, $email);
        // if (is_wp_error($user_id)) {
        //     $this->log_error('Error creating user: ' . $user_id->get_error_message());
        //     wp_send_json_error('Error creating user: ' . $user_id->get_error_message());
        // }

        $user = new WP_User($user_id);
        $user->set_role('gym_member');

        $unique_id = $this->generate_unique_id();
        $registration_date = current_time('mysql');
        $end_date = $this->calculate_end_date($membership_type, $registration_date);

        update_user_meta($user_id, 'unique_id', $unique_id);
        update_user_meta($user_id, 'membership_type', $membership_type);
        update_user_meta($user_id, 'status', $status);
        update_user_meta($user_id, 'registration_date', $registration_date);
        update_user_meta($user_id, 'end_date', $end_date);

        wp_send_json_success('Member added successfully');
    }

    public function ajax_edit_gym_member()
    {
        check_ajax_referer('gym_members_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $user_id = intval($_POST['user_id']);
        $email = sanitize_email($_POST['email']);
        $membership_type = sanitize_text_field($_POST['membership_type']);
        $status = sanitize_text_field($_POST['status']);

        $user_data = array(
            'ID' => $user_id,
            'user_email' => $email
        );

        if (!empty($_POST['password'])) {
            $user_data['user_pass'] = $_POST['password'];
        }

        $user_id = wp_update_user($user_data);
        if (is_wp_error($user_id)) {
            wp_send_json_error($user_id->get_error_message());
        }
        $unique_id = $this->generate_unique_id();
        update_user_meta($user_id, 'unique_id', $unique_id);
        update_user_meta($user_id, 'membership_type', $membership_type);
        update_user_meta($user_id, 'status', $status);

        wp_send_json_success('Member updated successfully');
    }

    public function ajax_delete_gym_member()
    {
        check_ajax_referer('gym_members_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $user_id = intval($_POST['user_id']);
        if (wp_delete_user($user_id)) {
            wp_send_json_success('Member deleted successfully');
        } else {
            wp_send_json_error('Failed to delete member');
        }
    }

    public function ajax_register_gym_member()
    {
        check_ajax_referer('gym_members_nonce', 'nonce');

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $membership_type = sanitize_text_field($_POST['membership_type']);

        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error('Username, email, and password are required');
        }

        if (!is_email($email)) {
            wp_send_json_error('Invalid email address');
        }

        // Check if username or email already exists
        if (username_exists($username)) {
            wp_send_json_error('Username already exists');
        }

        if (email_exists($email)) {
            wp_send_json_error('Email address already exists');
        }

        // Dummy payment process
        $payment_successful = $this->process_payment($_POST['payment_data']);

        if (!$payment_successful) {
            wp_send_json_error('Payment failed');
        }

        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            error_log('Gym Membership Registration - Error creating user: ' . $user_id->get_error_message());
            wp_send_json_error('Error creating user: ' . $user_id->get_error_message());
        }

        $user = new WP_User($user_id);
        $user->set_role('gym_member');

        $unique_id = $this->generate_unique_id();
        $registration_date = current_time('mysql');
        $end_date = $this->calculate_end_date($membership_type, $registration_date);

        update_user_meta($user_id, 'unique_id', $unique_id);
        update_user_meta($user_id, 'membership_type', $membership_type);
        update_user_meta($user_id, 'status', 'active');
        update_user_meta($user_id, 'registration_date', $registration_date);
        update_user_meta($user_id, 'end_date', $end_date);

        wp_send_json_success('Registration successful');
    }
    // public function ajax_register_gym_member() {
    //     check_ajax_referer('gym_members_nonce', 'nonce');

    //     $username = sanitize_user($_POST['username']);
    //     $email = sanitize_email($_POST['email']);
    //     $password = $_POST['password'];
    //     $membership_type = sanitize_text_field($_POST['membership_type']);

    //     // Validate input
    //     if (empty($username) || empty($email) || empty($password)) {
    //         wp_send_json_error('Username, email, and password are required');
    //     }

    //     if (!is_email($email)) {
    //         wp_send_json_error('Invalid email address');
    //     }

    //     // Check if username or email already exists
    //     if (username_exists($username)) {
    //         wp_send_json_error('Username already exists');
    //     }

    //     if (email_exists($email)) {
    //         wp_send_json_error('Email address already exists');
    //     }

    //     // Dummy payment process
    //     $payment_successful = $this->process_payment($_POST['payment_data']);

    //     if (!$payment_successful) {
    //         wp_send_json_error('Payment failed');
    //     }

    //     $user_id = wp_create_user($username, $password, $email);
    //     if (is_wp_error($user_id)) {
    //         $this->log_error('Error creating user: ' . $user_id->get_error_message());
    //         wp_send_json_error('Error creating user: ' . $user_id->get_error_message());
    //     }

    //     $user = new WP_User($user_id);
    //     $user->set_role('gym_member'); // Ensure the user is assigned the gym_member role

    //     update_user_meta($user_id, 'membership_type', $membership_type);
    //     update_user_meta($user_id, 'status', 'active');

    //     wp_send_json_success('Registration successful');
    // }
    private function generate_unique_id()
    {
        $prefix = 'GYM';
        $unique_number = mt_rand(100000, 999999);
        $unique_id = $prefix . $unique_number;

        // Ensure the ID is unique
        while (get_users(array('meta_key' => 'unique_id', 'meta_value' => $unique_id))) {
            $unique_number = mt_rand(100000, 999999);
            $unique_id = $prefix . $unique_number;
        }

        return $unique_id;
    }
    private function get_membership_types()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';
        $results = $wpdb->get_results("SELECT id, type, price FROM $table_name", ARRAY_A);
        return $results ? $results : array();
    }
    private function get_membership_type_name($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';
        $type_name = $wpdb->get_var($wpdb->prepare("SELECT type FROM $table_name WHERE id = %d", $id));

        return $type_name;
    }

    private function calculate_end_date($membership_type_id, $start_date)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';
        $duration = strtolower($wpdb->get_var($wpdb->prepare("SELECT type FROM $table_name WHERE id = %d", $membership_type_id)));

        if (!$duration) {
            return date('Y-m-d H:i:s', strtotime('+30 days', strtotime($start_date))); // Default to 30 days if duration not found
        }

        switch ($duration) {
            case 'yearly':
                return date('Y-m-d H:i:s', strtotime('+365 days', strtotime($start_date)));
            case 'monthly':
                return date('Y-m-d H:i:s', strtotime('+30 days', strtotime($start_date)));
            default:
                return date('Y-m-d H:i:s', strtotime('+30 days', strtotime($start_date))); // Default to 30 days
        }
    }
    private function process_payment($payment_data)
    {
        // This is a dummy payment process
        // In a real-world scenario, you would integrate with a payment gateway here
        return true;
    }
}

// Initialize the plugin
GymMembershipRegistration::get_instance();
