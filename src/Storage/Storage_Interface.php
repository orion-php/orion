<?php
declare(strict_types=1);

namespace Orion\Storage;

interface Storage_Interface {
	
	/**
	 * Save data
	 * 
	 * @param string $table
	 * @param string $key
	 * @param string $unique_id
	 * @param string $value
	 * @param int    $timestamp
	 * @return void
	 */
	public function save(string $table, string $key, string $unique_id, string $value, int $timestamp): void;

	/**
	 * Save series data
	 * 
	 * @param string $series_id
	 * @param string $key
	 * @param string $unique_id
	 * @param string $value
	 * @param int    $timestamp
	 * @return void
	 */
	public function saveSeries(string $series_id, string $key, string $unique_id, string $value, int $timestamp): void;
}