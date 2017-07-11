<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:04
 */

namespace DDelivery\DB\Mysqli;

class Connect extends \DDelivery\DB\Abstr\Connect {

    public function __construct(\mysqli $mysqli)
    {
        parent::__construct($mysqli);
    }

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
        return mysqli_query($this->linkIdentifier, $query);
    }

    /**
     * Возвращает ID последней вставленной строки или последовательное значение
     * @return string
     */
    public function lastInsertId()
    {
        return mysqli_insert_id($this->linkIdentifier);
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
        return mysqli_affected_rows($this->linkIdentifier);
    }


}