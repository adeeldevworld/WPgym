<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use PeachPayments\PeachPayments;
use WPGymAccessControl\WPGymAccessControl;

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
        add_action('wp_ajax_register_gym_member', array($this, 'ajax_register_gym_member'));
        add_action('wp_ajax_nopriv_register_gym_member', array($this, 'ajax_register_gym_member'));
        add_action('wp_ajax_verify_payment_and_create_user', array($this, 'verify_payment_and_create_user'));
        add_action('wp_ajax_nopriv_verify_payment_and_create_user', array($this, 'verify_payment_and_create_user'));
    }

    public function create_gym_member_role()
    {
        add_role('gym_member', 'Gym Member', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false
        ));
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
            $payment_status = get_user_meta($user->ID, 'payment_staus', true);
            $registration_date = get_user_meta($user->ID, 'registration_date', true);
            $end_date = get_user_meta($user->ID, 'end_date', true);
?>
            <tr class="align-middle status-row" data-status="">
                <!-- Unique ID -->
                <td class="text-center"><?php echo esc_html($unique_id); ?></td>

                <!-- Username -->
                <td><?php echo esc_html($user->user_login); ?></td>

                <!-- Email -->
                <td><?php echo esc_html($user->user_email); ?></td>

                <!-- Membership Type -->
                <td><?php echo esc_html($membership_type); ?></td>

                <!-- Status -->
                <td class="text-center">
                    <span class="badge <?php echo ($status === 'active') ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo esc_html($status); ?>
                    </span>
                </td>

                <!-- Payment Status -->
                <td class="text-center">
                    <span class="badge <?php echo ($payment_status === 'success') ? 'bg-success' : 'bg-warning'; ?>">
                        <?php echo esc_html($payment_status); ?>
                    </span>
                </td>

                <!-- Registration Date -->
                <td class="text-center"><?php echo esc_html($registration_date); ?></td>

                <!-- End Date -->
                <td class="text-center"><?php echo esc_html($end_date); ?></td>

                <!-- Actions -->
                <td class="text-center">
                    <button class="btn btn-sm btn-primary edit-member" data-id="<?php echo esc_attr($user->ID); ?>">
                        <?= __('Edit', 'wpgym') ?>
                    </button>
                    <button class="btn btn-sm btn-danger delete-member ms-2" data-id="<?php echo esc_attr($user->ID); ?>">
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
            'payment_staus' => get_user_meta($user->ID, 'payment_staus', true),
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
        $unique_id = get_user_meta($user_id, 'unique_id', true);
        $unique_id = get_user_meta($user_id, 'unique_id', true);

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
        update_user_meta($user_id, 'membership_type', $membership_type);
        update_user_meta($user_id, 'status', $status);

        /**
         * CHeck if status is inactive remove from access control
         */
        if($status === 'inactive'){
            //uncomment when issue reolved
            // WPGymAccessControl::deleteUser($unique_id);
        }
        else{
            $userData =
            [
                // "EnrollNumber" => $unique_id,
                "EnrollNumber" => "3434",
                "Name" => "John Doe Only hj",
                "Password" => "1234",
                "Privilege" => 0,
            ];
            // WPGymAccessControl::updateUser($userData);
        }

        wp_send_json_success('Member updated successfully');
    }

    public function ajax_delete_gym_member()
    {
        check_ajax_referer('gym_members_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $user_id = intval($_POST['user_id']);
        // WPGymAccessControl::deleteUser($uniq_id)
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
        $price = sanitize_text_field($_POST['price']);
        $redirectURL = sanitize_text_field($_POST['redirectURL']);
        $unique_id = $this->generate_unique_id();

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

        $response = PeachPayments::initiatePayment(
            $unique_id,
            $price,
            'USD',
            'CARD',
            'PA',
            $redirectURL
        );

        if ($response['error']) {
            if (isset($response['details'])) {
                wp_send_json_error($response['message']);
            }
        } else {
            if (isset($response['redirectUrl'])) {
                set_transient('pending_registration_' . $unique_id, [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'membership_type' => $membership_type,
                    'price' => $price,
                    'unique_id' => $unique_id,
                ], HOUR_IN_SECONDS);
                wp_send_json_success(['redirect' => $response['redirectUrl']]);
            } else {
                wp_send_json_error(['message' => 'Payment initiation failed. Please try again.']);
            }
        }

        // $user_id = wp_create_user($username, $password, $email);
        // if (is_wp_error($user_id)) {
        //     error_log('Gym Membership Registration - Error creating user: ' . $user_id->get_error_message());
        //     wp_send_json_error('Error creating user: ' . $user_id->get_error_message());
        // }

        // $user = new WP_User($user_id);
        // $user->set_role('gym_member');
        // $registration_date = current_time('mysql');
        // $end_date = $this->calculate_end_date($membership_type, $registration_date);

        // update_user_meta($user_id, 'unique_id', $unique_id);
        // update_user_meta($user_id, 'membership_type', $membership_type);
        // update_user_meta($user_id, 'status', 'active');
        // update_user_meta($user_id, 'registration_date', $registration_date);
        // update_user_meta($user_id, 'end_date', $end_date);

        // // wp_send_json_success('Registration successful');
        wp_die();
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
    public  function verify_payment_and_create_user()
    {
        if (isset($_POST['id']) && isset($_POST['resourcePath'])) {
            $response = PeachPayments::fetchTransactionDetails($_POST['id']);
            $unique_id = $response['data']['merchantTransactionId'];
            $registration_data = get_transient('pending_registration_' . $unique_id);
            if ($registration_data) {
                // Create the user
                $user_id = wp_create_user(
                    $registration_data['username'],
                    $registration_data['password'],
                    $registration_data['email']
                );

                if (is_wp_error($user_id)) {
                    error_log('Gym Membership Registration - Error creating user: ' . $user_id->get_error_message());
                    wp_die('Error creating user: ' . $user_id->get_error_message());
                }

                // Set user role and metadata
                $user = new WP_User($user_id);
                $user->set_role('gym_member');

                $registration_date = current_time('mysql');
                $end_date = $this->calculate_end_date($registration_data['membership_type'], $registration_date);

                update_user_meta($user_id, 'unique_id', $registration_data['unique_id']);
                update_user_meta($user_id, 'user_password', $registration_data['password']);
                update_user_meta($user_id, 'membership_type', $registration_data['membership_type']);
                update_user_meta($user_id, 'registration_date', $registration_date);
                update_user_meta($user_id, 'end_date', $end_date);

                // Clear the transient
                delete_transient('pending_registration_' . $unique_id);
                if ($response['sucess']) {
                    // [
                    //     // "EnrollNumber" => 1234555, 
                    //     "Name" => "John Doe test",
                    //     "Password" => "1234",
                    //     "Privilege" => 0,
                    //     "CardNumber" => "12345678"
                    // ]
                    // WPGymAccessControl::addUser($userData);
                    update_user_meta($user_id, 'payment_staus', 'success');
                    update_user_meta($user_id, 'status', 'active');
                    wp_send_json_success([
                        'message' => 'Registration successful! Thank you for signing up.',
                    ]);
                } else {
                    update_user_meta($user_id, 'status', 'Inactive');
                    update_user_meta($user_id, 'payment_staus', 'pending');
                    wp_send_json_error(['message' => 'Payment verification' . ' ' . $response['status']]);
                }
            } else {
                wp_send_json_error(['message' => 'Invalid or expired registration data.']);
            }
        } else {
            wp_send_json_error(['message' => 'Payment verification failed.']);
        }
    }
}

// Initialize the plugin
GymMembershipRegistration::get_instance();
