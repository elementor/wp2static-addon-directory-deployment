<?php

/**
 * Plugin Name:       WP2Static Add-on: Deploy to local directory
 * Plugin URI:        https://wp2static.com
 * Description:       Local directory deployment add-on for WP2Static.
 * Version:           1.0.1-dev
 * Requires PHP:      7.3
 * Author:            Adam Twardoch
 * Author URI:        https://twardoch.github.io/
 * License:           Unlicense
 * License URI:       http://unlicense.org
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WP2STATIC_DIRECTORY_DEPLOYMENT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP2STATIC_DIRECTORY_DEPLOYMENT_VERSION', '1.0.1-dev' );

if ( file_exists( WP2STATIC_DIRECTORY_DEPLOYMENT_PATH . 'vendor/autoload.php' ) ) {
    require_once WP2STATIC_DIRECTORY_DEPLOYMENT_PATH . 'vendor/autoload.php';
}

function run_wp2static_addon_copy() : void {
    $controller = new WP2StaticCopy\Controller();
    $controller->run();
}

register_activation_hook(
    __FILE__,
    [ 'WP2StaticCopy\Controller', 'activate' ]
);

register_deactivation_hook(
    __FILE__,
    [ 'WP2StaticCopy\Controller', 'deactivate' ]
);

run_wp2static_addon_copy();

