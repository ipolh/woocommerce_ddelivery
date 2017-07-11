<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 8:05 PM
 */

namespace DDelivery\Server;


use DDelivery\Adapter\Adapter;

class Api
{

    public $apiKey;

    public $apiServer;

    /**
     * @var CurlProvider
     */
    public $curlProvider;

    public function __construct($apiKey, $apiServer, $curlProvider)
    {
        $this->apiKey = $apiKey;
        $this->apiServer = $apiServer;
        $this->curlProvider = $curlProvider;
    }

    /**
     * @param $token
     * @return array
     */
    public function checkHandshakeToken($token)
    {
        $params = array(
            'token' => $token
        );
        return (array)$this->curlProvider->processGet($this->getUrl('passport', 'handshake'), $params);
    }


    /**
     *
     * Отправляем заказ на сервер DDelivery.ru
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment_variant - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $payment_price - наложенный платеж [0,1](нет, да)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     *
     * @param string $comment
     * @param float|int $payment_price_value
     * @return array
     */
    public function sendOrder(
        $sdkId, $cmsId, $payment_variant, $status,
        $payment_price, $to_name, $to_phone, $to_email,
        $comment = '', $payment_price_value = 0
    ) {
        $params = array(
            'id' => $sdkId,
            'shop_refnum' => $cmsId,
            'payment_variant' => $payment_variant,
            'local_status' => $status,
            'payment_price' => $payment_price,
            'payment_price_value' => $payment_price_value,
            Adapter::USER_FIELD_NAME => $to_name,
            Adapter::USER_FIELD_PHONE => $to_phone,
            Adapter::USER_FIELD_EMAIL => $to_email,
            Adapter::USER_FIELD_COMMENT => $comment
        );

        return (array)$this->curlProvider->processPost($this->getUrl('order', 'send'), $params);
    }


    /**
     *
     * Редактируем заказ на сервере DDelivery.ru(если заявка отправлена)
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment_variant - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $payment_price - наложенный платеж [0,1](нет, да)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     *
     * @param string $comment
     * @param float|int $payment_price_value
     * @return array
     */
    public function changeOrder(
        $sdkId, $cmsId, $payment_variant, $status,
        $payment_price, $to_name, $to_phone, $to_email,
        $comment = '', $payment_price_value = 0
    ) {
        $params = array(
            'id' => $sdkId,
            'shop_refnum' => $cmsId,
            'payment_variant' => $payment_variant,
            'local_status' => $status,
            'payment_price' => $payment_price,
            'payment_price_value' => $payment_price_value,
            Adapter::USER_FIELD_NAME => $to_name,
            Adapter::USER_FIELD_PHONE => $to_phone,
            Adapter::USER_FIELD_EMAIL => $to_email,
            Adapter::USER_FIELD_COMMENT => $comment
        );

        return (array)$this->curlProvider->processPost($this->getUrl('order', 'change'), $params);
    }


    /**
     * Получить информацию о заказе
     *
     * @param $sdkId
     * @return array
     */
    public function viewOrder($sdkId)
    {
        $params = array(
            'id' => $sdkId
        );
        return (array)$this->curlProvider->processPost($this->getUrl('order', 'view'), $params);
    }


    /**
     *
     * Редактируем заказ на сервере сдк
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment_variant - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     *
     * @param $payment_price
     * @param string $comment
     * @return array
     */
    public function editOrder(
        $sdkId, $cmsId, $payment_variant, $status,
        $to_name, $to_phone, $to_email,
        $payment_price, $comment = ''
    ) {
        $params = array(
            'id' => $sdkId,
            'shop_refnum' => $cmsId,
            'payment_variant' => $payment_variant,
            'local_status' => $status,
            Adapter::USER_FIELD_NAME => $to_name,
            Adapter::USER_FIELD_PHONE => $to_phone,
            Adapter::USER_FIELD_EMAIL => $to_email,
            Adapter::USER_FIELD_COMMENT => $comment
        );
        return (array)$this->curlProvider->processPost($this->getUrl('order', 'edit'), $params);
    }

    /**
     *
     * Получить доступ к ПАМ
     *
     * @param $token
     * @param string $realUrl
     * @return array
     */
    public function accessAdmin($token, $realUrl = '')
    {
        $params = array(
            'token' => $token,
            'real_url' => $realUrl
        );
        return (array)$this->curlProvider->processGet($this->getUrl('passport', 'auth'), $params);
    }

    public function pushCart(array $cart)
    {
        return (array)$this->curlProvider->processJson($this->getUrl('passport', 'shop'), $cart);
    }

    public function pushOrderEditCart(array $cart, $id)
    {
        $cart['id'] = $id;
        return (array)$this->curlProvider->processPost($this->getUrl('passport', 'order'), $cart);
    }


    public function getUrl($controller, $method)
    {
        return $this->apiServer . $controller . '/' . $this->apiKey . '/' . $method . '.json';
    }
} 