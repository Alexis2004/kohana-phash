PHP pHash binding wrapper module for Kohana 3.3.x
=================================================

This module implements ability to use pHash library (http://phash.org/) functions for
calculating images perceptual hashes and comparing them.

Supported algorithms are:
- **DCT**: DCT-based image hash
- **MH**: Marr-Hildreth wavelet based image hash

## Requeriments

Installed PHP extensions:

- **GMP**: http://www.php.net/manual/en/book.gmp.php
- **pHash**: https://github.com/Alexis2004/php-phash

## How to use:

**Enable module in your bootstrap.php file:**

	Kohana::modules(array(
			...
			'phash'  => MODPATH.'phash'
		));

**Calculate perceptual hashes of different types:**

	// DCT hash
	$phash_dct = PHash_Image::calculate(
			'https://i.ytimg.com/vi/rpefUp8_NWA/default.jpg',
			PHash_Image::PHASH_DCT);

	// MH hash
	$phash_mh = PHash_Image::calculate(
			'https://i.ytimg.com/vi/rpefUp8_NWA/default.jpg',
			PHash_Image::PHASH_MH);

	// MH hash with non standard calculation options
	$phash_mh = PHash_Image::calculate(
			'https://i.ytimg.com/vi/rpefUp8_NWA/default.jpg',
			PHash_Image::PHASH_MH,
			array('alpha' => 2.0, 'level' => 1.0));

**Compare perceptual hashes of different types:**

	// DCT hash
	$is_similar = PHash_Image::is_similar($phash_dct1, $phash_dct2, PHash_Image::PHASH_DCT);

	// MH hash
	$is_similar = PHash_Image::is_similar($phash_mh1, $phash_mh2, PHash_Image::PHASH_MH);


## Theoretical Basis

For more details read http://phash.org/docs/design.html.
