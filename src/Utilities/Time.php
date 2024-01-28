<?php
declare(strict_types=1);

namespace Orion\Utilities;

class Time {
	protected int $time;
	public string $seconds;
	public string $minutes;
	public string $hours;
	public string $day;
	public string $month;
	public string $year;
	public string $dayOfWeek;
	public string $dayOfYear;
	public string $weekOfYear;
	public string $monthOfYear;
	public string $dateTime;

	public function __construct() {
		$this->time        = time();
		$this->seconds     = date('s', $this->time);
		$this->minutes     = date('i', $this->time);
		$this->hours       = date('H', $this->time);
		$this->day         = date('d', $this->time);
		$this->month       = date('m', $this->time);
		$this->year        = date('Y', $this->time);
		$this->dayOfWeek   = date('w', $this->time);
		$this->dayOfYear   = date('z', $this->time);
		$this->weekOfYear  = date('W', $this->time);
		$this->monthOfYear = date('n', $this->time);
		$this->dateTime    = date('Y-m-d H:i:s', $this->time);
	}
}