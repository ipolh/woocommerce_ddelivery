<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 11:33 PM
 */

namespace DDelivery\Storage;


interface SettingStorageInterface {

    public function createStorage();

    public function save($settings);

    public function getParam($paramName);

    public function drop();
} 