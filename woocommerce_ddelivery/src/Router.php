<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;

class Router {
	public static function resolveRoute( $route ) {
		$routes = self::routes();
		if ( ! isset( $routes[ $route ] ) ) {
			throw new \InvalidArgumentException( 'Wrong route' );
		}

		return $routes[ $route ];
	}

	public static function buildRestUrl( $route ) {
		return '/wp-json/' . Core::PLUGIN_ID . '/' . trim( $route );
	}

	/**
	 * 'path'=>array(class,method)
	 * @return array
	 */
	private static function routes() {
		return array(
			'generateSDKToken'  => array(
				Controller::class,
				'actionSDKToken'
			),
			'ddeliveryEndpoint' => array(
				Controller::class,
				'actionDDelivery'
			),
			'getUserCart' => array(
				Controller::class,
				'actionUserCart'
			),
			'savePrice' => array(
				Controller::class,
				'actionSavePrice'
			),
			'saveSDK' => array(
				Controller::class,
				'actionSaveSDK'
			),
			'debug' => array(
				Controller::class,
				'actionDebug'
			)
		);
	}

	public static function registerRoutes() {
		$routes = self::routes();
		foreach ( $routes as $route => $callback ) {
			register_rest_route( Core::PLUGIN_ID,
			                     $route,
			                     array(
				                     'methods'  => 'GET',
				                     'callback' => $callback,
			                     ) );
			register_rest_route( Core::PLUGIN_ID,
			                     $route,
			                     array(
				                     'methods'  => 'POST',
				                     'callback' => $callback,
			                     ) );
		}
	}
}