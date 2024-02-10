<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\User_Event;
use Orion\Events\Route_Event;

class Request_Listener {

	protected array $events = [
		User_Event::class,
		Route_Event::class,
	];
	
	public function record() {
		//
	}
}