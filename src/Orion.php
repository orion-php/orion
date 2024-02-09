<?php
declare(strict_types=1);
namespace Orion;

use Exception;
use ReflectionClass;
use Orion\Storage\Mysql_Storage;
use Orion\Storage\Storage_Interface;
use Orion\Utilities\Compression;
use Orion\Utilities\Time;
use Orion\Exceptions\Orion_Exception;
use Orion\Handlers\Error_Handler;
use Orion\Handlers\Shutdown_Handler;
use Orion\Utilities\Injector;

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
	 * Injector
	 * 
	 * @var Injector
	 */
	public Injector $Injector;

	/**
	 * Time
	 * 
	 * @var Time
	 */
	protected Time $Time;

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

	/**
	 * Constructor
	 * 
	 * @param array $config
	 */
	protected function __construct(array $config) { 
		$this->time = new Time;
		if (!empty($config)) {
			foreach ($config as $key => $value) {
				if (isset($this->config[$key])) {
					continue;
				}
				$this->config[$key] = $value;
			}
		}

		$this->Injector = new Injector;
		$this->enable();
		$this->dispatchPostEnableEvents();
	}

	/**
	 * Enable Orion
	 * 
	 * @return void
	 */
	protected function enable(): void {

		$this->configureStorage();

		// Set up error and shutdown handlers
		$Error_Handler = $this->Injector->resolve(Error_Handler::class);
		$Error_Handler->registerErrorHandler();

		$Shutdown_Handler = $this->Injector->resolve(Shutdown_Handler::class);
		$Shutdown_Handler->registerShutdownHandler();

		// Register default listeners
		// TODO: add listener for route requested, logged in user, anon user etc
		// TODO: ignore this if config is set to ignore, ie cron for beacon event probably won't need this
		$this->register([
			Execution_Listener::class,
		]);

		$this->callOnEnabledEvents();

		return;
	}

	/**
	 * Call on enabled events
	 * 
	 * @return void
	 */
	protected function callOnEnabledEvents(): void {
		$on_enable_events = [
			Execution_Start::class => [],
		];

		foreach ($on_enable_events as $event => $dependencies) {
			$this->fire($this->Injector->resolve($event, $dependencies));
		}
	}

	/**
	 * Dispatch post enable events
	 * 
	 * @return void
	 */	
	protected function dispatchPostEnableEvents(): void {
		$post_enable_events = [
			// example: Execution_End::class => [],
		];

		foreach ($post_enable_events as $event => $dependencies) {
			$this->fire($this->Injector->resolve($event, $dependencies));
		}
	}

	/**
	 * Configure the storage
	 * 
	 * @return void
	 */
	protected function configureStorage(): void {
		if (empty($this->config['storage_type'])) {
			throw new Orion_Exception("Storage type not set");
		}

		switch($this->config['storage_type']) {
			case 'mysql':
				$this->Storage = $this->Injector->resolve(Mysql_Storage::class, $this->config['database']);
				break;
			default:
				throw new Orion_Exception("Database type not supported");
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
	 * TODO: would this be better to fire off with exec() so it is more "async"?
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

		unset($this->queued[$event_class]);

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