<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Events\Beacon;
use Orion\Data_Types\Key_Pair;
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

		$Cpu_Data = new Key_Pair('cpu_use', $this->getCpuUsage());
		$Cpu_Data->time = $event->Time->timestamp;
		
		$Ram_Data = new Key_Pair('ram_use', $this->getRamUsage());
		$Ram_Data->time = $event->Time->timestamp;
		
		// uncomment for demo.php
		// var_dump($Cpu_Data->unwrap());
		// var_dump($Ram_Data->unwrap());
		// echo $event->Time->seconds . PHP_EOL;
		
		$Orion = Orion::getInstance();
		$Orion->savePointInTimeData($Cpu_Data);
		$Orion->savePointInTimeData($Ram_Data);
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