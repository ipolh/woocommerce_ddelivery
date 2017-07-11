<?php
use DDelivery\Adapter\Adapter;
use DDelivery\DDeliveryException;
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 2:53 PM
 */
class IntegratorAdapter extends Adapter  {
    /**
     *
     * Получить апи ключ
     *
     * @throws DDeliveryException
     * @return string
     */
    public function getApiKey(){
        return 'c0dbcd6dd89837104de0d7f42b9fe7b2';
    }
    public function getPathByDB(){
        return 'db.sqlite';
    }
    /**
     * Настройки базы данных
     * @return array
     */
    public function getDbConfig(){

        return array(
            'type' => self::DB_MYSQL,
            'dsn' => 'mysql:host=localhost;dbname=sdk_test',
            'user' => 'root',
            'pass' => 'root',
            'prefix' => '',
        );

        return array(
            'pdo' => new \PDO('mysql:host=localhost;dbname=sdk_test', 'root', 'root', array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
            'prefix' => '',
        );

        return array(
            'type' => self::DB_SQLITE,
            'dbPath' => $this->getPathByDB(),
            'prefix' => '',
        );


    }
    /**
     *
     * При синхронизации статусов заказов необходимо
     * array(
     *      'id' => 'status',
     *      'id2' => 'status2',
     * )
     *
     * @param array $orders
     * @return bool
     */
    public function changeStatus(array $orders){
        // TODO: Implement changeStatus() method.
    }
    /**
     * Получить урл апи сервера
     *
     * @return string
     */
    public function getSdkServer(){
        return self::SDK_SERVER_SDK ;
    }
    public function getCmsName(){
        return 'CmsExample';
    }
    public function getCmsVersion(){
        return '1.1';
    }
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
    public function getOrder($id){
        return array(
            'city' => 'Урюпинск',
            'payment_id' => 22,
            'payment_name' => "Карточкой",
            'status_id' => 11,
            'status' => 'Статус',
            'date' => '2015.12.12',
            'sum' => 2200,
            'delivery' => 220,
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
     * @return array
     */
    public function getOrders($from, $to){
        return array(
            array(
                'city' => 'Урюпинск',
                'payment_id' => 22,
                'payment_name' => "Карточкой",
                'status_id' => 11,
                'status' => 'Статус',
                'date' => '2015.12.12',
                'sum' => 2200,
                'delivery' => 220,
            ),
            array(
                'city' => 'г. Москва, Московская область',
                'payment_id' => 22,
                'payment_name' => "Наличными",
                'status_id' => 11,
                'status' => 'Отгружен',
                'date' => '2015.13.14',
                'sum' => 2100,
                'delivery' => 120,
            ),
            array(
                'city' => 'Сити Питер',
                'payment_id' => 42,
                'payment_name' => "Рубли",
                'status_id' => 11,
                'status' => 'Отгружен',
                'date' => '2015.11.17',
                'sum' => 2100,
                'delivery' => 120,
            )
        );
    }
    /**
     *
     * Получить поля пользователя для отправки на серверное сдк
     *
     * @param $request
     * @return array
     */
    public function getUserParams($request){
        return array(
            self::USER_FIELD_STREET => 'Цветаевой',
            self::USER_FIELD_COMMENT => 'Комментарий',
            self::USER_FIELD_HOUSE => '2а',
            self::USER_FIELD_FLAT => '123',
            self::USER_FIELD_ZIP => '10101'
        );

        //return parent::getUserParams($request);
        /*
        return array(
            self::USER_FIELD_STREET => 'Цветаевой',
            self::USER_FIELD_COMMENT => 'Комментарий',
            self::USER_FIELD_HOUSE => '2а',
            self::USER_FIELD_FLAT => '123',
            self::USER_FIELD_ZIP => '10101'
        );
        */
    }
    /**
     * Получить скидку в рублях
     *
     * @return float
     */
    public function getDiscount(){
        return 0;
    }
    /**
     *
     * Получить содержимое корзини
     *
     * @return array
     */
    public function getProductCart(){
        return array(
            array(
                "id"    =>  12,
                "name"  =>  "Веселый клоун",
                "width" =>  10,
                "height"=>10,
                "length"=>10,
                "weight"=>1,
                "price"=>1110,
                "quantity"=>2,
                "sku"=>"app2"
            )
        );
    }
    /**
     * Получить массив с соответствием статусов DDelivery
     * @return array
     */
    public function getCmsOrderStatusList(){
        return array('10' => 'Завершен', '11' => 'Куплен');
    }
    /**
     * Получить массив со способами оплаты
     * @return array
     */
    public function getCmsPaymentList(){
        return array('14' => 'Наличными', '17' => 'Карточкой');
    }
    /***
     *
     * В этом участке средствами Cms проверить права доступа текущего пользователя,
     * это важно так как на базе этого  метода происходит вход
     * на серверние настройки
     *
     * @return bool
     */
    public function isAdmin(){
        return true;
    }
    /**
     * Получить список кастомных полей в CAP
     *
     * @return array
     */
    public function getCustomSettingsFields(){
        return array(
            array(
                "title" => "Название (Пример кастомного поля)",
                "type" => self::FIELD_TYPE_TEXT,
                "name" => "name",
                //"items" => getStatusList(),
                "default" => 0,
                "data_type" => array("string"),
                "required" => 1
            ),
            array(
                "title" => "Выводить способ доставки(Пример кастомного поля)",
                "type" => self::FIELD_TYPE_CHECKBOX,
                "name" => "checker",
                "default" => true,
                "data_type" => array("int"),
                "required" => 1
            )
        );
    }
}