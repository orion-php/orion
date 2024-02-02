<?php
declare(strict_types=1);

namespace Orion\Utilities;

class Time {
	/**
	 * @var int
	 */
	protected int $time;
	
	/**
	 * @var string
	 */
	public string $seconds;
	
	/**
	 * @var string
	 */
	public string $minutes;
	
	/**
	 * @var string
	 */
	public string $hours;
	
	/**
	 * @var string
	 */
	public string $day;
	
	/**
	 * @var string
	 */
	public string $month;
	
	/**
	 * @var string
	 */
	public string $year;
	
	/**
	 * @var string
	 */
	public string $day_of_week;
	
	/**
	 * @var string
	 */
	public string $day_of_year;
	
	/**
	 * @var string
	 */
	public string $week_of_year;
	
	/**
	 * @var string
	 */
	public string $month_of_year;
	
	/**
	 * @var string
	 */
	public string $date_time;

	/**
	 * @var int
	 */
	public int $timestamp;

	/**
	 * Construct
	 * 
	 * @return void
	 */
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
		$this->timestamp   = $this->time;
	}
}