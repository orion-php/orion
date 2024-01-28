<?php
declare(strict_types=1);
namespace Orion;

include_once 'vendor/autoload.php';

use Exception;
use ReflectionClass;

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

	/**
	 * Register listeners
	 * 
	 * Uses the listeners events property to determine which events to listen for
	 * 
	 * @param array<string> $listeners
	 * @return void
	 */
	public function register(array $listeners): void {
		array_map(function($listener) {
			$reflection = new ReflectionClass($listener);
			$events = $reflection->getProperty('events');
			$events->setAccessible(true);
			$events = $events->getValue(new $listener());
			foreach ($events as $event) {
				$this->listeners[$event][] = $listener;
			}
		}, $listeners);
	}
}