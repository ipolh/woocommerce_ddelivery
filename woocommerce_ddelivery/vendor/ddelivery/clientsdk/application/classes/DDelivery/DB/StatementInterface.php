<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:32
 */

namespace DDelivery\DB;

use DDelivery\DB\ConstPDO as PDO;

interface StatementInterface {

    /**
     * Запускает подготовленный запрос на выполнение
     * @param array $inputParameters
     * @return bool
     */
    public function execute($inputParameters = array());

    /**
     * Привязывает параметр запроса к переменной
     * @param mixed $parameter
     * @param mixed $variable
     * @param int $dataType
     * @return bool
     */
    public function bindParam($parameter, $variable, $dataType = PDO::PARAM_STR);

    /**
     *
     * @param int $fetchStyle ConstPDO::FETCH_*
     * @return array
     */
    public function fetchAll($fetchStyle);

    /**
     * @param int $fetchStyle ConstPDO::FETCH_*
     * @return mixed
     */
    public function fetch($fetchStyle);
}