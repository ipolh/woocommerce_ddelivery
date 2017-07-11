<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:08 PM
 */

namespace DDelivery;


use DateTime;
use DDelivery\Adapter\Adapter;
use DDelivery\Business\Business;
use DDelivery\Storage\LogStorageInterface;

class DDeliveryUI
{

    public $request;

    /**
     * @var Adapter
     */
    public $adapter;

    /**
     * @var Business
     */
    public $business;

    /**
     * @var LogStorageInterface
     */
    public $log;


    public function actionDefault()
    {
        //throw new DDeliveryException("Not Found");
        return 1;
    }


    /**
     *
     * Валидация дати
     *
     * @param $date
     * @param string $format
     * @return bool
     */
    function validateDate($date, $format = 'Y.m.d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


    /**
     *
     * Получить заказ по ID
     *
     * @return array
     * @throws DDeliveryException
     */
    public function actionOrder()
    {
        if (!empty($this->request['id'])) {
            $order = $this->adapter->getOrder($this->request['id']);
            return $order;
        }
        throw new DDeliveryException("Ошибка получения заказа");
    }


    /**
     * Сбор логов
     * @return array
     */
    public function actionLog()
    {
        $logs = $this->log->getAllLogs();
        $this->log->deleteLogs();
        return $logs;
    }


    /**
     *
     * Получить список заказов
     *
     * @return array
     * @throws DDeliveryException
     */
    public function actionOrders()
    {
        if ($this->validateDate($this->request['from']) &&
            $this->validateDate($this->request['to'])
        ) {
            $orders = $this->adapter->getOrders($this->request['from'], $this->request['to']);
            if (count($orders)) {
                return $orders;
            }
            return array();
        }
        throw new DDeliveryException("Ошибка получения списка заказов");
    }

    /**
     * Получить информацию о заказе по cmsId
     *
     * @return array
     * @throws DDeliveryException
     */
    public function actionVersion()
    {
        return array(
            'version' => $this->adapter->getCmsVersion(),
            'cms' => $this->adapter->getCmsName(),
            'sdk' => Adapter::SDK_VERSION
        );
    }


    /**
     * Получить список полей настроек
     *
     * @return array
     */
    public function actionFields()
    {
        return $this->adapter->getFieldList();
    }

    /**
     * Сохранить настройки
     *
     * @return int
     * @throws DDeliveryException
     */
    public function actionSave()
    {
        if (!empty($this->request['cms'])) {
            $result = $this->business->saveSettings($this->request['cms']);
            if ($result) {
                return 1;
            }
        }
        throw new DDeliveryException("Ошибка сохранения настроек");
    }


    /**
     * Обработка пуша статусов
     */
    public function actionPush()
    {
        if (!empty($this->request['status'])) {
            if ($this->adapter->changeStatus($this->request['status'])) {
                return 1;
            }
        }
        return 0;
    }


    /**
     *
     * Редактировать заказ
     *
     * @throws DDeliveryException
     */
    public function actionEdit()
    {

        if ($this->adapter->isAdmin()) {
            $cart = $this->adapter->getAdminCartAndDiscount();
            $token = $this->business->renderEditOrderToken($cart, (int)$this->request['id']);

            if ($token) {
                $url = $this->adapter->getSdkServer() . 'delivery/' . $token . '/edit.json';
                $params = http_build_query($this->adapter->getUserParams($this->request));
                $url .= (empty($params)) ? '' : '?' . $params;
                $this->setRedirect($url);
            }
        } else {
            throw new DDeliveryException("Для редактирвания заказа необходимо быть администратором");
        }
        throw new DDeliveryException("Ошибка входа в админ панель");
    }


    /**
     *
     * Перейти к форме оформления заказа
     *
     * @throws DDeliveryException
     */
    public function actionModule()
    {
        $cart = $this->adapter->getCartAndDiscount();
        $token = $this->business->renderModuleToken($cart);
        if ($token) {
            $url = $this->adapter->getSdkServer() . 'delivery/' . $token . '/index.json';
            $params = http_build_query($this->adapter->getUserParams($this->request));
            $url .= (empty($params)) ? '' : '?' . $params;
            $this->setRedirect($url);
            return;
        }
        throw new DDeliveryException("Ошибка вывода модуля");
    }


    /**
     *
     * Перейти к форме оформления заказа
     *
     * @throws DDeliveryException
     */
    public function actionShop()
    {
        $cart = $this->adapter->getCartAndDiscount();
        $token = $this->business->renderModuleToken($cart);
        if ($token) {
            $url = $this->adapter->getSdkServer() . 'ui/' . $token . '/module.json';
            $params = http_build_query($this->adapter->getUserParams($this->request));
            $url .= (empty($params)) ? '' : '?' . $params;
            $this->setRedirect($url);
        }
        throw new DDeliveryException("Ошибка входа в магазин");
    }


    /**
     *
     * Переход в админку СДК
     *
     * @throws DDeliveryException
     */
    public function actionAdmin()
    {

        if ($this->adapter->isAdmin()) {
            $token = $this->business->renderAdmin($this->adapter->getRealUrl());
            if ($token) {
                $url = $this->adapter->getSdkServer() . 'passport/' .
                    $this->adapter->getApiKey() . '/admin.json?token=' . $token;
                $this->setRedirect($url);
            }
        } else {
            throw new DDeliveryException("Для входа в админ панель
                                            необходимо быть администратором");
        }
        throw new DDeliveryException("Ошибка входа в админ панель");
    }


    /**
     *
     * Сгенерировать токен для виполнения операций
     *
     * @return array
     * @throws DDeliveryException
     */
    public function actionHandshake()
    {
        if (isset($this->request['api_key']) && isset($this->request['token'])) {
            if ($this->request['api_key'] == $this->adapter->getApiKey()) {
                if ($this->business->checkHandshakeToken($this->request['token'])) {
                    $token = $this->business->generateToken();
                    if (!empty($token)) {
                        return array('token' => $token);
                    }
                }
            }
        }
        throw new DDeliveryException("Ошибка рукопожатия");
    }

    public function render(array $request)
    {
        $this->request = $request;
        $this->preRender();
        $success = 1;
        try {
            if (!isset($request['action'])) {
                $request['action'] = 'default';
            }
            $request['action'] = strtolower($request['action']);
            if (in_array($request['action'], $this->getTokenMethod())) {
                if (!$this->checkToken()) {
//                    throw new DDeliveryException("Ошибка доступа в раздел");
                }
            }
            $action = 'action' . ucfirst(strtolower($request['action']));
            // If the action doesn't exist, it's a 404
            if (!method_exists($this, $action)) {
                $action = 'actionDefault';
            }
            $data = $this->{$action}();
        } catch (\Exception $e) {
            $success = 0;
            $data = $e->getMessage();
            $data = array(array('error' => $data));
            $this->log->saveLog($e->getMessage());
            echo $e->getMessage();
            exit;
        }
        $this->postRender();
        echo json_encode(array('success' => $success, 'data' => $data));
    }

    /**
     * Проверка существования токена для совершения
     * закритого метода
     *
     * @return bool
     */
    public function checkToken()
    {

        if (isset($this->request['api_key']) && isset($this->request['token'])) {

            if ($this->business->checkToken($this->request['token'])
                && $this->request['api_key'] == $this->adapter->getApiKey()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * Методи которие доступни по токену
     *
     * @return array
     */
    public function getTokenMethod()
    {
        return array('orders', 'push', 'fields', 'save', 'order', 'log', 'version');
    }

    public function preRender()
    {

    }

    public function setRedirect($url)
    {
        header('Location: ' . $url);
    }

    /**
     * @param $adapter
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param Business $business
     */
    public function setBusiness(Business $business)
    {
        $this->business = $business;
    }


    public function actionLogin()
    {
        if ($this->checkToken()) {
            return 1;
        }
        return 0;
    }

    /**
     * @param Storage\LogStorageInterface $log
     */
    public function setLog(LogStorageInterface $log)
    {
        $this->log = $log;
    }


    public function postRender()
    {

    }
}