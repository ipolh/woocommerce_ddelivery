<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 8:00 PM
 */

namespace DDelivery\Business;


use DDelivery\Adapter\Adapter;
use DDelivery\DDeliveryException;
use DDelivery\Server\Api;
use DDelivery\Storage\LogStorageInterface;
use DDelivery\Storage\SettingStorageInterface;
use DDelivery\Storage\TokenStorageInterface;
use DDelivery\Utils;

class Business
{


    /**
     * Время действия токена в минутах
     */
    const TOKEN_LIFE_TIME = 1;

    /**
     * @var Api
     */
    private $api;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SettingStorageInterface
     */
    private $settingStorage;


    /**
     * @var LogStorageInterface
     */
    private $log;


    public function __construct(
        Api $api, TokenStorageInterface $tokenStorage,
        SettingStorageInterface $settingStorage, LogStorageInterface $log
    ) {
        $this->api = $api;
        $this->tokenStorage = $tokenStorage;
        $this->settingStorage = $settingStorage;
        $this->log = $log;
    }


    /**
     * Создать хранилища необходимые для работы модуля
     */
    public function initStorage()
    {
        $tokenStorage = $this->tokenStorage->createStorage();
        $settingStorage = $this->settingStorage->createStorage();
        $log = $this->log->createStorage();
        if ($tokenStorage && $settingStorage && $log) {
            return true;
        }

        return false;

    }


    /**
     * Создать хранилища необходимые для работы модуля
     */
    public function deleteStorage()
    {
        $tokenStorage = $this->tokenStorage->drop();
        $settingStorage = $this->settingStorage->drop();
        $log = $this->log->drop();
        if ($tokenStorage && $settingStorage && $log) {
            return true;
        }

        return false;

    }


    /**
     * Визивается при окончании оформления заказа
     * для привязки заказа на стороне цмс и на стороне сервера
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     * @param null $payment_price - заказ  наложенным платежем или нет [0,1]
     * @param string $comment
     * @return bool
     */
    public function onCmsOrderFinish(
        $sdkId, $cmsId, $payment, $status, $to_name,
        $to_phone, $to_email, $payment_price = null, $comment = ''
    ) {
        if ($payment_price === null) {
            if ($this->settingStorage->getParam(Adapter::PARAM_PAYMENT_LIST) == $payment) {
                $payment_price = 1;
            } else {
                $payment_price = 0;
            }
        } else {
            $payment_price = (int)$payment_price;
        }
        $result = $this->api->editOrder($sdkId, $cmsId, $payment, $status, $to_name,
            $to_phone, $to_email, $payment_price, $comment);
        if (isset($result['success']) && $result['success'] == 1 && !empty($result['data']['id'])) {
            return $result['data'];
        }
        return false;
    }


    /**
     *
     * Получить информацию о заказе по сдк ID
     *
     * @param $sdkId
     * @return array
     */
    public function viewOrder($sdkId)
    {
        if (!empty($sdkId)) {
            $result = $this->api->viewOrder($sdkId);
            if (isset($result['success']) && ($result['success'] == 1) && (!empty($result['data']['id']))) {
                return $result['data'];
            }
        }
        return array();
    }

    /**
     *
     * Отправить заказ  на DDelivery.ru
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     * @param null $payment_price - наложенный платеж,
     * по умолчанию берется из значения параметра настроек варианта оплаты,
     * но возможно выставлять и вручную
     * @param string $comment
     * @param float|int $payment_price_value
     * @return int
     * @throws DDeliveryException
     */
    public function cmsSendOrder(
        $sdkId, $cmsId, $payment, $status, $to_name,
        $to_phone, $to_email, $payment_price = null,
        $comment = '', $payment_price_value = 0
    ) {
        $payment_price = $this->_getPaymentPrice($payment, $payment_price);
        $result = $this->api->sendOrder($sdkId, $cmsId, $payment, $status, $payment_price, $to_name,
            $to_phone, $to_email, $comment, $payment_price_value);
        if (isset($result['success']) && $result['success'] == 1) {
            $ddelivery_id = $result['data']['ddelivery_id'];
            return $ddelivery_id;
        } else {
            throw new DDeliveryException($result['error_description']);
        }
    }


    /**
     * Вызывается при смене статуса заказа, если статус заказа соответствует
     * статусу указанному в настройках
     * то заказ отправляется на сервер DDelivery.ru
     *
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     * @param null $payment_price - наложенный платеж [0,1]
     * @param string $comment
     * @param int|float $payment_price_value
     *
     * @return int
     * @throws DDeliveryException
     */
    public function onCmsChangeStatus(
        $sdkId, $cmsId, $payment, $status, $to_name, $to_phone,
        $to_email, $payment_price = null, $comment = '',
        $payment_price_value = 0
    ) {
        if ($this->settingStorage->getParam(Adapter::PARAM_STATUS_LIST) != $status) {
            return 0;
        }
        $payment_price = $this->_getPaymentPrice($payment, $payment_price);
        $result = $this->api->sendOrder($sdkId, $cmsId, $payment, $status, $payment_price,
            $to_name, $to_phone, $to_email, $comment, $payment_price_value);

        if (isset($result['success']) && $result['success'] == 1) {
            $ddelivery_id = $result['data']['ddelivery_id'];
            return $ddelivery_id;
        } else {
            throw new DDeliveryException($result['error_description']);
        }
    }

