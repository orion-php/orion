<?php
declare(strict_types=1);
namespace Orion;

use Exception;
use ReflectionClass;

class Orion {

	/**
	 * Instance of Orion
	 * 
	 * @var Orion
	 */
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
	 * Configuration
	 * 
	 * @var array
	 */
	protected array $config = [];

	/**
	 *Prevent the instance from being cloned
	 */
	protected function __clone() { }

	/**
	 * Prevent from being unserialized
	 */
	public function __wakeup() {
		throw new Exception("I'm a singleton, you're in danger!");
	}

	protected function __construct(array $config) { 
		if (!empty($config)) {
			// TODO: make sure config is not already set
			$this->config = $config;
		}
	}

	/**
	 * Get instance of Orion
	 * 
	 * @return Orion
	 */
	public static function getInstance($config = []): Orion {
		if (!isset(self::$instance)) {
			self::$instance = new static($config);
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
		array_map(
			function($listener) {
				$reflection = new ReflectionClass($listener);
				$events = $reflection->getProperty('events');
				$events = $events->getValue(new $listener());
				foreach ($events as $event) {
					$this->listeners[$event][] = $listener;
				}
			}, 
			$listeners
		);
	}

	/**
	 * Fire an event, calling all listeners that are registered to that event
	 * 
	 * @param object $event
	 * @return void
	 */
	public function fire($event): void {
		$event_class = get_class($event);

		if (isset($this->queued[$event_class])) {
			$this->processQueuedEvents($event_class, $event);
		}

		if (isset($this->listeners[$event_class])) {
			$this->processEvents($event_class, $event);
		}

		return;
	}

	/**
	 * Processed queued listeners, listeners that start with on one event and end with another, ie: latency listeners
	 * 
	 * @param class-string $event_class
	 * @param object $event
	 * @return void
	 */
	protected function processQueuedEvents(string $event_class, object $event): void {
		foreach($this->queued[$event_class] as $listener) {
			$listener = new $listener();
			$listener->record($event);
		}

		return;
	}

	/**
	 * Processed listeners that are registered to an event
	 * 
	 * @param class-string $event_class
	 * @param object $event
	 * @return void
	 */
	protected function processEvents(string $event_class, object $event): void {
		foreach ($this->listeners[$event_class] as $listener) {
			$listener = new $listener();
			$listener->record($event);
		}
		
		return;
	}
}