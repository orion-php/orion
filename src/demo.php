<?php
declare(strict_types=1);
require_once 'vendor/autoload.php';

use Orion\Orion;
use Orion\Listeners\Server_Stats_Listener;
use Orion\Events\Beacon;

$Orion = Orion::getInstance([
	'db' => 'foo',
	'bar' => 'baz'
]);

// Register listeners that care about the Beacon event
$Orion->register([
	Server_Stats_Listener::class
]);

// cycles through and fires the Beacon event every second, this would normally be a cron
while(true) {
	sleep(1);
	$Orion->fire(new Beacon());
}
