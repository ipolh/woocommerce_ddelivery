<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:27
 */

namespace DDelivery\DB\Mysqli;


use DDelivery\DB\ConnectInterface;
use DDelivery\DB\StatementInterface;
use DDelivery\DB\ConstPDO as PDO;

class Statement extends \DDelivery\DB\Abstr\Statement {

    /**
     * Привязывает параметр запроса к переменной
     * @param mixed $parameter
     * @param mixed $variable
     * @param int $dataType
     * @throws \Exception
     * @return bool
     */
    public function bindParam($parameter, $variable, $dataType = PDO::PARAM_STR)
    {
        if(!is_array($this->bindParams))
            throw new \Exception('PDO:bindParam can not be called after execute');
        if($dataType == PDO::PARAM_INT){
            $variable = (int)$variable;
        }else{ // $dataType == PDO::PARAM_STR
            $variable = '"' . mysqli_real_escape_string($this->linkIdentifier, $variable) . '"';
        }
        $this->bindParams[$parameter] = $variable;
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
     * @param int $fetchStyle ConstPDO::FETCH_*
     * @return mixed
     */
    public function fetch($fetchStyle = PDO::FETCH_BOTH)
    {
        switch($fetchStyle){
            case PDO::FETCH_ASSOC:
                return mysqli_fetch_array($this->resource, MYSQLI_ASSOC);
            case PDO::FETCH_BOTH:
                return mysqli_fetch_array($this->resource, MYSQLI_BOTH);
            case PDO::FETCH_NUM:
                return mysqli_fetch_array($this->resource, MYSQLI_NUM);
            case PDO::FETCH_OBJ:
                $res = mysqli_fetch_array($this->resource, MYSQLI_ASSOC);
                if($res){
                    return (object)$res;
                }
                return false;
        }
        throw new \Exception('Fetch style '.$fetchStyle.' not supported');

    }
}