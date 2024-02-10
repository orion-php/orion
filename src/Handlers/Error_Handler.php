<?php
declare(strict_types=1);

namespace Orion\Handlers;

use Orion\Orion;
use Orion\Events\Fatal_Error_Event;
use Orion\Events\Warning_Event;
use Orion\Events\Notice_Event;
use Orion\Events\Deprecated_Event;

class Error_Handler {

	/**
	 * Instance of Orion
	 * 
	 * @var Orion
	 */
	protected Orion $Orion;

	/**
	 * Construct
	 * 
	 * @param Orion $Orion
	 * @return void
	 */
	public function __construct(Orion $Orion) {
		$this->Orion = $Orion::getInstance();
	}

	/**
	 * Error handler
	 * 
	 * @return void
	 */
	public function registerErrorHandler(): void {
		// register an error handler to catch errors, notices, warnings etc
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			$error = [
				'type' => $errno,
				'message' => htmlspecialchars($errstr),
				'file' => $errfile,
				'line' => $errline,
			];

			switch($errno) {
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					$this->Orion->fire($this->Orion->Injector->resolve(Fatal_Error_Event::class, [$error]));
					break;
				case E_WARNING:
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
				case E_USER_WARNING:
					$this->Orion->fire($this->Orion->Injector->resolve(Warning_Event::class, [$error]));
					break;
				case E_NOTICE:
				case E_USER_NOTICE:
					$this->Orion->fire($this->Orion->Injector->resolve(Notice_Event::class, [$error]));
					break;
				case E_DEPRECATED:
				case E_USER_DEPRECATED:
					$this->Orion->fire($this->Orion->Injector->resolve(Deprecated_Event::class, [$error]));
					break;
				default:
					break;
			}

			return false;
		});
	}
}