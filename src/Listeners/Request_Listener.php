<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\Route_Event;
use Orion\Utilities\Default_Keys;

class Request_Listener {

	protected array $events = [
		Route_Event::class,
	];
	
	public function record($event) {
		if ($event instanceof Route_Event) {
			Orion::getInstance()->save(Default_Keys::ROUTE_REQUEST, $event->url, $event->Time->timestamp);
		}

		return;
	}
}