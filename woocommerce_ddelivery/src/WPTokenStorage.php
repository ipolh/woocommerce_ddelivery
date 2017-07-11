<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;

use DDelivery\Storage\TokenStorageInterface;

class WPTokenStorage implements TokenStorageInterface {
	/**
	 * @var string уникальный идентификатор группы кеша
	 */
	private $wpCacheGroup = 'wc_dd_group';

	public function createStorage() {
		return function_exists('wp_cache_add');
	}

	public function deleteExpired() {
		return true;
	}

	public function checkToken( $token ) {
		$found=false;
		wp_cache_get($token,$this->wpCacheGroup,true,$found);
		return $found;
	}

	public function createToken( $token, $expired ) {
		return wp_cache_set( $token, $token, $this->wpCacheGroup, $expired );
	}

	/**
	 * Выбрать все записи
	 *
	 * @return array
	 */
	public function getAll() {
		// can not implement
		return array();
	}

	public function drop() {
		return wp_cache_flush();
	}
}