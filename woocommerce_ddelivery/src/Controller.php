<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;

class Controller {
	public static function actionDDelivery() {

		$container = Helper::createContainer();
		$container->getUi()
		          ->render( $_REQUEST );
		die();
	}

	public static function actionSDKToken() {

		$products = ( isset( $_POST ) && isset( $_POST['products'] ) )
			? $_POST['products']
			: array();
		$discount = ( isset( $_POST ) && isset( $_POST['discount'] ) )
			? $_POST['discount']
			: array();

		$container       = Helper::createContainer( array(
			                                            'form'     => $products,
			                                            'discount' => (float) $discount
		                                            ) );
		$business        = $container->getBusiness();
		$cartAndDiscount = $container->getAdapter()
		                             ->getCartAndDiscount();
		$token           = $business->renderModuleToken( $cartAndDiscount );

		return array(
			'url' => $container->getAdapter()
			                   ->getSdkServer() . 'delivery/' . $token . '/index.json'
		);
	}

	public static function actionUserCart() {
		return Helper::getCurrentCartProducts();
	}

	/**
	 * сохраняем sdk-id выданый дделивери. это ид черновика.
	 *
	 * @return array
	 */
	public static function actionSaveSDK() {
		$sdkId = $_POST['id'];
		if ( empty( $sdkId ) ) {
			return array( 'status' => 'fail' );
		}

		$session           = WC()->session;
		$field             = Core::SESSION_FIELD_SDK_ID;
		$session->{$field} = $sdkId;
		$session->save_data();

		return array( 'status' => 'ok' );
	}

	/**
	 * сохраняем выбраную йузером цену доставки в сессии
	 *
	 * @return array
	 */
	public static function actionSavePrice() {
		$data = $_POST['data'];
		if ( empty( $data ) ) {
			return array( 'status' => 'fail' );
		}

		$price = $data['price'];

		$wc      = WC();
		$session = $wc->session;
		$field   = Core::SESSION_FIELD_PRICE;
		unset( $session->{$field} );
		$session->{$field} = $price;
		// hack to remove session key for ddelivery shipping package to force it recalculate instead of getting cached
		// @stolen in class-wc-shipping.php@calculate_shipping_for_package()
		// emulating case when recalculating is forced
		$total = $session->get( 'shipping_method_counts', array());
		foreach ( $total as $i => $whatever ) {
			$session->set( 'shipping_for_package_' . $i,
			               'fuck_you_woocommerce' );
		}

		$session->save_data();

		return array( 'status' => 'ok' );
	}

	public static function actionDebug() {
		$order = Helper::getOrder( $_POST['orderId'] );

		$order->save();

		return die( print_r( $order,
		                     1 ) );
	}

