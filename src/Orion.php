<?php
declare(strict_types=1);

namespace Orion;

use Exception;

class Orion {

	private static $instance;

	/**
	 * Listeners to call when an event is fired
	 * 
	 * @var array
	 */
	protected array $listeners = [];

	/**
	 * Listeners that are started and waiting for an event to complete to be resolved
	 * 
	 * @var array
	 */
	protected array $queued = [];

	/**
	 *Prevent the instance from being cloned
	 */
	protected function __clone() { }

	/**
	 * Prevent from being unserialized
	 */
	public function __wakeup() {
		throw new Exception("I'm a singleton, I'm in danger!");
	}

	protected function __construct() { 
		// we might need a thing or two here
	}

	/**
	 * Get instance of Orion
	 * 
	 * @return Orion
	 */
	public static function getInstance(): Orion {
		if (!isset(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}
}