<?php
/**
 * @author dmz9 <dmz9@yandex.ru>
 * @copyright 2017 http://ipolh.com
 * @licence MIT
 */
namespace WPWooCommerceDDelivery;

use DDelivery\Storage\LogStorageInterface;

class WPLogStorage implements LogStorageInterface
{
	private $logFilePath;
	
	public function __construct()
	{
		$this->logFilePath = rtrim(
				dirname(dirname(__FILE__)),
				'/\\'
			) . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'logstorage.log';

	}
	
	/**
	 * Создаем хранилище
	 *
	 * @return bool
	 */
	public function createStorage()
	{
		return true;
	}
	
	public function getAllLogs()
	{
		return file_get_contents($this->logFilePath);
	}
	
	public function saveLog($content)
	{
		$formatted = date('Y-m-d h.m.s') . "\t" . (string)$content . "\r";
		file_put_contents(
			$this->logFilePath,
			$formatted,
			FILE_APPEND
		);
	}
	
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return null;
	}
	
	public function deleteLogs()
	{
		@file_put_contents(
			$this->logFilePath,
			''
		);
	}
	
	public function drop()
	{
		return true;
	}
}