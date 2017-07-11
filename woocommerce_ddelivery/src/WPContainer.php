<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;

use DDelivery\Adapter\Container;
use DDelivery\Storage\TokenStorageInterface;

class WPContainer extends Container {
	/**
	 * Получить хранилище токенов
	 *
	 * @return TokenStorageInterface
	 */
	public function getTokenStorage() {
		if ( ! isset( $this->shared['token'] ) ) {
			$this->shared['token'] = new WPTokenStorage();
		}

		return $this->shared['token'];
	}

	public function getSettingStorage() {
		if ( ! isset( $this->shared['settings'] ) ) {
			$this->shared['settings'] = new WPSettingsStorage();
		}

		return $this->shared['settings'];
	}

	public function getLogStorage() {
		if ( ! isset( $this->shared['log'] ) ) {
			$this->shared['log'] = new WPLogStorage();
		}

		return $this->shared['log'];
	}
}