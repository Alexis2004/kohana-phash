<?php defined('SYSPATH') OR die('No direct access allowed.');

class PHash
{

	/**
	 * @return bool
	 */
	public static function is_supported()
	{
		if (!extension_loaded("pHash") ||
			!extension_loaded("gmp"))
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * @param array $phash1
	 * @param array $phash2
	 * @param int $bits
	 * @return bool|int
	 */
	public static function hamming_distance($phash1, $phash2, $bits = 8)
	{
		$phash1 = PHash::array_to_bin($phash1, $bits);
		$phash2 = PHash::array_to_bin($phash2, $bits);
		if (!$phash1 || !$phash2)
		{
			return FALSE;
		}

		$ham1 = gmp_init($phash1, 2);
		$ham2 = gmp_init($phash2, 2);
		return gmp_hamdist($ham1, $ham2);
	}

	/**
	 * @param array $phash
	 * @param int $bits
	 * @return bool|string
	 */
	public static function array_to_bin($phash, $bits = 8)
	{
		if (!is_array($phash))
		{
			return FALSE;
		}

		$hash = '';
		for ($i = 0; $i < count($phash); $i++)
		{
			$hash .= str_pad(decbin($phash[$i]), $bits, "0", STR_PAD_LEFT);
		}
		return $hash;
	}

}