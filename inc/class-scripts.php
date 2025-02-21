<?php

namespace WPgymAssetLoader;

class WPGym_Asset_Loader
{

    public function __construct()
    {
        // Enqueue assets on the front-end
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);

        // Enqueue assets on the admin-side for WPGym pages
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Enqueue Bootstrap and custom styles/scripts for the front-end.
     */
    public function enqueue_frontend_assets()
    {
        wp_enqueue_style(
            'bootstrap-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            [],
            '5.3.2'
        );
        wp_enqueue_script(
            'bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
            ['jquery'],
            '5.3.2',
            true
        );
    }

    /**
     * Enqueue Bootstrap and custom styles/scripts for the admin-side WPGym pages.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_admin_assets($hook)
    {

        wp_enqueue_style(
            'bootstrap-admin-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            [],
            '5.3.2'
        );

        wp_enqueue_script(
            'bootstrap-admin-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
            ['jquery'],
            '5.3.2',
            true
        );
    }
}

new WPGym_Asset_Loader();
