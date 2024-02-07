<?php
declare(strict_types=1);
namespace Orion;

use Exception;
use ReflectionClass;
use Orion\Storage\Mysql_Storage;
use Orion\Storage\Storage_Interface;
use Orion\Utilities\Compression;

class Orion {

	const HISTORICAL_TABLE = 'orion_historical';
	const EVENT_TABLE = 'orion_event';
	const SERIES_TABLE = 'orion_series';

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
	 * Storage
	 * 
	 * @var Storage_Interface
	 */
	protected Storage_Interface $Storage;

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
			foreach ($config as $key => $value) {
				if (isset($this->config[$key])) {
					continue;
				}
				$this->config[$key] = $value;
			}
		}

		$this->configureStorage();
	}

	/**
	 * Configure the storage
	 * 
	 * @return void
	 */
	protected function configureStorage(): void {
		switch($this->config['database']['type']) {
			case 'mysql':
				$this->Storage = new Mysql_Storage($this->config['database']);
				break;
			default:
				throw new Exception("Database type not supported");
		}
		return;
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

	/**
	 * Wait for a future event to be fired, store the listener in a queue to be finished
	 * 
	 * @param string $future_event event class name
	 * @param object $listener     listener object
	 * @return void
	 */
	public function wait(string $future_event, object $listener): void {
		$this->queued[$future_event][] = $listener;
		return;
	}

	/**
	 * Saves point in time data ie cpu usage, ram usage
	 * 
	 * Data is not compressed on insert
	 * 
	 * @param string $key_name
	 * @param mixed $data
	 * @param int $timestamp
	 * @return void
	 */
	public function save(string $key_name, $data, int $timestamp): void {
		$this->Storage->save(self::HISTORICAL_TABLE, $key_name, $this->uniqueId(), $data, $timestamp);
		return;
	}

	/**
	 * Saves an events data ie  page load time, user interaction
	 * 
	 * Data is compressed on insert
	 * 
	 * @param string $key_name
	 * @param array $data
	 * @param int $timestamp
	 * @return void
	 */
	public function saveEvent(string $key_name, array $data, int $timestamp): void {
		$data_compressed = Compression::compress(json_encode($data));
		$this->Storage->save(self::EVENT_TABLE, $key_name, $this->uniqueId(), $data_compressed, $timestamp);
		return;
	}

	/**
	 * Saves a series of related events ie: user interactions over time
	 * 
	 * Data is compressed on insert
	 * 
	 * @param string $key_name
	 * @param array $data
	 * @param int $timestamp
	 * @param string $series_id
	 * @return void
	 */
	public function saveSeries(string $key_name, array $data, int $timestamp, string $series_id = ''): void {
		$data_compressed = Compression::compress(json_encode($data));
		$this->Storage->saveSeries($series_id, $key_name, $this->uniqueId(), $data_compressed, $timestamp);
		return;
	}

	/**
	 * Generate a unique id
	 * 
	 * @return string
	 */
	protected function uniqueId(): string {
		return bin2hex(openssl_random_pseudo_bytes(12));
	}
}