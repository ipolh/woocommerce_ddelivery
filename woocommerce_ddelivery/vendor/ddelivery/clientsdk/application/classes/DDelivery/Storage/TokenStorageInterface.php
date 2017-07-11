<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 10:37 PM
 */

namespace DDelivery\Storage;


interface TokenStorageInterface {

    public function createStorage();

    public function deleteExpired();

    public function checkToken($token);

    public function createToken($token, $expired);

    /**
     * Выбрать все записи
     *
     * @return array
     */
    public function getAll();

    public function drop();
}