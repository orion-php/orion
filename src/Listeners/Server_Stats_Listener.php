<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Events\Beacon;

class Server_Stats_Listener {

	/**
	 * Events to listen for
	 * 
	 * @var array
	 */
	protected array $events = [
		Beacon::class
	];

	/**
	 * Record the event
	 * 
	 * @param object $event
	 * @return void
	 */
	public function record($event): void {
		if ($event instanceof Beacon) {
			$this->beaconEvent($event);
		}
	}

	/**
	 * Beacon event
	 * 
	 * @param Beacon $event
	 * @return void
	 */
	protected function beaconEvent(Beacon $event): void {
		if ($event->Time->seconds % 5 !== 0) {
			return;
		}
		
		// demo poc for beacon event
		echo "From Server_Stats_Listener" . PHP_EOL;
		echo "OS Family: " . PHP_OS_FAMILY . PHP_EOL;
		echo $event->Time->seconds . PHP_EOL;
		return;
	}
}