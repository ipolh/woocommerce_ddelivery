<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;
class Helper {
	/**
	 * @return bool
	 */
	public static function woocommerceActive() {
		$active_plugins = get_option( 'active_plugins',
		                              array() );
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins',
			                                            array() );
			$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
			$active_plugins          = array_merge( $active_plugins,
			                                        $active_sitewide_plugins );
		}

		return in_array( 'woocommerce/woocommerce.php',
		                 $active_plugins );
	}

	/**
	 * @param array $adapterParams
	 *
	 * @return WPContainer
	 */
	public static function createContainer( array $adapterParams = array() ) {
		$adapter = new WPAdapter( $adapterParams );

		return new WPContainer( [ 'adapter' => $adapter ] );
	}

	public static function getCurrentCartProducts() {

		$cart = self::getCart();

		$products = array();

		foreach ( $cart as $item ) {
			$data = $item['data'];
			if ( $data instanceof \WC_Product_Simple ) {
				$products[ $item['product_id'] ] = [
					'id'       => $item['product_id'],
					'sku'      => ! empty( $data->get_sku() ) ? $data->get_sku() : 'sku-' . $item['product_id'],
					'name'     => $data->get_name(),
					'price'    => $data->get_price(),
					'quantity' => $item['quantity'],
					'width'    => ! empty( $data->get_width() )  ? $data->get_width()  : 10,
					'height'   => ! empty( $data->get_height() ) ? $data->get_height() : 10,
					'length'   => ! empty( $data->get_length() ) ? $data->get_length() : 10,
					'weight'   => ! empty( $data->get_weight() ) ? $data->get_weight() : 10
				];
			}
		}

		return $products;
	}

	public static function getCart() {
		$wc = WC();

		return $wc->cart->get_cart();
	}

	public static function getAssetUrl( $assetName ) {
		$pluginFilename = self::getPluginFilename();

		return plugin_dir_url( $pluginFilename ) . "assets/$assetName";
	}

	public static function getPluginFilename() {
		return rtrim( dirname( __DIR__ ),
		              '/\\' ) . DIRECTORY_SEPARATOR . "woocommerce_ddelivery.php";
	}

	public static function sessionStarted() {
		return ( session_status() != PHP_SESSION_NONE );
	}

	/**
	 * @param $orderId
	 *
	 * @return \WC_Order
	 * @throws \Exception
	 */
	public static function getOrder( $orderId ) {
		$orderData = wc_get_order( $orderId );
		if ( ! ( $orderData instanceof \WC_Order ) ) {
			throw new \Exception( 'Order not found' );
		}

		return $orderData;
	}

	public static function stringToNumber( $string ) {
		$alphabet = array(
			'a' => '01',
			'b' => '02',
			'c' => '03',
			'd' => '04',
			'e' => '05',
			'f' => '06',
			'g' => '07',
			'h' => '08',
			'i' => '09',
			'j' => '10',
			'k' => '11',
			'l' => '12',
			'm' => '13',
			'n' => '14',
			'o' => '15',
			'p' => '16',
			'q' => '17',
			'r' => '18',
			's' => '19',
			't' => '20',
			'u' => '21',
			'v' => '22',
			'w' => '23',
			'x' => '24',
			'y' => '25',
			'z' => '26'
		);
		$string   = trim( (string) $string );
		$string   = strtolower( $string );
		$len      = strlen( $string );
		$rez      = '';
		for ( $i = 0; $i < $len; $i ++ ) {
			if ( isset( $alphabet[ $string[ $i ] ] ) ) {
				$rez .= $alphabet[ $string[ $i ] ];
			}
		}
		$rez = '1' . $rez;
		if ( strlen( $rez ) > 32 ) {
			$rez = substr( $rez,
			               0,
			               32 );
		}

		return (int) $rez;
	}

	public static function addUploadError(
		$error,
		$orderId
	) {
		$fname = self::getLogFilePath();
		$data  = @file_get_contents( $fname );
		if ( empty( $data ) ) {
			$errors = array();
		} else {
			$errors = unserialize( $data );
		}
		$errors[ $orderId ] = $error;
		@file_put_contents( $fname,
		                    serialize( $errors ) );
	}

	public static function getLogFilePath() {
		return rtrim( dirname( dirname( __FILE__ ) ),
		              '/\\' ) . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'last-error.log';
	}

	public static function showUploadErrorsIfAny() {
		$fname = self::getLogFilePath();
		$data  = @file_get_contents( $fname );
		if ( empty( $data ) ) {
			return;
		}
		$errors = unserialize( $data );
		foreach ( $errors as $order => $error ) {
			$message = "Ошибка при загрузке заказа $order в DDelivery: " . self::translateError( $error );
			echo "<div class=\"notice notice-error is-dismissible\">
	<p><strong>$message</strong></p>
	<button type=\"button\" class=\"notice-dismiss\">
		<span class=\"screen-reader-text\">Скрыть</span>
	</button>
</div>";
		}
	}

	public static function translateError( $error ) {
		$translated = array(
			'validation/order.to_phone._validate_phone' => 'Ошибка валидации: проверьте корректность номера телефона клиента'
		);

		return isset( $translated[ $error ] ) ? $translated[ $error ] : $error;
	}

	public static function dropUploadErrors() {
		$fname = self::getLogFilePath();
		@file_put_contents( $fname,
		                    '' );
	}
}