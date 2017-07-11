<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;

use DDelivery\Storage\SettingStorageInterface;

class WPSettingsStorage implements SettingStorageInterface {
	public function createStorage() {
		return true;
	}

	public function save( $settings ) {
		$group = DDeliveryShipping::getOptionsGroup();
		$opts  = get_option( $group );
		if ( ! isset( $opts['storage'] ) ) {
			$opts['storage'] = array();
		}
		$opts['storage'] = $settings;

		return update_option( $group,
		                      $opts );
	}

	public function getParam( $paramName ) {
		$group = DDeliveryShipping::getOptionsGroup();
		$opts  = get_option( $group );
		if ( empty( $opts['storage'] ) ) {
			$opts['storage'] = array();
		}

		return ( isset( $opts['storage'][ $paramName ] ) )
			? $opts['storage'][ $paramName ]
			: null;
	}

	public function drop() {
		return true;
	}
}