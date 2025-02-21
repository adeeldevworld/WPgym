<?php

/**
 * Register activation hook for this plugin by invoking activate
 * in WygymActication class.
 *
 * @param string   wygym__mainfile path to the plugin file.
 * @param callback wygym_activation_callback The function to be run when the plugin is activated.
 */

namespace wygymactication;

class WygymActication
{
    public function __construct()
    {
        register_activation_hook(wygym__mainfile, [$this, 'wygym_activation_callback']);
    }

    public function wygym_activation_callback()
    {
        // Plugin Activation
        /**
         * Adding admin page test comment
         */
        global $wpdb;
        $table_name = $wpdb->prefix . 'memberships';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE wp_memberships (
            id INT AUTO_INCREMENT PRIMARY KEY,
            photo_url varchar(255) NOT NULL,
            name VARCHAR(50) NOT NULL,
            type ENUM('Yearly', 'Monthly') NOT NULL,
            price VARCHAR(50) NOT NULL,
            billing_type ENUM('No Recurring', 'Recurring') NOT NULL,
            dashboard_access VARCHAR(255) NOT NULL,
            access_hours VARCHAR(50) NOT NULL,
            benefits TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

new wygymactication();
