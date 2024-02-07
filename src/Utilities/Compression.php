<?php
declare(strict_types=1);

namespace Orion\Utilities;

class Compression {
	
	/**
	 * Compress a string
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function compress(string $string): string {
		return gzcompress($string);
	}

	/**
	 * Decompress a string
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function decompress(string $string): string {
		return gzuncompress($string);
	}
}