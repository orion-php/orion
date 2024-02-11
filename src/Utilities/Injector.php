<?php
declare(strict_types=1);

namespace Orion\Utilities;

use ReflectionClass;
use Orion\Orion;

class Injector{
	
	/**
	 * @var array $resolved_classes
	 */
	protected array $resolved_classes = [];

	/**
	 * @var array $registered_classes
	 */
	protected array $registered_classes = [];

	/**
	 * Constructor
	 *
	 * @param array $registered_classes registered classes
	 */
	public function __construct(array $registered_classes = []) {
		$this->registered_classes = $registered_classes;
	}

	/**
	 * Registers a class instance
	 *
	 * @param string $class_name class name
	 * @param object $instance   instance
	 * @return void
	 */
	public function register(string $class_name, object $instance) {
		$this->registered_classes[$class_name] = $instance;
	}

	/**
	 * Resolves a class instance
	 *
	 * @param string $class_name   class name
	 * @param array  $dependencies dependencies
	 * @return object
	 */
	public function resolve(string $class_name, array $dependencies = [], $data = null): object {
		if (!empty($this->resolved_classes[$class_name])) {
			return $this->resolved_classes[$class_name];
		}

		if (!empty($this->registered_classes[$class_name])) {
			return $this->registered_classes[$class_name];
		}

		return $this->createFreshInstance($class_name, $dependencies, $data);
	}

	/**
	 * Resolves a fresh class instance
	 *
	 * @param string $class_name   class name
	 * @param array  $dependencies dependencies
	 * @return object
	 */
	public function resolveFresh(string $class_name, array $dependencies = [], $data = null): object {
		return $this->createFreshInstance($class_name, $dependencies, $data);
	}

	/**
	 * Creates a fresh class instance
	 *
	 * @param string $class_name   class name
	 * @param array  $dependencies dependencies
	 * @return object
	 */
	protected function createFreshInstance(string $class_name, array $dependencies, $data = null):object {
		$dependencies[Injector::class] = $this;

		$reflection  = new ReflectionClass($class_name);
		$constructor = $reflection->getConstructor();

		if (empty($constructor)) {
			if (!is_null($data)) {
				$instance = new $class_name($data);
			} else {
				$instance = new $class_name();
			}
		} else {
			$constructor_params = $constructor->getParameters();
			$constructor_args   = [];

			foreach ($constructor_params as $param) {
				$param_class = $param->getType()->getName(); // @phpstan-ignore-line
				$param_name  = $param->getName();

				if (!empty($dependencies[$param_class])) {
					$constructor_args[] = $dependencies[$param_class];
					continue;
				} else if (!empty($param_class) && class_exists($param_class)) {
					$constructor_args[] = $this->resolve($param_class);
					continue;
				} else if (!empty($this->registered_classes[$param_class])) {
					$constructor_args[] = $this->registered_classes[$param_class];
					continue;
				} else {
					continue;
				}
			}
			
			if (!is_null($data)) {
				$constructor_args[] = $data;
			}

			$instance = $reflection->newInstanceArgs($constructor_args);
		}

		$this->resolved_classes[$class_name] = $instance;

		return $instance;
	}

}