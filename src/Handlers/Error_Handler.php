<?php
declare(strict_types=1);

namespace Orion\Handlers;

use Orion\Orion;

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
	}
}