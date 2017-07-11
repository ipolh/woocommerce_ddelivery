<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:03
 */

namespace DDelivery\DB;

/**
 * Простая симуляция PDO
 * Interface MysqlInterface
 * @package DDelivery\DB
 */
interface ConnectInterface {
    /**
     * Подготавливает запрос к выполнению и возвращает ассоциированный с этим запросом объект
     * @param string $statement
     * @return StatementInterface
     */
    public function prepare($statement);

    /**
     * @return bool
     */
    public function beginTransaction();

    /**
     * @return bool
     */
    public function commit();

    /**
     * @return bool
     */
    public function rollBack();

    /**
     * Запускает SQL запрос на выполнение и возвращает количество строк, задействованых в ходе его выполнения
     * @param string $query
     * @return int
     */
    public function exec($query);

    /**
     * Выполняет SQL запрос и возвращает результирующий набор в виде объекта StatementInterface
     * @param string $query
     * @return StatementInterface
     */
    public function query($query);

    /**
     * Возвращает ID последней вставленной строки или последовательное значение
     * @return string
     */
    public function lastInsertId();

    /**
     * Заключает строку в кавычки и экранирует специальные символы
     * @param $string
     * @return string
     */
    //public function quote($string);
}
