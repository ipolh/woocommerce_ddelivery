<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:55 PM
 */

namespace DDelivery\Adapter;


use DDelivery\DB\ConnectInterface;
use DDelivery\DDeliveryException;
use DDelivery\Utils;
use PDO;

abstract class Adapter
{

    public $params;

    const SDK_VERSION = '0.9';

    const SDK_SERVER_SDK = 'http://sdk.ddelivery.ru/api/v1/';

    const SDK_SERVER_STAGE_SDK = 'http://stagesdk.ddelivery.ru/api/v1/';

    const SDK_SERVER_DEV_SDK = 'http://devsdk.ddelivery.ru/api/v1/';

    const SDK_SERVER_DEV_SDK1 = 'http://devsdk1.ddelivery.ru/api/v1/';

    const SDK_SERVER_DEV_SDK2 = 'http://devsdk2.ddelivery.ru/api/v1/';

    const FIELD_TYPE_LIST = 'list';

    const FIELD_TYPE_TEXT = 'text';

    const FIELD_TYPE_CHECKBOX = 'checkbox';

    const PARAM_PAYMENT_LIST = 'payment_list';

    const PARAM_STATUS_LIST = 'status_list';

    const DB_MYSQL = 1;

    const DB_SQLITE = 2;

    /**
     * Поле ФИО
     */
    const USER_FIELD_NAME = 'to_name';
    /**
     * Поле email
     */
    const USER_FIELD_EMAIL = 'to_email';
    /**
     * Поле телефон
     */
    const USER_FIELD_PHONE = 'to_phone';
    /**
     * Поле улица
     */
    const USER_FIELD_STREET = 'to_street';
    /**
     * Поле дом
     */
    const USER_FIELD_HOUSE = 'to_house';
    /**
     * Поле Квартира
     */
    const USER_FIELD_FLAT = 'to_flat';
    /**
     * Поле индекс
     */
    const USER_FIELD_ZIP = 'to_zip';

    /**
     * Поле комментарий
     */
    const USER_FIELD_COMMENT = 'comment';

    public function __construct($params = array())
    {
        $this->params = $params;
    }

    public function getEnterPoint()
    {
        return Utils::fullUrl($_SERVER, false);
    }

    public function getPathByDB()
    {
        return '../db/db.sqlite';
    }


