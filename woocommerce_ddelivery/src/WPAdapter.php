<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;
class WPAdapter extends \DDelivery\Adapter\Adapter {


	/**
	 *
	 * Получить апи ключ
	 *
	 * @return string
	 * @throws \DDelivery\DDeliveryException
	 */
	public function getApiKey() {
		$group                       = DDeliveryShipping::getOptionsGroup();
		$woocommerceShippingSettings = get_option( $group );

		return (string) $woocommerceShippingSettings[DDeliveryShipping::API_KEY_FIELD];
	}

	/**
	 *
	 * При синхронизации статусов заказов необходимо
	 * [
	 *      'id' => 'status',
	 *      'id2' => 'status2',
	 * ]
	 *
	 * @param array $orders
	 *
	 * @return bool
	 */
	public function changeStatus( array $orders ) {
		// TODO: Implement changeStatus() method.
		return true;
	}

	public function getCmsName() {
		return "WordPress";
	}

	public function getCmsVersion() {
		return (string) get_bloginfo( 'version' );
	}

	/**
	 * Получить  заказ по id
	 * ['city' => город назначения, 'payment' => тип оплаты, 'status' => статус заказа,
	 * 'sum' => сумма заказа, 'delivery' => стоимость доставки]
	 *
	 * город назначения, тип оплаты, сумма заказа, стоимость доставки
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function getOrder( $id ) {
		$order   = Helper::getOrder( $id );
		$payment = Helper::stringToNumber( $order->get_payment_method() );

		return array(
			'city'     => $order->get_billing_city(),
			'payment'  => $payment,
			'status'   => str_replace( 'wc-',
			                           '',
			                           $order->get_status() ),
			'sum'      => $order->get_total(),
			'delivery' => $order->get_shipping_total(),
		);
	}

	/**
	 * Получить список заказов за период
	 * ['city' => город назначения, 'payment' => тип оплаты, 'status' => 'статус заказа'
	 * 'sum' => сумма заказа, 'delivery' => стоимость доставки]
	 *
	 * город назначения, тип оплаты, сумма заказа, стоимость доставки
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return array
	 */
	public function getOrders( $from, $to ) {
		$startDate = \DateTime::createFromFormat( "Y.m.d",
		                                          $from );
		$endDate   = \DateTime::createFromFormat( "Y.m.d",
		                                          $to );
		$orders    = get_posts( array(
			                        'numberposts' => - 1,
			                        'post_type'   => wc_get_order_types(),
			                        'post_status' => array_keys( wc_get_order_statuses() ),
			                        'date_query'  => array(
				                        array(
					                        'after'     => $startDate->format( 'y-m-d' ),
					                        'before'    => $endDate->format( 'y-m-d' ),
					                        'inclusive' => true,
				                        ),
			                        )
		                        ) );
		$data      = array();
		foreach ( $orders as $customer_order ) {
			$data[] = self::getOrder( $customer_order->ID );
		}

		return $data;
	}

	/**
	 *
	 * Получить скидку
	 *
	 * @return float
	 */
	public function getDiscount() {
		return 0;
	}

	/**
	 *
	 * получить продукты из корзины
	 *
	 * @return array
	 */
	public function getProductCart() {

		return isset($this->params['form'])?$this->params['form']:array();
	}

	/**
	 * Получить массив с соответствием статусов DDelivery
	 * @return array
	 */
	public function getCmsOrderStatusList() {
		$statuses = wc_get_order_statuses();
		$res      = array();
		foreach ( $statuses as $id => $statusname ) {
			$key         = str_replace( 'wc-',
			                            '',
			                            $id );
			$res[ $key ] = $statusname;
		}

		return $res;
	}

	public function getEnterPoint() {
		return get_bloginfo( 'url' ) . Router::buildRestUrl( 'ddeliveryEndpoint' );
	}

	/**
	 * Получить массив со способами оплаты
	 * @return array
	 */
	public function getCmsPaymentList() {
		$all = WC()->payment_gateways->get_available_payment_gateways();;
		$res = array();
		foreach ( $all as $id => $item ) {
			$key         = Helper::stringToNumber( $id );
			$res[ $key ] = $item->method_title;
		}

		return $res;
	}

	/***
	 *
	 * В этом участке средствами Cms проверить права доступа текущего пользователя,
	 * это важно так как на базе этого  метода происходит вход
	 * на серверние настройки
	 *
	 * @return bool
	 */
	public function isAdmin() {
		return true;
	}

	public function getRealUrl() {
		return get_bloginfo( 'url' );
	}

	public function getSdkServer() {
		return 'https://sdk.ddelivery.ru/api/v1/';
	}
}