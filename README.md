# Orion 🛰️
A PHP APM

## Config options

```ini
; required
storage_type='mysql'
; turns off default tracking
default_listeners=true
; optional, if missing executions are all logged
execution_slow_threshold=3000

[database]
host='your host'
db='your db'
user='your user'
pass='your pass'

; wip still need to implement
ignore_warnings=false
ignore_notices=false
ignore_deprecated=false
ignore_css_requests=false
ignore_js_requests=false
ignore_image_requests=false

```

## Adding to App

Add repo to composer.json

```
"repositories": [
	{
		"type": "vcs",
		"url": "git@github.com:orion-php/orion.git"
	}
]
```

Run `composer update orion-php/orion`


Add to Application
```php
use Orion\Orion;

$orion_config = parse_ini_file('orion_config.ini', true);
Orion::getInstance($orion_config)->enable();
```

This enables tracking for:

orion_event
- Fatal Errors
- Warnings
- Notices
- Deprecated
- User tracking
- Total Execution time
- Slow Execution time

orion_historical
- URL tracking

These are default events tracked through a registered shutdown function and set error handler.

## Custom Tracking

Create Listener

```php
<?php
declare(strict_types=1);

namespace Foo\Orion;

use Orion\Orion;
use Foo\Orion\Test_Event;

class Test_Listener {
	protected array $events = [
		Test_Event::class
	];

	public function record($event): void {
		if ($event instanceof Test_Event) {
			Orion::getInstance()->save('test', 'foo_bar', $event->Time->timestamp);
		}
	}
}
```

Create Event

```php
<?php
declare(strict_types=1);

namespace Foo\Orion;

use Orion\Utilities\Time;

class Test_Event {
	
	public Time $Time;

	public function __construct() {
		$this->Time = new Time();
	}
}
```

Add to application and call event
```php
Orion::getInstance()->register([
	Test_Listener::class,
]);

// Do some other stuff

Orion::getInstance()->fire(new Test_Event());
```

## Save methods

- save : Saves to orion_historical (key/value)
- saveEvent : saves to orion_event (key/array) compressed
- saveSeries : saves to orion_series (key/array) save series of events, compressed