<?php
/**
 * Plugin Name: PayDirectFPX
 * Plugin URI: https://www.paydirect.my
 * Description: Enable online payments using FPX online banking. Currently PayDirectFPX service is only available to businesses that reside in Malaysia.
 * Version: 1.0.2
 * Author: PayDirectFPX
 * Author URI: https://www.linkedin.com/in/shahrul1995/
 * WC requires at least: 2.6.0
 * WC tested up to: 6.6
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# Include PayDirectFPX Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'paydirectfpx_init', 0 );

function paydirectfpx_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/paydirectfpx.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_paydirectfpx_to_woocommerce' );
	function add_paydirectfpx_to_woocommerce( $methods ) {
		$methods[] = 'paydirectfpx';

		return $methods;
	}
}

# Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'paydirectfpx_links' );

function paydirectfpx_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=paydirectfpx' ) . '">' . __( 'Settings', 'paydirectfpx' ) . '</a>',
	);

	# Merge our new link with the default ones
	return array_merge( $plugin_links, $links );
}

add_action( 'init', 'paydirectfpx_check_response', 15 );

function paydirectfpx_check_response() {
	# If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/paydirectfpx.php' );

	$paydirectfpx = new paydirectfpx();
	$paydirectfpx->check_paydirectfpx_response();
}

function paydirectfpx_hash_error_msg( $content ) {
	return '<div class="woocommerce-error">Invalid data entered. Please contact your merchant for more info.</div>' . $content;
}

function paydirectfpx_payment_declined_msg( $content ) {
	return '<div class="woocommerce-error">Fail transaction. Please check with your bank system.</div>' . $content;
}

function paydirectfpx_success_msg( $content ) {
	return '<div class="woocommerce-info">The payment was successful. Thank you.</div>' . $content;
}
