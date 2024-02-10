<?php
declare(strict_types=1);

namespace Orion\Events;

use Orion\Utilities\Time;

class User_Event {
	
	public Time $Time;

	public string $user_ip;

	public string $url;

	public function __construct(array $server) {
		$this->Time = new Time();
		$this->user_ip = $server['REMOTE_ADDR'];
		$this->url = $server['REQUEST_URI'];
	}
}