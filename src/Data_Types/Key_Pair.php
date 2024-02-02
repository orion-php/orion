<?php
declare(strict_types=1);

namespace Orion\Data_Types;

//meh
class Key_Pair {

	/**
	 * @var string
	 */
	public string $storage_key;
	
	/**
	 * @var string
	 */
	protected string $key;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * Construct
	 * 
	 * @param string $key         The key name for the value
	 * @param mixed $value        The value to store
	 * @param string $storage_key The name to store the key/value pair under in database if applicable
	 */
	public function __construct(string $key, $value, $storage_key = '') {
		$this->key = $key;
		$this->value = $value;
		$this->storage_key = $storage_key;
	}

	/**
	 * Get the key and value
	 * 
	 * @return array
	 */
	public function unwrap(): array {
		return [
			$this->key,
			$this->value
		];
	}

	/**
	 * Compress the key/value pair
	 * 
	 * @return string
	 */
	public function compress():string {
		return gzcompress(json_encode($this->unwrap()));
	}

	/**
	 * Decompress the key/value pair
	 * 
	 * @param string $compressed
	 * @return self
	 */
	public function decompress($compressed):self {
		$uncompressed = gzuncompress($compressed);
		[$this->key, $this->value] = json_decode($uncompressed, true);
		
		return $this;
	}
}