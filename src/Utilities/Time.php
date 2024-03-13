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
	 * @var float
	 */
	public float $microtime;

	/**
	 * Construct
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->time          = time();
		$this->seconds       = date('s', $this->time);
		$this->minutes       = date('i', $this->time);
		$this->hours         = date('H', $this->time);
		$this->day           = date('d', $this->time);
		$this->month         = date('m', $this->time);
		$this->year          = date('Y', $this->time);
		$this->day_of_week   = date('w', $this->time);
		$this->day_of_year   = date('z', $this->time);
		$this->week_of_year  = date('W', $this->time);
		$this->month_of_year = date('n', $this->time);
		$this->date_time     = date('Y-m-d H:i:s', $this->time);
		$this->timestamp     = $this->time;
		$this->microtime     = microtime(true);
	}
}