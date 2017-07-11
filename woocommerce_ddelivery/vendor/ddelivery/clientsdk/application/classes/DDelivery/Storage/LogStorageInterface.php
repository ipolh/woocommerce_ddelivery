<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/22/15
 * Time: 7:09 PM
 */

namespace DDelivery\Storage;


interface LogStorageInterface {

    /**
     * Создаем хранилище
     *
     * @return bool
     */
    public function createStorage();


    public function getAllLogs();


    public function saveLog($content);

    /**
     * @return string
     */
    public function  getTableName();


    public function deleteLogs();

    public function drop();
} 