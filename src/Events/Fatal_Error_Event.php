<?php
declare(strict_types=1);

namespace Orion\Events;

use Orion\Utilities\Time;

class Fatal_Error_Event {
	
	public Time $Time;

	public array $fatal_event;

	public function __construct(array $fatal_event) {
		$this->Time = new Time();
		$this->fatal_event = $fatal_event;
	}
}