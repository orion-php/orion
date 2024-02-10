<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\Fatal_Error_Event;

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
		'1' => 'Fatal Error',
		'16' => 'Core Error',
		'64' => 'Compile Error',
		'256' => 'User Error',
	];
	
	/**
	 * Record the event
	 * 
	 * @param object $event
	 * @return void
	 */
	public function record($event): void {
		$error_name = $this->error_types[$event->fatal_event['type']];
		$error_message = $event->fatal_event['message'];
		$error_file = $event->fatal_event['file'] . 'LINE: ' . $event->fatal_event['line'];

		$data = [
			'error_name' => $error_name,
			'error_message' => $error_message,
			'error_file' => $error_file,
		];

		Orion::getInstance()->saveEvent('fatal_error', $data, $event->Time->timestamp);
	}
}