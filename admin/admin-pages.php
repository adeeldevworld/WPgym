<?php 
/**
 * Class use to create admin menu pages 
 */
namespace AdminMenuPages;

class WPGym_Admin_Pages {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page']);
    }

    public function add_menu_page() {
        // Add top-level menu page
        add_menu_page(
            'WPGym Dasboard',
            'WPGym',
            'manage_options',
            'wpgym-dashboard',
            [$this, 'render_dashboard'],
            'dashicons-groups',
            80
        );

        // Add submenu pages
        add_submenu_page(
            'wpgym-dashboard', 
            'Membership Plans', 
            'Membership Plans',
            'manage_options',
            'wpgym-membership-plans',
            [$this, 'render_membership_plans']
        );
        add_submenu_page('wpgym-membership-plans', 'All Memberships', 'All Memberships', 'manage_options', 'membership-manager', array($this, 'render_membership_plans'));
        add_submenu_page('wpgym-membership-plans', 'Add New Membership', 'Add New Membership', 'manage_options', 'add-new-membership', array($this, 'add_new_membership_page'));

        add_submenu_page(
            'wpgym-dashboard',
            'User Management',
            'User Management',
            'manage_options',
            'wpgym-user-management',
            [$this, 'render_user_management']
        );

        add_submenu_page(
            'wpgym-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'wpgym-settings',
            [$this, 'render_settings']
        );
    }

    public function render_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p>Welcome to the WPGym dashboard. Manage your gym operations here.</p>
        </div>
        <?php
    }

    public function render_membership_plans() {
        ?>
        <div class="wrap">
            <h1>Membership Plans</h1>
            <p>Manage membership plans for your gym members here.</p>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'memberships';
            $memberships = $wpdb->get_results("SELECT * FROM $table_name");
    
            include(plugin_dir_path(__FILE__) . '../membership/templates/memberships-list.php');
            ?>

        </div>
        <?php
    }
    public function add_new_membership_page() {
        include(plugin_dir_path(__FILE__) . '../membership/templates/add-new-membership.php');
    }


    public function render_user_management() {
        ?>
        <div class="wrap">
            <h1>User Management</h1>
            <!-- templates/admin-page.php -->
<div class="wrap">
    <h1>Gym Members</h1>
    <button id="add-new-member" class="button button-primary">Add New Member</button>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
            <th>Unique Id</th>

                <th>Username</th>
                <th>Email</th>
                <th>Membership Type</th>
                <th>Status</th>
                <th>Membership Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="gym-members-list">
            <!-- This will be populated via AJAX -->
        </tbody>
    </table>
</div>

<div id="member-modal" style="display:none;">
    <form id="member-form">
        <input type="hidden" id="user-id" name="user_id">
        <p>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </p>
        <p>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
        </p>
        <p>
            <label for="membership-type">Membership Type:</label>
            <select id="membership-type" name="membership_type">
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'memberships';
                $results = $wpdb->get_results("SELECT id, type, price FROM $table_name", ARRAY_A);
                $membership_types = $results;
                foreach ($membership_types as $type) {
                    echo '<option value="' . esc_attr($type['id']) . '" data-price="' . esc_attr($type['price']) . '">' . esc_html($type['type']) . ' - $' . esc_html($type['price']) . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" readonly>
        </p>
        <p>
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </p>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('gym_members_nonce'); ?>">

        <button type="submit" class="button button-primary">Save Member</button>
    </form>
</div>
        </div>

        
        <?php
    }

    public function render_settings() {
        ?>
        <div class="wrap">
            <h1>Settings</h1>
            <p>Customize WPGym settings here.</p>
        </div>
        <?php
    }
    

}

new WPGym_Admin_Pages();