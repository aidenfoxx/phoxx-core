<?php

namespace Phoxx\Core\File;

/**
 * https://github.com/symfony/http-foundation/blob/master/File/File.php
 */
class Image extends File
{
	const FORMAT_BMP = 'bmp';
	const FORMAT_JPG = 'jpeg';
	const FORMAT_PNG = 'png';

	const SCALE_FILL = 'fill';
	const SCALE_COVER = 'cover'; 
	const SCALE_CONTAIN = 'contain';

	public function setQuality(float $quality)
	{

	}

	public function setFormat(string $format)
	{

	}

	public function resize(?int $width, int ?$height = null, string $scale = self::SCALE_FILL): void
	{

	}

	public function rotate(int $angle)
	{

	}
}