<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/9/15
 * Time: 11:06 PM
 */
use DDelivery\Adapter\Container;
error_reporting(E_ALL);
ini_set('display_errors', '1');
require(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application','bootstrap.php')));
require('IntegratorAdapter.php');
//echo implode(DIRECTORY_SEPARATOR, array(__DIR__,'application','bootstrap.php'));
$adapter = new IntegratorAdapter();
$container = new Container(array('adapter' => $adapter));
/*$container->getBusiness()->initStorage();
$container->getBusiness()->deleteStorage();*/
$container = new Container(array('adapter' => $adapter));
//$container->getBusiness()->initStorage();
$container->getUi()->render($_REQUEST);