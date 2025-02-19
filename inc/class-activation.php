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
    }
}

new wygymactication();
