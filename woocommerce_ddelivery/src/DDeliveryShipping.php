<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;
class DDeliveryShipping extends \WC_Shipping_Method {
	const DELIVERY_ID = 'ddelivery-id';
	const API_KEY_FIELD = 'apikey';
	const IS_DEBUG_FIELD = 'is_debug';
	const IS_DEBUG_DEFAULT_NO = 'no';

	/**
	 * Constructor for your shipping class
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->id                 = self::DELIVERY_ID;
		$this->method_title       = __( 'DDelivery' );
		$this->method_description = __( 'Доставка через DDelivery' );

		$this->enabled = "yes";
		$this->title   = "DDelivery";
		parent::__construct();
		$this->init();
	}

	public static function getOptionsGroup() {
		return 'woocommerce_' . DDeliveryShipping::DELIVERY_ID . '_settings';
	}

	/**
	 *
	 */
	public function init() {
		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_shipping_' . $this->id,
		            array( $this, 'process_admin_options' ) );
	}

	/**
	 * админская часть - поля настройки
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			self::API_KEY_FIELD  => array(
				'title'       => __( 'Апи ключ' ),
				'type'        => 'text',
				'description' => __( 'Ключ можно найти в личном кабинете DDelivery' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			self::IS_DEBUG_FIELD => array(
				'title'       => __( 'Дебаг режим' ),
				'type'        => 'checkbox',
				'description' => __( 'В консоли чекаута показываются отладочные сообщения' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'label'       => __( 'Включить дебаг' )
			),
		);
	}

	public static function checkoutFields( $fields ) {
		return $fields;
	}

	public static function shippingFields( $fields ) {
		return $fields;
	}

	/**
	 * регистрация доставки в woocommerce
	 *
	 * @param $methods
	 *
	 * @return mixed
	 */
	public static function addShippingToFrontend( $methods ) {
		$methods[ self::DELIVERY_ID ] = __CLASS__;

		return $methods;
	}

	/**
	 * то что выводится на страничке в настройках в админке
	 */
	public function admin_options() {
		if ( ! $this->instance_id ) {
			echo '<h2>' . esc_html( $this->get_method_title() ) . '</h2>';
		}
		if ( ! empty( $this->settings[ self::API_KEY_FIELD ] ) ) {

			$container = Helper::createContainer();
			try {
				$ddeliveryEndpoint = get_bloginfo( 'url' ) . Router::buildRestUrl( 'ddeliveryEndpoint' ) .
				                     "?action=admin";
				$token             = $container->getBusiness()
				                               ->renderAdmin( $ddeliveryEndpoint );
				if ( empty( $token ) ) {
					throw new \Exception( 'Пустой токен получен от DDelivery. Проверьте корректность ключа.' );
				}
				echo "<p>Ключ привязан! <a href='$ddeliveryEndpoint' target='_blank'>Перейти в настройки</a></p>";
			} catch ( \Exception $exception ) {
				echo 'Ошибка: ' . $exception->getMessage();
			}
		}
		echo wp_kses_post( wpautop( $this->get_method_description() ) );
		echo $this->get_admin_options_html();
	}

	/**
	 * типичный рассчет цены. читается при пересчете с аяксом. надо хранить в сессии цену рассчета
	 *
	 * @see Controller::actionSavePrice()
	 *
	 * @param array $package
	 */
	public function calculate_shipping( $package = array() ) {
		$price = WC()->session->get( Core::SESSION_FIELD_PRICE,
		                             false );
		if ( ! $price ) {
			$price = 400;
		}
		$this->add_rate( array(
			                 'id'       => $this->id,
			                 'label'    => $this->title,
			                 'cost'     => $price,
			                 'calc_tax' => 'per_order'
		                 ) );
	}
}