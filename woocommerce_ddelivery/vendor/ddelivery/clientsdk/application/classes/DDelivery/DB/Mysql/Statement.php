<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:27
 */

namespace DDelivery\DB\Mysql;


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
        if(is_null($variable)){
            $variable = 'NULL';
        }elseif($dataType == PDO::PARAM_INT){
            $variable = (int)$variable;
        }else{ // $dataType == PDO::PARAM_STR
            $variable = '"' . mysql_real_escape_string($variable, $this->linkIdentifier) . '"';
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
        return mysql_query($query, $this->linkIdentifier);
    }

    /**
     * @param int $fetchStyle ConstPDO::FETCH_*
     * @return mixed
     */
    public function fetch($fetchStyle = PDO::FETCH_BOTH)
    {
        switch($fetchStyle){
            case PDO::FETCH_ASSOC:
                return mysql_fetch_array($this->resource, MYSQL_ASSOC);
            case PDO::FETCH_BOTH:
                return mysql_fetch_array($this->resource, MYSQL_BOTH);
            case PDO::FETCH_NUM:
                return mysql_fetch_array($this->resource, MYSQL_NUM);
            case PDO::FETCH_OBJ:
                $res = mysql_fetch_array($this->resource, MYSQL_ASSOC);
                if($res){
                    return (object)$res;
                }
                return false;
        }
        throw new \Exception('Fetch style '.$fetchStyle.' not supported');

    }
}