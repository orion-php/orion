<?php
declare(strict_types=1);

namespace Orion\Listeners;

use Orion\Orion;
use Orion\Events\Fatal_Error_Event;
use Orion\Events\Notice_Event;
use Orion\Events\Deprecated_Event;
use Orion\Events\Warning_Event;

class Warning_Listener {
	
	protected array $events = [
		Warning_Event::class,
		Notice_Event::class,
		Deprecated_Event::class,
		Fatal_Error_Event::class,
	];

	protected array $types = [
		'1' => 'Fatal Error',
		'16' => 'Core Error',
		'64' => 'Compile Error',
		'256' => 'User Error',
		'2' => 'Warning',
		'32' => 'Core Warning',
		'128' => 'Compile Warning',
		'512' => 'User Warning',
		'8' => 'Notice',
		'1024' => 'User Notice',
		'2048' => 'Deprecated',
		'8192' => 'User Deprecated',
	];

	public function record($event): void {
		$error_name = $this->types[$event->event['type']];
		$error_message = $event->event['message'];
		$error_file = $event->event['file'] . 'LINE: ' . $event->event['line'];

		$data = [
			'error_name' => $error_name,
			'error_message' => $error_message,
			'error_file' => $error_file,
		];

		switch($event['type']) {
			case '1':
			case '16':
			case '64':
			case '256':
				Orion::getInstance()->saveEvent('fatal_error', $data, $event->Time->timestamp);
				break;
			case '2':
			case '32':
			case '128':
			case '512':
				Orion::getInstance()->saveEvent('warning', $data, $event->Time->timestamp);
				break;
			case '8':
			case '1024':
				Orion::getInstance()->saveEvent('notice', $data, $event->Time->timestamp);
				break;
			case '2048':
			case '8192':
				Orion::getInstance()->saveEvent('deprecated', $data, $event->Time->timestamp);
				break;
		}
	}
}