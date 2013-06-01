<?php defined('SYSPATH') OR die('No direct access allowed.');

class PHash_Image
{

	/*
	 * DCT-based image hash
	 */
	const PHASH_DCT = 1;

	/**
	 * Marr-Hildreth wavelet based image hash
	 */
	const PHASH_MH = 2;

	/**
	 * @link http://phash.org/docs/design.html
	 */
	const DCT_IMAGE_HASH_SIMILARITY_THRESHOLD = 18;
	const MH_IMAGE_HASH_SIMILARITY_THRESHOLD = 0.32;

	/**
	 * @return bool
	 */
	public static function is_supported()
	{
		if (!PHash::is_supported())
		{
			return FALSE;
		}
		if (!function_exists("ph_dct_imagehash") || !function_exists("ph_dct_imagehash_to_array") ||
			!function_exists("ph_mh_imagehash") || !function_exists("ph_mh_imagehash_to_array"))
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * @param string $url
	 * @param int $phash_type
	 * @param array $params
	 * @return bool|array
	 */
	public static function calculate($url, $phash_type = PHash_Image::PHASH_DCT, array $params = array())
	{
		if (!PHash_Image::is_supported())
		{
			// Extension is not installed
			return FALSE;
		}

		if (!in_array($phash_type, array(PHash_Image::PHASH_DCT, PHash_Image::PHASH_MH)))
		{
			// Invalid hash type
			return FALSE;
		}

		$data = @file_get_contents($url);
		if ($data === FALSE)
		{
			// Error loading file/url
			return FALSE;
		}

		$temp_file = sys_get_temp_dir() . '/' . Text::random('hexdec', 32) . '.' . pathinfo($url, PATHINFO_EXTENSION);
		$hash = null;
		file_put_contents($temp_file, $data);
		if ($phash_type == PHash_Image::PHASH_DCT) {
			$hash = ph_dct_imagehash($temp_file);
		} else if ($phash_type == PHash_Image::PHASH_MH) {
			$alpha = Arr::get($params, 'alpha', 2.0);
			$level = Arr::get($params, 'level', 1.0);
			$hash = ph_mh_imagehash($temp_file, $alpha, $level);
		}
		unlink($temp_file);

		if ($hash === FALSE)
		{
			// Hash calculating error (file is corrupted or not an image file)
			return FALSE;
		}

		// Returns hash as array of integers
		if ($phash_type == PHash_Image::PHASH_DCT)
		{
			$hash = ph_dct_imagehash_to_array($hash);
		}
		else if ($phash_type == PHash_Image::PHASH_MH)
		{
			$hash = ph_mh_imagehash_to_array($hash);
		}
		return $hash;
	}

	/**
	 * @param array $phash1
	 * @param array $phash2
	 * @param int $phash_type
	 * @return bool
	 */
	public static function is_similar($phash1, $phash2, $phash_type = PHash_Image::PHASH_DCT)
	{
		$distance = PHash::hamming_distance($phash1, $phash2);
		if ($distance !== FALSE)
		{
			if ($phash_type == PHash_Image::PHASH_DCT)
			{
				return $distance < PHash_Image::DCT_IMAGE_HASH_SIMILARITY_THRESHOLD;
			}
			else if ($phash_type == PHash_Image::PHASH_MH)
			{
				return ($distance / count($phash1) / 8) < PHash_Image::MH_IMAGE_HASH_SIMILARITY_THRESHOLD;
			}
		}
		return FALSE;
	}

}