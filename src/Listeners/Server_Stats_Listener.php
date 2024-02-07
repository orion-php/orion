<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Events\Beacon;
use Orion\Orion;

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
		
		return;
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

		$Orion = Orion::getInstance();
		$Orion->save('cpu_use', $this->getCpuUsage(), $event->Time->timestamp);
		$Orion->save('ram_use', $this->getRamUsage(), $event->Time->timestamp);

		return;
	}

	/**
	 * Get the CPU usage
	 * 
	 * @return float
	 */
	protected function getCpuUsage(): float {
		$cpu_usage = shell_exec("top -bn1 | grep -E '^(%Cpu|CPU)' | awk '{ print $2 + $4 }'");
		return round((float) $cpu_usage, 2);
	}

	/**
	 * Get the free ram
	 * 
	 * @return float
	 */
	protected function getRamUsage(): float {
		$free_result = shell_exec("free -m | grep -E 'Mem:' | awk '{ print ($3/$2) * 100 }'");
		return round((float) $free_result, 2);
	}

}