<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:04
 */

namespace DDelivery\DB\Mysql;

class Connect extends \DDelivery\DB\Abstr\Connect {

    /**
     * Подготавливает запрос к выполнению и возвращает ассоциированный с этим запросом объект
     * @param string $statement
     * @return Statement
     */
    public function prepare($statement)
    {
        return new Statement($statement, $this->linkIdentifier);
    }


    /**
     * Выполняет SQL запрос и возвращает resource
     * @param string $query
     * @return resource
     */
    protected function _query($query)
    {
        return mysql_query($query, $this->linkIdentifier);
    }

    /**
     * Возвращает ID последней вставленной строки или последовательное значение
     * @return string
     */
    public function lastInsertId()
    {
        return mysql_insert_id($this->linkIdentifier);
    }

    /**
     * Запускает SQL запрос на выполнение и возвращает количество строк, задействованых в ходе его выполнения
     * @param string $query
     * @return int
     */
    public function exec($query)
    {
        if(!$this->_query($query)){
            return false;
        }
        return mysql_affected_rows($this->linkIdentifier);
    }



}