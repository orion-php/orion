<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\User_Event;
use Orion\Events\Route_Event;
use Orion\Utilities\Default_Keys;

class Request_Listener {

	protected array $events = [
		Route_Event::class,
	];
	
	public function record($event) {
		if ($event instanceof Route_Event) {
			$data = [
				'user_ip' => $event->user_ip,
				'url' => $event->url,
			];

			Orion::getInstance()->save(Default_Keys::ROUTE_REQUEST, $event->url, $event->Time->timestamp);
		}

		return;
	}
}