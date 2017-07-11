<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:41 PM
 */

namespace DDelivery\Storage;


abstract class DBStorage {

    /**
     * @var \PDO
     */
    public $pdo;

    public $dbType;

    public $tableName;

    public function __construct($pdo, $dbType, $pdoTablePrefix = ''){
        $this->pdo = $pdo;
        $this->dbType = $dbType;
        $this->tableName = $pdoTablePrefix . $this->getTableName() ;
    }

    /**
     *
     * Получить название таблицы
     *
     * @return string
     */
    abstract public function  getTableName();

    /**
     *
     * Создать хранилище
     *
     * @return bool
     */
    abstract public function  createStorage();

    public function  drop(){

    }
} 