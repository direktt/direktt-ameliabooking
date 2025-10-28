<?php

/**
 * Plugin Name: Direktt Amelia Booking Integration
 * Description: Direktt Amelia Booking Integration Direktt Plugin
 * Version: 1.0.0
 * Author: Direktt
 * Author URI: https://direktt.com/
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$direktt_ameliabooking_plugin_version = "1.0.0";
$direktt_ameliabooking_github_update_cache_allowed = true;

require_once plugin_dir_path( __FILE__ ) . 'direktt-github-updater/class-direktt-github-updater.php';

$direktt_ameliabooking_plugin_github_updater  = new Direktt_Github_Updater( 
    $direktt_ameliabooking_plugin_version, 
    'direktt-ameliabooking/direktt-ameliabooking.php',
    'https://raw.githubusercontent.com/direktt/direktt-ameliabooking/master/info.json',
    'direktt_ameliabooking_github_updater',
    $direktt_ameliabooking_github_update_cache_allowed );

add_filter( 'plugins_api', array( $direktt_ameliabooking_plugin_github_updater, 'github_info' ), 20, 3 );
add_filter( 'site_transient_update_plugins', array( $direktt_ameliabooking_plugin_github_updater, 'github_update' ));
add_filter( 'upgrader_process_complete', array( $direktt_ameliabooking_plugin_github_updater, 'purge'), 10, 2 );

add_action( 'plugins_loaded', 'direktt_ameliabooking_activation_check', -20 );

// Add settings page
add_action( 'direktt_setup_settings_pages', 'direktt_ameliabooking_setup_settings_page' );

// Enqueue script for hiding last name and email fields
add_action( 'wp_enqueue_scripts', 'direktt_ameliabooking_enqueue_fe_scripts' );

function direktt_ameliabooking_activation_check() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $required_plugin = 'direktt/direktt.php';
    $is_required_active = is_plugin_active( $required_plugin )
        || ( is_multisite() && is_plugin_active_for_network( $required_plugin ) );

    if ( ! $is_required_active ) {
        // Deactivate this plugin
        deactivate_plugins( plugin_basename( __FILE__ ) );

        // Prevent the “Plugin activated.” notice
        if ( isset( $_GET['activate'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Justification: not a form processing, just removing a query var.
            unset( $_GET['activate'] );
        }

        // Show an error notice for this request
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error is-dismissible"><p>'
                . esc_html__( 'Direktt Amelia Booking Integration activation failed: The Direktt WordPress Plugin must be active first.', 'direktt-ameliabooking' )
                . '</p></div>';
        });

        // Optionally also show the inline row message in the plugins list
        add_action(
            'after_plugin_row_direktt-ameliabooking/direktt-ameliabooking.php',
            function () {
                echo '<tr class="plugin-update-tr"><td colspan="3" style="box-shadow:none;">'
                    . '<div style="color:#b32d2e;font-weight:bold;">'
                    . esc_html__( 'Direktt Amelia Booking Integration requires the Direktt WordPress Plugin to be active. Please activate it first.', 'direktt-ameliabooking' )
                    . '</div></td></tr>';
            },
            10,
            0
        );
    }
}

function direktt_ameliabooking_enqueue_fe_scripts() {
    global $post;

    if ( ! isset( $post ) ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        return;
    }

    $user = wp_get_current_user();

    if ( ! Direktt_User::is_wp_user_direktt_role( $user ) ) {
        return;
    }

    if ( has_shortcode( $post->post_content, 'ameliabooking' ) || has_shortcode( $post->post_content, 'ameliastepbooking' ) || has_shortcode( $post->post_content, 'ameliacatalogbooking' ) ) {
        wp_enqueue_script(
            'direktt-ameliabooking-script',
            plugin_dir_url( __FILE__ ) . 'assets/js/direktt-ameliabooking.js',
            [ 'jquery' ],
            filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/direktt-ameliabooking.js' ),
            true
        );

        $direktt_user = Direktt_User::get_direktt_user_by_wp_user( $user );
        $display_name = $direktt_user['direktt_display_name'];
        
        wp_localize_script( 'direktt-ameliabooking-script',
            'direkttAmeliaBooking',
            array(
                'displayName' => $display_name,
                'label'       => esc_html__( 'Direktt Display Name:', 'direktt-ameliabooking' ),
            )
        );

        if ( has_shortcode( $post->post_content, 'ameliabooking' ) ) {
            wp_localize_script( 'direktt-ameliabooking-script',
                'direkttAM',
                array(
                    'type' => 1,
                )
            );
        } elseif ( has_shortcode( $post->post_content, 'ameliastepbooking' ) || has_shortcode( $post->post_content, 'ameliacatalogbooking' ) ) {
            wp_localize_script( 'direktt-ameliabooking-script',
                'direkttAM',
                array(
                    'type' => 2,
                    'user' => $user->user_login
                )
            );
        }
    }
}