<?php
declare(strict_types=1);

namespace Orion\Handlers;

use Orion\Orion;
use Orion\Events\Execution_End;
use Orion\Events\Fatal_Error_Event;

class Shutdown_Handler {

	/**
	 * Shutdown handler
	 * 
	 * @return void
	 */
	public function registerShutdownHandler(): void {
		register_shutdown_function(function() {
			$Orion = Orion::getInstance();
			$error = error_get_last();

			if (!empty($error)) {
				switch($error['type']) {
					case E_ERROR:
					case E_CORE_ERROR:
					case E_COMPILE_ERROR:
					case E_USER_ERROR:
						$Orion->fire($Orion->Injector->resolve(Fatal_Error_Event::class, [], [$error]));
						break;
					default:
						break;
				}
			}

			$Orion->fire($Orion->Injector->resolve(Execution_End::class));

			return;
		});
	}
}