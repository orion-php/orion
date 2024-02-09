<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\Execution_Start;
use Orion\Events\Execution_End;

class Execution_Listener {

	/**
	 * Events to listen for
	 * 
	 * @var array
	 */
	protected array $events = [
		Execution_Start::class,
		Execution_End::class,
	];

	/**
	 * Start time
	 * 
	 * @var int
	 */
	protected int $start_time;

	/**
	 * Record the event
	 * 
	 * @param object $event
	 * @return void
	 */
	public function record($event): void {
		if ($event instanceof Execution_Start && !isset($this->start_time)) {
			$this->start_time = $event->Time->timestamp;
			
			Orion::getInstance()->wait(Execution_End::class, $this);
			
			return;
		}
		
		$this->endEvent($event);

		return;
	}

	/**
	 * End event
	 * 
	 * @param Execution_End $event
	 * @return void
	 */
	protected function endEvent(Execution_End $event): void {
		$end_time = $event->Time->timestamp;
		$execution_time = ($end_time - $this->start_time) / 1000;
		$data = [
			'execution_time' => $execution_time,
			'end_time' => $end_time,
			'start_time' => $this->start_time,
			'request' => $_SERVER['REQUEST_URI'],
		];

		Orion::getInstance()->saveEvent('execution_time', $data, $event->Time->timestamp);

		return;
	}
}