<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 12:51 PM
 */

namespace DDelivery\Storage;


use DDelivery\Adapter\Adapter;

class TokenStorageDB extends DBStorage implements TokenStorageInterface {

    /**
     * @return string
     */
    public function  getTableName(){
        return 'ddelivery_tokens';
    }

    public function createStorage(){
        if($this->dbType == Adapter::DB_MYSQL) {
            $query = "CREATE TABLE IF NOT EXISTS  `$this->tableName` (
                            `token` varchar(60) NOT NULL,
                            `created` DATETIME NOT NULL,
                            `expires` DATETIME NOT NULL,
                            PRIMARY KEY (`token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }elseif($this->dbType == Adapter::DB_SQLITE){
            $query = "CREATE TABLE IF NOT EXISTS  $this->tableName (
                            token PRIMARY KEY,
                            created TEXT,
                            expires TEXT
                      )";
        }
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }


    /**
     * Выбрать все записи
     *
     * @return array
     */
    public function getAll(){
        if($this->dbType == Adapter::DB_SQLITE) {
            $query = 'SELECT * FROM ' . $this->tableName;
        }elseif($this->dbType == Adapter::DB_MYSQL){
            $query = 'SELECT * FROM ' . $this->tableName;
        }
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        if( count($result) > 0 ){
            return $result;
        }
        return null;
    }


    /**
     * Удалить простроченные
     *
     * @return array
     */
    public function deleteExpired(){
        if($this->dbType == Adapter::DB_SQLITE) {
            $query = 'DELETE FROM ' . $this->tableName . ' WHERE expires < datetime("now")';
        }elseif($this->dbType == Adapter::DB_MYSQL) {
            $query = 'DELETE FROM ' . $this->tableName . ' WHERE expires < NOW()';
        }
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }

    /**
     * Проверяем существование токена
     *
     * @param $token
     * @return bool
     */
    public function checkToken($token){
        if($this->dbType == Adapter::DB_SQLITE) {
            $query = 'SELECT token
                FROM ' . $this->tableName . '
                WHERE token = :token AND expires > datetime("now")';
        }elseif($this->dbType == Adapter::DB_MYSQL){
            $query = 'SELECT token
                FROM ' . $this->tableName . '
                WHERE token = :token AND expires > NOW()';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':token', $token );
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_OBJ);
        if( count($result) > 0 ){
            return true;
        }
        return false;
    }

    /**
     * Создаем токен
     *
     * @param $token
     * @param $expired
     * @return bool
     */
    public function createToken($token, $expired){

        // периодически чистим таблицу
        // с просрочеными токенами
        $chance = rand(1, 10);
        if( $chance == 5 ){
            $this->deleteExpired();
        }

        if($this->dbType == Adapter::DB_SQLITE) {
            $query = 'INSERT INTO '.$this->tableName.'(token, created, expires) VALUES
                            (:token, datetime("now"), datetime("now", "+' . $expired . ' minutes"))';
        }elseif($this->dbType == Adapter::DB_MYSQL) {
            $query = 'INSERT INTO ' . $this->tableName . '(token, created, expires) VALUES
                            (:token, NOW(), ( NOW() + INTERVAL ' . $expired . ' MINUTE ))';
        }

        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':token', $token );
        return $sth->execute();
    }


    public function drop()
    {
        $query = "DROP TABLE " . $this->getTableName();
        $sth = $this->pdo->prepare( $query );
        return $sth->execute();
    }


}