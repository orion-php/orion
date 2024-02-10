<?php
declare(strict_types=1);

namespace Orion\Events;

use Orion\Utilities\Time;

class Deprecated_Event {
	
	public Time $Time;

	public array $event;

	public function __construct(array $event) {
		$this->Time = new Time();
		$this->event = $event;
	}
}