    /**
     * Настройки базы данных
     * @return array
     */
    public function getDbConfig()
    {
        return array(
            'type' => self::DB_SQLITE,
            'dbPath' => $this->getPathByDB(),
            'prefix' => '',
        );
        return array(
            'pdo' => new \PDO('mysql:host=localhost;dbname=ddelivery', 'root', '0', array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
            'prefix' => '',
        );
        return array(
            'type' => self::DB_MYSQL,
            'dsn' => 'mysql:host=localhost;dbname=ddelivery',
            'user' => 'root',
            'pass' => '0',
            'prefix' => '',
        );
    }

    /**
     *
     * Получить объект PDO
     *
     * @return \PDO
     * @throws DDeliveryException
     */
    public function getDb()
    {
        $dbConfig = $this->getDbConfig();
        if (isset($dbConfig['pdo']) && ($dbConfig['pdo'] instanceof \PDO || $dbConfig['pdo'] instanceof ConnectInterface)) {
            $pdo = $dbConfig['pdo'];
        } elseif ($dbConfig['type'] == self::DB_SQLITE) {
            if (!$dbConfig['dbPath']) {
                throw new DDeliveryException('SQLite db is empty');
            }
            $dbDir = dirname($dbConfig['dbPath']);

            if ((!is_writable($dbDir)) || (!is_writable($dbConfig['dbPath'])) || (!is_dir($dbDir))) {
                throw new DDeliveryException('SQLite database does not exist or is not writable');
            }

            $pdo = new \PDO('sqlite:' . $dbConfig['dbPath']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //$pdo->exec('PRAGMA journal_mode=WAL;');
            //$pdo->errorInfo()
        } elseif ($dbConfig['type'] == self::DB_MYSQL) {
            $pdo = new \PDO($dbConfig['dsn'], $dbConfig['user'], $dbConfig['pass']);
            $pdo->exec('SET NAMES utf8');
        } else {
            throw new DDeliveryException('Not support database type');
        }
        return $pdo;
    }

    /**
     *
     * Получить апи ключ
     *
     * @return string
     * @throws \DDelivery\DDeliveryException
     */
    abstract public function getApiKey();


    /**
     *
     * При синхронизации статусов заказов необходимо
     * [
     *      'id' => 'status',
     *      'id2' => 'status2',
     * ]
     *
     * @param array $orders
     * @return bool
     */
    abstract public function changeStatus(array $orders);

    abstract public function getCmsName();

    abstract public function getCmsVersion();


    /**
     * Получить  заказ по id
     * ['city' => город назначения, 'payment' => тип оплаты, 'status' => статус заказа,
     * 'sum' => сумма заказа, 'delivery' => стоимость доставки]
     *
     * город назначения, тип оплаты, сумма заказа, стоимость доставки
     *
     * @param $id
     * @return array
     */
    abstract public function getOrder($id);


    /**
     * Получить список заказов за период
     * ['city' => город назначения, 'payment' => тип оплаты, 'status' => 'статус заказа'
     * 'sum' => сумма заказа, 'delivery' => стоимость доставки]
     *
     * город назначения, тип оплаты, сумма заказа, стоимость доставки
     *
     * @param $from
     * @param $to
     * @return array
     */
    abstract public function getOrders($from, $to);


    /**
     *
     * Получить поля пользователя для отправки на серверное сдк
     *
     * @param $request
     * @return array
     */
    public function getUserParams($request)
    {
        if (is_array($request) && count($request) > 0) {
            foreach ($request as $key => $item) {
                if (!is_array($item)) {
                    $request[$key] = urlencode($item);
                }
            }
        }
        return $request;
    }


    /**
     *
     * Получить скидку
     *
     * @return float
     */
    abstract public function getDiscount();

    /**
     *
     * получить продукты из корзины
     *
     * @return array
     */
    abstract public function getProductCart();


    /**
     * Получить скидку
     * @return float
     */
    public function getAdminDiscount()
    {
        $this->getDiscount();
    }

    /**
     *
     * Получить корзину заказа в админке
     * @return array
     */
    public function getAdminProductCart()
    {
        $this->getProductCart();
    }

    /**
     *
     * Получить корзину и скидку
     *
     * @return array
     */
    public function getCartAndDiscount()
    {
        $cart = array(
            "products" => $this->getProductCart(),
            "discount" => $this->getDiscount()
        );
        return $cart;
    }

    /**
     *
     * Получить корзину и скидку в административной панели
     * для редактирования заказов
     *
     * @return array
     */
    public function getAdminCartAndDiscount()
    {

        $cart = array(
            "products" => $this->getAdminProductCart(),
            "discount" => $this->getAdminDiscount()
        );
        return $cart;
    }


    /**
     * Получить урл апи сервера
     *
     * @return string
     */
    public function getSdkServer()
    {
        return self::SDK_SERVER_SDK;
    }

    /**
     * Получить массив с соответствием статусов DDelivery
     * @return array
     */
    abstract public function getCmsOrderStatusList();


    /**
     * Получить массив со способами оплаты
     * @return array
     */
    abstract public function getCmsPaymentList();


    /***
     *
     * В этом участке средствами Cms проверить права доступа текущего пользователя,
     * это важно так как на базе этого  метода происходит вход
     * на серверние настройки
     *
     * @return bool
     */
    abstract public function isAdmin();

    public function getCustomSettingsFields()
    {
        return array();
    }

    /***
     *
     * Для формирование настроек на стороне серверного сдк,
     * описание в виде массива
     *
     * @return array
     */
    function getFieldList()
    {

        $userFields = $this->getCustomSettingsFields();
        $requiredFields = array(
            array(
                "title" => "Способы оплаты, который соответствует наложенному платежу",
                "type" => self::FIELD_TYPE_LIST,
                "name" => self::PARAM_PAYMENT_LIST,
                "items" => $this->getCmsPaymentList(),
                "default" => 0,
                "data_type" => array("int"),
                "required" => 1
            ),
            array(
                "title" => "Статус заказа для отправки на сервер DDelivery.ru ",
                "type" => self::FIELD_TYPE_LIST,
                "name" => self::PARAM_STATUS_LIST,
                "items" => $this->getCmsOrderStatusList(),
                "default" => 0,
                "data_type" => array("int"),
                "required" => 1
            )
        );

        return array_merge($userFields, $requiredFields);
    }

    public function getRealUrl()
    {
        return '';
    }
} 