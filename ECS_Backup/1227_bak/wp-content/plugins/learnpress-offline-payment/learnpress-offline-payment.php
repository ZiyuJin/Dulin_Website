<?php
/*
Plugin Name: LearnPress Offline Payment
Plugin URI: http://thimpress.com/learnpress
Description: Make a payment with cash
Author: ThimPress
Version: 2.0
Author URI: http://thimpress.com
Tags: learnpress
Text Domain: learnpress-offline-payment
Domain Path: /languages/
Requires at least: 3.8
Tested up to: 4.6.1
Last updated: 2016-03-17 3:29pm GMT
*/

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Addon path
define( 'LP_ADDON_OFFLINE_PAYMENT_FILE', __FILE__ );
define( 'LP_ADDON_OFFLINE_PAYMENT_PATH', dirname( __FILE__ ) );

/**
 * Class LP_Addon_Offline_Payment
 */
class LP_Addon_Offline_Payment {
	/**
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * LP_Addon_Offline_Payment constructor.
	 */
	function __construct() {
		add_action( 'learn_press_ready', array( $this, 'load_gateway') );
		add_action( 'init', array( $this, 'load_text_domain' ) );
	}

	function load_gateway(){
		include_once "incs/class-lp-gateway-offline-payment.php";
		add_filter( 'learn_press_payment_method', array( 'LP_Gateway_Offline_Payment', 'add_payment' ) );
	}

	/**
	 * load text domain
	 */
	static function load_text_domain(){
		if( function_exists('learn_press_load_plugin_text_domain')){ learn_press_load_plugin_text_domain(LP_ADDON_OFFLINE_PAYMENT_PATH, true ); }
	}

	/**
	 * @return LP_Addon_Offline_Payment|null
	 */
	static function instance() {
		if ( !self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

LP_Addon_Offline_Payment::instance();