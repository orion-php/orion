<?php
declare(strict_types=1);

namespace Orion\Events;

use Orion\Utilities\Time;

class Route_Event {
	
	public Time $Time;

	public string $url;

	public function __construct(array $server) {
		$this->Time = new Time();
		$this->url = $server['REQUEST_URI'];
	}
}