	/**
	 * backend only. sending order to ddelivery is here
	 *
	 * @param $orderId
	 *
	 * @return int
	 */
	public static function actionOrderUpdate( $orderId ) {
		$logger = new WPLogStorage();
		$logger->saveLog( " " );

		$orderId = (int) $orderId;
		if ( empty( $orderId ) ) {
			$logger->saveLog( "Order create stopped: empty orderId" );

			return $orderId;
		}

		$logger->saveLog( 'Order update hook ' . $orderId );

		$container = Helper::createContainer();
		$business  = $container->getBusiness();

		try {
			$order = Helper::getOrder( $orderId );
			$sdkId = $order->get_meta( Core::SESSION_FIELD_SDK_ID );
			if ( empty( $sdkId ) ) {
				$logger->saveLog( "Empty sdk id, stopping" );

				return $orderId;
			}
			$logger->saveLog( "Order $orderId has sdk id $sdkId" );
			$ddeliveryId = $order->get_meta( Core::ORDER_FIELD_DDELIVERY_ID,
			                                 false );
			if ( $ddeliveryId !== false && ! empty( $ddeliveryId ) ) {
				$logger->saveLog( "Order already uploaded, ddelivery id: " . print_r( $ddeliveryId,
				                                                                      1 ) );

				return $orderId;
			}
		} catch ( \Exception $exception ) {
			$logger->saveLog( "Exception in actionOrderUpdate: {$exception->getMessage()}" );

			return $orderId;
		}

		$logger->saveLog( "Sending order to DDelivery" );
		$toSend = array(
			$sdkId,
			$orderId,
			Helper::stringToNumber( $order->get_payment_method() ),
			$order->get_status(),
			$order->get_formatted_billing_full_name(),
			$order->get_billing_phone(),
			$order->get_billing_email(),
			$order->get_total(),
			''
		);
		try {
			$result = $business->onCmsChangeStatus( $toSend[0],
			                                        $toSend[1],
			                                        $toSend[2],
			                                        $toSend[3],
			                                        $toSend[4],
			                                        $toSend[5],
			                                        $toSend[6],
			                                        $toSend[7],
			                                        $toSend[8] );
		} catch ( \Exception $exception ) {
			$logger->saveLog( "Exception in onCmsChangeStatus: {$exception->getMessage()}" );
			$logger->saveLog( "Debug data: " . print_r( $toSend,
			                                            1 ) );

			Helper::addUploadError( $exception->getMessage(),
			                        $orderId );

			return $orderId;
		}
		if ( $result === 0 ) {
			$logger->saveLog( "Status '{$order->get_status()}' for send order doesnt match, stopping" );
		} else if ( $result > 0 ) {
			$logger->saveLog( "DDelivery order id : " . print_r( $result,
			                                                     1 ) );
			$order->add_meta_data( Core::ORDER_FIELD_DDELIVERY_ID,
			                       (int) $result,
			                       true );
			$order->save();

			Helper::dropUploadErrors();
		} else {
			$logger->saveLog( "Unexpected result: " . print_r( $result,
			                                                   1 ) );
		}

		return $orderId;
	}

	/**
	 * backend only. binding order to ddelivery
	 *
	 * @param $orderId
	 *
	 * @return int
	 */
	public static function actionOrderCreate( $orderId ) {
		$logger = new WPLogStorage();
		$logger->saveLog( " " );

		$orderId = (int) $orderId;
		if ( empty( $orderId ) ) {
			$logger->saveLog( "Order create stopped: empty orderId" );

			return $orderId;
		}

		$logger->saveLog( 'Order create hook ' . $orderId );

		$session = WC()->session;

		$container = Helper::createContainer();
		$business  = $container->getBusiness();

		try {
			$order = Helper::getOrder( $orderId );
			$field = Core::SESSION_FIELD_SDK_ID;
			$sdkId = $session->get( $field );
			if ( empty( $sdkId ) ) {
				$logger->saveLog( "SDK ID not found" );

				return $orderId;
			} else {
				unset( $session->{$field} );
				$session->save_data();
			}
			$order->add_meta_data( Core::SESSION_FIELD_SDK_ID,
			                       $sdkId,
			                       true );
			$order->save();
			$logger->saveLog( "Order $orderId has sdk id $sdkId" );
		} catch ( \Exception $exception ) {
			$logger->saveLog( "Exception in actionOrderCreate: {$exception->getMessage()}" );

			return $orderId;
		}

		$logger->saveLog( "Sending order to DDelivery" );
		$result = $business->onCmsOrderFinish( $sdkId,
		                                       $orderId,
		                                       Helper::stringToNumber( $order->get_payment_method() ),
		                                       $order->get_status(),
		                                       $order->get_formatted_billing_full_name(),
		                                       $order->get_billing_phone(),
		                                       $order->get_billing_email(),
		                                       $order->get_total(),
		                                       '' );
		if ( $result !== false ) {
			$logger->saveLog( "DDelivery data: " . print_r( $result,
			                                                1 ) );
		} else {
			$logger->saveLog( "DDelivery onCmsOrderFinish returned empty answer" );
		}

		return $orderId;
	}
}