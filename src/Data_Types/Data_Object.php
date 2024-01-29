<?php
declare(strict_types=1);

namespace Orion\Data_Types;

abstract class Data_Object {

	/**
	 * @var string
	 */
	public string $storage_key;

	/**
	 * Construct
	 * 
	 * @param string $storage_key The name to store the key/value pair under in database if applicable
	 */
	public function __construct($storage_key = '') {
		$this->storage_key = $storage_key;
	}

	/**
	 * Set a property
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set(string $key, $value): void {
		$this->{$key} = $value;
	}

	/**
	 * Compress the data
	 * 
	 * @return string
	 */
	public function compress(): string {
		$class_properties = get_object_vars($this);
		$data = [];

		foreach ($class_properties as $key => $value) {
			$data[$key] = $value;
		}

		return gzcompress(json_encode($data));
	}

	/**
	 * Decompress the data
	 * 
	 * @param string $compressed
	 * @return void
	 */
	public function decompress($compressed):void {
		$uncompressed = gzuncompress($compressed);
		$data = json_decode($uncompressed, true);

		foreach ($data as $key => $value) {
			$this->set($key, $value);
		}

		return;
	}
}