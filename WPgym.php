<?php
/*
 * Plugin Name:       WYgym
 * Description:       WPgym is a powerful WordPress plugin for gym management, allowing you to manage members, staff, schedules, and payments with ease. Perfect for fitness centers and personal trainers!
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            AdeelDEv
 * Author URI:        iamadeel.r@gmail.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wygym
 * Domain Path: /languages

------------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.
 */

// --------------------------------------------------------------------------
// ---------------------- Security: Abort if this file is called directly
// -----------------------------------------------------------------------
if (!function_exists('add_action')) {
    echo esc_attr_e('Your are not allowed to used this plugin', wygym_domain);
    exit;
}
// ----------------------- || --------------------
if (!defined('ABSPATH')) {
    die();
}

if (!defined('wygym_path')) {
    /**
     * Defines the Base Path.
     *
     * @since   Unknown
     *
     * @var string wygym_path The base Path.
     */
    define('wygym_path', trailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('wygym_url')) {
    /**
     * Defines the Base URL.
     *
     * @since   Unknown
     *
     * @var string wygym_path The base url.
     */
    define('wygym_url', trailingslashit(plugin_dir_url(__FILE__)));
}

if (!defined('wygym_domain')) {
    /**
     * Defines the PLugin Domain.
     *
     * @since   Unknown
     *
     * @var string wygym_domain The base url.
     */
    define('wygym_domain', 'wygym');
}

if (!defined('wygym__mainfile')) {
    /**
     * Defines the  Plugin Main File.
     *
     * @since   Unknown
     *
     * @var string wygym__mainfile For main file path
     */
    define('wygym__mainfile', __FILE__);
}
load_plugin_textdomain(wygym_domain, false, dirname(plugin_basename(__FILE__)) . '/languages/');

// Load Composer dependencies
require_once wygym_path . '/vendor/autoload.php';

// Including Files
require_once wygym_path . '/inc/class-activation.php';
require_once wygym_path . '/inc/class-scripts.php';
require_once wygym_path . '/inc/class-peachpayment.php';
require_once wygym_path . '/inc/class-acessControl.php';
require_once wygym_path . '/inc/class-setting-fields.php';
require_once wygym_path . '/admin/admin-pages.php';
require_once wygym_path . '/membership/membership-manager.php';
require_once wygym_path . '/users/class-user-registration.php';