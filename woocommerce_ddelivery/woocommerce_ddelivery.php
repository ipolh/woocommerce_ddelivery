<?php
/**
 * Plugin Name: WooCommerce DDelivery
 * Plugin URI: https://ipolh.com
 * Description: DDelivery integration for checkout of WooCommerce
 * Version: 1.0.0
 * Author: IPOL <support@ipolh.com>
 * Author URI: https://ipolh.com
 * Requires at least: 4.7.5
 *
 * @author dmz9 <dmz9@yandex.ru>
 *
 * supports woocommerce 3.0.8
 */
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WooCommerce' ) ) {
	exit; // Exit if accessed directly.
}

include_once "vendor/autoload.php";
/**
 * стараемся не мусорить переменными в глобале поэтому статик
 */
\WPWooCommerceDDelivery\Core::init();