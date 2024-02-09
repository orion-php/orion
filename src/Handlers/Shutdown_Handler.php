<?php
declare(strict_types=1);

namespace Orion\Handlers;

use Orion\Orion;

class Shutdown_Handler {

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
	 * Shutdown handler
	 * 
	 * @return void
	 */
	public function registerShutdownHandler(): void {
		// register a shutdown handler to catch fatals and dispatch specific events like execution end
	}
}