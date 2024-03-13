<?php
declare(strict_types=1);

namespace Orion\Storage;

use PDO;
use Orion\Orion;
use Orion\Storage\Storage_Interface;
use Orion\Exceptions\Orion_Storage_Exception;

class Mysql_Storage implements Storage_Interface {

	/**
	 * Database connection
	 * 
	 * @var PDO
	 */
	protected PDO $Database;

	/**
	 * Constructor
	 * 
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->Database = new PDO("mysql:host={$config['host']};dbname={$config['db']}", $config['user'], $config['pass']);
	}

	/**
	 * Save data
	 * 
	 * @param string $table
	 * @param string $key
	 * @param string unique_id
	 * @param string $value
	 * @param int $timestamp
	 * @return void
	 */
	public function save(string $table, string $key, string $unique_id, string $value, int $timestamp): void {

		if ($table === Orion::SERIES_TABLE) {
			throw new Orion_Storage_Exception("Use saveSeries to save data to the series table. save should be used for historical or event data.");
		}

		$statement = $this->Database->prepare("INSERT INTO {$table} (unique_id, key_name, data_value, created) VALUES (:unique_id, :key_name, :data_value, :created)");
		$statement->execute([
			':unique_id' => $unique_id,
			':key_name' => $key,
			':data_value' => $value,
			':created' => date("Y-m-d H:i:s", $timestamp),
		]);
		return;
	}

	/**
	 * Save series data
	 * 
	 * @param string $series_id
	 * @param string $key
	 * @param string $unique_id
	 * @param string $value
	 * @param int $timestamp
	 * @return void
	 */
	public function saveSeries(string $series_id, string $key, string $unique_id, string $value, int $timestamp): void {
		$table = Orion::SERIES_TABLE;
		$statement = $this->Database->prepare("INSERT INTO {$table} (series_id, unique_id, key_name, data_value, created) VALUES (:series_id, :unique_id, :key_name, :data_value, :created)");
		$statement->execute([
			':series_id' => $series_id,
			':unique_id' => $unique_id,
			':key_name' => $key,
			':data_value' => $value,
			':created' => date("Y-m-d H:i:s", $timestamp),
		]);
		return;
	}
}