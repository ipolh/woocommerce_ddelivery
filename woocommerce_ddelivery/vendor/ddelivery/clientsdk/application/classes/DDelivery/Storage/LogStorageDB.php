<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/22/15
 * Time: 7:08 PM
 */

namespace DDelivery\Storage;


use DDelivery\Adapter\Adapter;

class LogStorageDB extends DBStorage implements LogStorageInterface  {

    /**
     *
     * Получить название таблицы
     *
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_logs';
    }

    /**
     *
     * Создать хранилище
     *
     * @return bool
     */
    public function  createStorage(){
        if($this->dbType == Adapter::DB_MYSQL) {
            $query = "CREATE TABLE IF NOT EXISTS `$this->tableName` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `created` DATETIME NOT NULL,
                            `content` TEXT DEFAULT NULL,
                            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }elseif($this->dbType == Adapter::DB_SQLITE){
            $query = "CREATE TABLE IF NOT EXISTS $this->tableName (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            created TEXT,
                            content TEXT
                            )";
        }
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }


    public function getAllLogs(){
        $query = 'SELECT * FROM ' . $this->tableName;
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        return $result;
    }

    public function saveLog($content){
        if($this->dbType == Adapter::DB_SQLITE) {
            $query = 'INSERT INTO '.$this->tableName.'(content, created) VALUES
                            (:content, datetime("now"))';
        }elseif($this->dbType == Adapter::DB_MYSQL) {
            $query = 'INSERT INTO ' . $this->tableName . '(content, created) VALUES
                            (:content, NOW())';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':content', $content );
        return $sth->execute();
    }

    public function deleteLogs(){
        $query = 'DELETE FROM ' . $this->tableName;
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }

    public function drop()
    {
        $query = "DROP TABLE " . $this->getTableName();
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }
}