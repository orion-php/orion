<?php
declare(strict_types=1);

namespace Orion\Data_Types;

use Orion\Data_Types\Data_Object;

//meh
class Series_Object {

	/**
	 * @var string
	 */
	public string $storage_key;

	/**
	 * @var int
	 */
	public int $time;

	/**
	 * @var array
	 */
	protected array $data_series = [];

	/**
	 * Construct
	 * 
	 * @param string $storage_key The name to store in database if applicable
	 */
	public function __construct($storage_key = '') {
		$this->storage_key = $storage_key;
	}

	/**
	 * Set an object
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set(Data_Object $Data_Object): void {
		$this->data_series[] = $Data_Object;
	}

	/**
	 * extract all data
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function extract(): array {
		$data = [];

		foreach ($this->data_series as $Data_Object) {
			$data[] = $Data_Object->extract();
		}

		return $data;
	}

	/**
	 * Compress the data
	 * 
	 * @return string
	 */
	public function compress(): string {
		return gzcompress(json_encode($this->data_series));
	}

	/**
	 * Decompress the data and return it
	 * 
	 * @param string $compressed
	 * @return void
	 */
	public function decompress($compressed):void {
		$uncompressed = gzuncompress($compressed);
		$data = json_decode($uncompressed, true);
		$this->data_series = $data;
		return;
	}
}