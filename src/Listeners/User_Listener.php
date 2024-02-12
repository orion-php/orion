<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\User_Event;
use Orion\Utilities\Default_Keys;

class User_Listener {
	
	protected array $events = [
		User_Event::class,
	];

	public function record($event): void {
		$data = [
			'user_ip' => $event->user_ip,
			'url' => $event->url,
		];

		Orion::getInstance()->saveEvent(Default_Keys::USER_IP, $data, $event->Time->timestamp);
	}
}