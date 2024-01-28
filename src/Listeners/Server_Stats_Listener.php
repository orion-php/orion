<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Events\Beacon;

class Server_Stats_Listener {
	public array $events = [
		Beacon::class
	];

	public function record($event): void {
		if ($event instanceof Beacon) {
			$this->beaconEvent($event);
		}
	}

	protected function beaconEvent(Beacon $event): void {
		if ($event->time->seconds % 5 !== 0) {
			return;
		}

		// do something else
	}
}