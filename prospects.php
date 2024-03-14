<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Prospects
Description: Prospects module for Perfex CRM.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('Prospects', 'prospects');

hooks()->add_action('admin_init', 'prospects_init_menu_items');

register_activation_hook(Prospects, 'prospects_activation_hook');

function prospects_activation_hook(){
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
    require_once(__DIR__ . '/vendor/autoload.php');
}

register_language_files(Prospects, [Prospects]);


function prospects_init_menu_items()
{
    if (is_admin()){
        $CI = &get_instance();

        $CI->app_menu->add_sidebar_menu_item('prospects', [
            'name' => _l('prospects'),
            'href' => admin_url('prospects'),
            'icon' => 'fa fa-file',
            'position' => 5,
        ]);
    }
}