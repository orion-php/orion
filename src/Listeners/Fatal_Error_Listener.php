<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\Fatal_Error_Event;
use Orion\Utilities\Default_Keys;

class Fatal_Error_Listener {
	/**
	 * Events to listen for
	 * 
	 * @var array
	 */
	protected array $events = [
		Fatal_Error_Event::class,
	];

	protected array $error_types = [
		1 => 'Fatal Error',
		16 => 'Core Error',
		64 => 'Compile Error',
		256 => 'User Error',
	];
	
	/**
	 * Record the event
	 * 
	 * @param object $event
	 * @return void
	 */
	public function record($event): void {
		$error_name = $this->error_types[$event->fatal_event[0]['type']];
		$error_message = $event->fatal_event[0]['message'];
		$error_file = $event->fatal_event[0]['file'] . 'LINE: ' . $event->fatal_event[0]['line'];

		$data = [
			'error_name' => $error_name,
			'error_message' => $error_message,
			'error_file' => $error_file,
		];

		Orion::getInstance()->saveEvent(Default_Keys::FATAL_ERROR, $data, $event->Time->timestamp);
	}
}