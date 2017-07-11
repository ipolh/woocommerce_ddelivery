<?php
use DDelivery\Adapter\Container;
use DDelivery\Business\Business;

error_reporting(E_ALL);
ini_set('display_errors', '1');
require(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application','bootstrap.php')));
require('IntegratorAdapter.php');
//echo implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php'));
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        input{
            display: inline-block;
        }
        .form-container{
            margin-top: 10px;
        }
        .highlight{
            border: solid 1px black;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="highlight">
    <?php
    /**
     * Created by PhpStorm.
     * User: mrozk
     * Date: 14.07.15
     * Time: 9:16
     */
    $business = $container->getBusiness();
    /**
     * @param $business
     */
    function performOnCmsOrderFinish(Business $business)
    {
        echo '<h3>Привязка заказа CMS к id заказа в сдк</h3>';
        $data = $business->onCmsOrderFinish(
                            $_POST['sdk_id'], $_POST['cms_id'],
                            $_POST['payment'], $_POST['status'],
                            $_POST['to_name'], $_POST['to_phone'],
                            $_POST['to_email']
        );
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($data['id'] > 0) {
            echo 'Заказ готов к отправке на ddelivery.ru';
        } else {
            echo 'Заказ уже привязан';
        }
    }

    /**
     * @param $business
     */
    function performViewOrder( Business $business)
    {
        echo '<h3>получить информацию о полях доставки</h3>';
        echo '<pre>';
        print_r($business->viewOrder($_POST['sdk_id']));
        echo '</pre>';
    }

    /**
     * @param $business
     */
    function performCmsSendOrder( Business $business)
    {
        echo '<h3>Отправка заявки на ddelivery.ru</h3>';
        echo '<pre>';
        $ddeliveryId = (int)$business->cmsSendOrder(
                        $_POST['sdk_id'], $_POST['cms_id'],
                        $_POST['payment'], $_POST['status'],
                        $_POST['to_name'], $_POST['to_phone'],
                        $_POST['to_email']
        );
        if ($ddeliveryId > 0) {
            echo 'Идентификатор заявки ' . $ddeliveryId;
        }
        echo '</pre>';
    }

    if(isset($_POST['action'])) {
        try{
            echo '<pre>';
            echo '<h1>Параметры POST</h1>';
            print_r($_POST);
            echo '</pre>';



            if ($_POST['action'] == 1) {
                performOnCmsOrderFinish($business);
            }elseif($_POST['action'] == 2) {
                performViewOrder($business);
            }elseif($_POST['action'] == 3){
                performCmsSendOrder($business);
            }


        }catch (\DDelivery\DDeliveryException $e){
            echo '<h3>Ошибка ' . $e->getMessage() . '</h3>';
        }
    }
    ?>
</div>
    <div class="highlight" style="margin-top: 20px;">
        <div class="form-container">
            <form method="post" action="delivery.php">
                <table>
                    <tr>
                        <td><label>имя клиента</label></td>
                        <td><input type="text" placeholder="" name="to_name" value="Иванов Иван" ></td>
                    </tr>
                    <tr>
                        <td><label>номер телефона</label></td>
                        <td><input type="text" placeholder="" name="to_phone" value="+7(926)111-11-11" ></td>
                    </tr>
                    <tr>
                        <td><label>email</label></td>
                        <td><input type="text" placeholder="" name="to_email" value="example2014@gmail.com" ></td>
                    </tr>

                    <tr>
                        <td><label>id статуса заказа</label></td>
                        <td><input type="text" name="status" value="status_id_17" ></td>
                    </tr>
                    <tr>
                        <td><label>id способа оплаты</label></td>
                        <td><input type="text" name="payment" value="payment_id_12" ></td>
                    </tr>
                    <tr>
                        <td><label>id заказа в CMS</label></td>
                        <td><input type="text" placeholder="" name="cms_id" value="12245" ></td>
                    </tr>
                </table>
                <input type="hidden" name="sdk_id" value="<?=$_POST['sdk_id']?>">
                <input type="hidden" name="action" value="1">
            <button>Окончание оформления заказа (OnCmsOrderFinish)</button>
        </form>
        </div>
        <div class="form-container">
            <form  method="post" action="delivery.php">
                <input type="hidden" name="sdk_id" value="<?=$_POST['sdk_id']?>">
                <input type="hidden" name="action" value="2">
                <button> получить информацию о полях доставки (viewOrder)</button>
            </form>
        </div>
        <div class="form-container">
            <form  method="post" action="delivery.php">
                    <input type="hidden" placeholder="" name="to_name" value="Иванов Иван" >
                    <input type="hidden" placeholder="" name="to_phone" value="+7(926)111-11-11" >
                    <input type="hidden" placeholder="" name="to_email" value="example2014@gmail.com" >
                    <input type="hidden" name="status" value="status_id_17" >
                    <input type="hidden" name="payment" value="payment_id_12" >
                    <input type="hidden" placeholder="" name="cms_id" value="12245" >
                    <input type="hidden" name="sdk_id" value="<?=$_POST['sdk_id']?>">
                    <input type="hidden" name="action" value="3">
                    <button>Отправка заявки на ddelivery.ru(cmsSendOrder или onCmsChangeStatus)</button>
            </form>
         </div>
    </div>
</body>