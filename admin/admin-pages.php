<?php

/**
 * Class use to create admin menu pages 
 */

namespace AdminMenuPages;

class WPGym_Admin_Pages
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu_page']);
    }

    public function add_menu_page()
    {
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
    }

    public function render_dashboard()
    {
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p>Welcome to the WPGym dashboard. Manage your gym operations here.</p>
        </div>
    <?php
    }

    public function render_membership_plans()
    {
    ?>
        <div class="wrap">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'memberships';
            $memberships = $wpdb->get_results("SELECT * FROM $table_name");

            include(plugin_dir_path(__FILE__) . '../membership/templates/memberships-list.php');
            ?>

        </div>
    <?php
    }
    public function add_new_membership_page()
    {
        include(plugin_dir_path(__FILE__) . '../membership/templates/add-new-membership.php');
    }


    public function render_user_management()
    {
    ?>
        <div class="wrap">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="m-0"><?= __('User Management', 'wpgym') ?></h1>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle shadow-sm">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th scope="col" class="text-center"><?= __('Unique ID', 'wpgym') ?></th>
                            <th scope="col"><?= __('Username', 'wpgym') ?></th>
                            <th scope="col"><?= __('Email', 'wpgym') ?></th>
                            <th scope="col"><?= __('Membership Type', 'wpgym') ?></th>
                            <th scope="col" class="text-center"><?= __('Status', 'wpgym') ?></th>
                            <th scope="col" class="text-center"><?= __('Start Date', 'wpgym') ?></th>
                            <th scope="col" class="text-center"><?= __('End Date', 'wpgym') ?></th>
                            <th scope="col" class="text-center"><?= __('Actions', 'wpgym') ?></th>
                        </tr>
                    </thead>
                    <tbody id="gym-members-list">
                        <!-- This will be populated via AJAX -->
                    </tbody>
                </table>
            </div>

            <?php
            require_once wygym_path . '/users/templates/update-from.php';
            ?>
        </div>


<?php
    }
}

new WPGym_Admin_Pages();
