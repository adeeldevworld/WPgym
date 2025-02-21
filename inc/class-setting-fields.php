<?php

namespace WPGym\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Setting_Fields
{

    public function __construct()
    {
        add_action('carbon_fields_register_fields', [$this, 'register_settings_fields']);
        add_action('after_setup_theme', [$this, 'render_carbon_field']);
    }

    public function render_carbon_field()
    {
        \Carbon_Fields\Carbon_Fields::boot();
    }
    public function register_settings_fields()
    {
        Container::make('theme_options', __('Settings'))
            ->set_page_parent('wpgym-dashboard')
            ->add_tab(__('Genera Setting'), array(
                Field::make('select', 'wpgym_styles', __('Enable Plugin Style Files', 'wpgym'))
                    ->set_options(array(
                        'enable' => __('Enable', 'wpgym'),
                        'disable' => __('Diable', 'wpgym'),
                    )),
            ))
            ->add_tab(__('API Setting'), array(
                Field::make('text', 'wpgym_api_base_url', __('Acess Control API Base URL', 'wpgym')),
                Field::make('text', 'wpgym_api_key', __('Acess Control API Authentication Key', 'wpgym'))
            ))
            ->add_tab(__('Payment'), array(
                Field::make('checkbox', 'wpgym_live_payment_check', __('Enable Live Payment' , 'wpgym')),
                Field::make('text', 'wpgym_payment_api_baselive', __('Peach Payment Live API  Base URL', 'wpgym')),
                Field::make('text', 'wpgym_payment_api_basesanbox', __('Peach Payment Sanbox API  Base URL', 'wpgym')),
            ));
    }
}

new Setting_Fields();
