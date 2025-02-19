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
        </div>
        <?php
    }

    public function render_user_management() {
        ?>
        <div class="wrap">
            <h1>User Management</h1>
            <p>Manage gym users and their details here.</p>
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