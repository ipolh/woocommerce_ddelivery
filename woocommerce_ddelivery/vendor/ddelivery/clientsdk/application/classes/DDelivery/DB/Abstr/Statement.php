<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:27
 */

namespace DDelivery\DB\Abstr;


use DDelivery\DB\ConnectInterface;
use DDelivery\DB\StatementInterface;
use DDelivery\DB\ConstPDO as PDO;

abstract class Statement implements StatementInterface {
    protected $linkIdentifier;

    /**
     * @var string
     */
    protected $query;

    protected $bindParams = array();
    protected $resource = false;

    public function __construct($query, $linkIdentifier )
    {
        $this->linkIdentifier = $linkIdentifier;
        $this->query = $query;
    }



    /**
     * Запускает подготовленный запрос на выполнение
     * @param array $inputParameters
     * @throws \Exception
     * @return bool
     */
    public function execute($inputParameters = array())
    {
        foreach($inputParameters as $parameter => $variable){
            $this->bindParam($parameter, $variable);
        }
        $query = $this->query;
        if(!empty($this->bindParams)) {
            $isNumParam = false;
            $isStringParam = false;
            foreach($this->bindParams as $key => $variable) {
                if(is_numeric($key)) {
                    $isNumParam = true;
                }else{
                    $isStringParam = true;
                }
            }
            if($isNumParam && $isStringParam){
                throw new \Exception('Cannot pass parameter 2 by reference');
            }
            if($isNumParam) {
                $query = str_replace( '?', '%s', $query);
                $query = vsprintf($query, array_values($this->bindParams));
            }else {
                $query = str_replace(array_keys($this->bindParams), array_values($this->bindParams), $query);
            }
            $this->bindParams = false;
            $this->query = $query;
        }
        $this->resource = $this->_query($query);
        return (bool)$this->resource;
    }

    /**
     * Выполняет SQL запрос и возвращает resource
     * @param string $query
     * @return resource
     */
    abstract protected function _query($query);


    /**
     *
     * @param int $fetchStyle ConstPDO::FETCH_*
     * @return array
     */
    public function fetchAll($fetchStyle = PDO::FETCH_BOTH)
    {
        $result = array();

        while($res = $this->fetch($fetchStyle)){
            $result[] = $res;
        }
        return $result;
    }

}