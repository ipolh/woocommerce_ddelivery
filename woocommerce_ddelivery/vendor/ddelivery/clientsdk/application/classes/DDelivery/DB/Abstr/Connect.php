<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:04
 */

namespace DDelivery\DB\Abstr;


use DDelivery\DB\ConnectInterface;
use DDelivery\DB\StatementInterface;

abstract class Connect implements ConnectInterface {

    /**
     * Линк на соединение
     */
    protected $linkIdentifier;

    public function __construct($linkIdentifier = null)
    {
        $this->linkIdentifier = $linkIdentifier;
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return (bool)$this->_query("START TRANSACTION");
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return (bool)$this->_query("COMMIT");
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        return (bool)$this->_query("ROLLBACK");
    }

    /**
     * Выполняет SQL запрос и возвращает результирующий набор в виде объекта StatementInterface
     * @param string $query
     * @return StatementInterface
     */
    public function query($query)
    {
        $statement = $this->prepare($query);
        if(!$statement->execute()) {
            return false;
        }
        return $statement;
    }

    /**
     * Выполняет SQL запрос и возвращает resource
     * @param string $query
     * @return resource
     */
    abstract protected function _query($query);

}