    /**
     *
     * Редактирование заказа
     *
     * @param $sdkId - идентификатор на сервере полученный при оформлении заказа
     * @param $cmsId - идентификатор заказа в CMS
     * @param $payment - вариант оплаты(идентификатор)
     * @param $status - статус заказа(идентификатор)
     * @param $to_name - имя покупателя
     * @param $to_phone - телефон покупателя
     * @param $to_email - email покупателя
     * @param null $payment_price - наложенный платеж [0,1]
     * @param string $comment
     * @param int|float $payment_price_value
     * @return mixed
     * @throws DDeliveryException
     */
    public function changeOrder(
        $sdkId, $cmsId, $payment, $status, $to_name, $to_phone,
        $to_email, $payment_price = null, $comment = '',
        $payment_price_value = 0
    ) {
        $payment_price = $this->_getPaymentPrice($payment, $payment_price);

        $result = $this->api->changeOrder($sdkId, $cmsId, $payment, $status, $payment_price,
            $to_name, $to_phone, $to_email, $comment, $payment_price_value);
        if (isset($result['success']) && $result['success'] == 1) {
            $id = $result['data']['id'];
            return $id;
        } else {
            throw new DDeliveryException($result['error_description']);
        }
    }

    /**
     *
     * Проверить токен рукопожатия на стороне серверного сдк
     * @param $token
     * @return bool
     */
    public function checkHandshakeToken($token)
    {
        $result = $this->api->checkHandshakeToken($token);
        if (isset($result['success']) && $result['success'] == 1) {
            return true;
        }
        return false;
    }

    /**
     * Сохранить настройки на стороне цмс
     *
     * @param $settings
     * @return bool
     */
    public function saveSettings($settings)
    {
        if ($this->settingStorage->save($settings)) {
            return true;
        }
        return false;
    }


    /**
     * Получить токен для входа в панель серверного сдк
     *
     * Урл магазина
     *
     * @param string $realUrl
     * @return null
     * @throws DDeliveryException
     */
    public function renderAdmin($realUrl = '')
    {
        $token = $this->generateToken();
        if (empty($token)) {
            throw new DDeliveryException("Ошибка генерции токена");
        }

        $result = $this->api->accessAdmin($token, $realUrl);
        if (isset($result['success']) && ($result['success'] == 1)) {
            return $result['data'];
        }
        return null;
    }


    /**
     *
     * Получить токен редактирования заказа
     *
     * @param $cart
     * @param $id
     * @return null
     */
    public function renderEditOrderToken($cart, $id)
    {
        $result = $this->api->pushOrderEditCart($cart, $id);
        if (isset($result['success']) && $result['success'] == 1) {
            return $result['data'];
        }
        return null;
    }


    /**
     *
     * Получить токен для показа модуля
     *
     * @param $cart
     * @return null
     */
    public function renderModuleToken($cart)
    {
        $result = $this->api->pushCart($cart);
        if (isset($result['success']) && $result['success'] == 1) {
            return $result['data'];
        }
        return null;
    }


    /**
     * Сгенерировать токен доступа
     * виполнения команд на стороне цмс
     *
     * @return string
     */
    public function generateToken()
    {
        $token = Utils::generateToken();
        if ($this->tokenStorage->createToken($token, self::TOKEN_LIFE_TIME)) {
            return $token;
        }
        return null;
    }

    /**
     *
     * Проверить токен доступа
     * виполнения команд на стороне цмс
     *
     * @param $token
     * @return bool
     */
    public function checkToken($token)
    {
        if ($this->tokenStorage->checkToken($token)) {
            return true;
        }
        return false;
    }

    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * @param \DDelivery\Storage\SettingStorageInterface $settingStorage
     */
    public function setSettingStorage($settingStorage)
    {
        $this->settingStorage = $settingStorage;
    }

    /**
     * @return \DDelivery\Storage\SettingStorageInterface
     */
    public function getSettingStorage()
    {
        return $this->settingStorage;
    }

    /**
     * @param \DDelivery\Storage\TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage($tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return \DDelivery\Storage\TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return \DDelivery\Storage\LogStorageInterface
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     *
     * Наложенный платеж заказа (Да|Нет)
     *
     * @param $payment
     * @param $payment_price
     * @return int
     */
    protected function _getPaymentPrice($payment, $payment_price)
    {
        if ($payment_price === null) {
            if ($this->settingStorage->getParam(Adapter::PARAM_PAYMENT_LIST) == $payment) {
                $payment_price = 1;
            } else {
                $payment_price = 0;
            }
        } else {
            $payment_price = (int)$payment_price;
        }
        return $payment_price;
    }

}