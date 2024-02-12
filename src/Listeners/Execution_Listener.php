<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\Execution_Start;
use Orion\Events\Execution_End;
use Orion\Utilities\Default_Keys;

class Execution_Listener {

	/**
	 * Events to listen for
	 * 
	 * @var array
	 */
	public array $events = [
		Execution_Start::class,
	];

	/**
	 * Start time
	 * 
	 * @var int
	 */
	protected float $start_time;

	/**
	 * Record the event
	 * 
	 * @param object $event
	 * @return void
	 */
	public function record($event): void {
		if ($event instanceof Execution_Start && !isset($this->start_time)) {
			$this->start_time = $event->Time->microtime;
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
		$end_time = $event->Time->microtime;
		$execution_time = round(($end_time - $this->start_time), 2);
		$data = [
			'execution_time' => $execution_time,
			'end_time' => $end_time,
			'start_time' => $this->start_time,
			'request' => $_SERVER['REQUEST_URI']
		];

		$config = Orion::getInstance()->config;

		if (!empty($config['execution_slow_threshold'])) {
			if ($execution_time > $config['execution_slow_threshold']) {
				Orion::getInstance()->saveEvent(Default_Keys::SLOW_EXECUTION, $data, $event->Time->timestamp);
			}
			return;
		}

		Orion::getInstance()->saveEvent(Default_Keys::EXECUTION, $data, $event->Time->timestamp);

		return;
	}
}