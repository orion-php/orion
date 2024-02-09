<?php
declare(strict_types=1);

namespace Orion\Events;

use Orion\Utilities\Time;

class Execution_End {
	public Time $Time;

	public function __construct() {
		$this->Time = new Time();
	}
}
