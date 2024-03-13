<?php
declare(strict_types=1);

namespace Orion\Handlers;

use Orion\Orion;
use Orion\Events\Warning_Event;
use Orion\Events\Notice_Event;
use Orion\Events\Deprecated_Event;

class Error_Handler {

	/**
	 * Error handler
	 * 
	 * @return void
	 */
	public function registerErrorHandler(): void {
		set_error_handler(function($errno, $errstr, $errfile, $errline){
			$Orion = Orion::getInstance();
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
					break;
				case E_WARNING:
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
				case E_USER_WARNING:
					$Orion->fire($Orion->Injector->resolve(Warning_Event::class, [], [$error]));
					break;
				case E_NOTICE:
				case E_USER_NOTICE:
					$Orion->fire($Orion->Injector->resolve(Notice_Event::class, [], [$error]));
					break;
				case E_DEPRECATED:
				case E_USER_DEPRECATED:
					$Orion->fire($Orion->Injector->resolve(Deprecated_Event::class, [], [$error]));
					break;
				default:
					break;
			}

			return false;
		});
	}
}