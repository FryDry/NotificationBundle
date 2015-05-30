<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 08/03/15
 * Time: 11:10
 */

namespace FryDry\NotificationBundle\Model;


use Symfony\Component\Config\Definition\Exception\Exception;

class ThumbGenerator {

	protected $options;

	function __construct($options)
	{
		$this->options = $options;
	}


	/**
	 * @param $path
	 * @param $width
	 * @param null $height
	 * @param int $quality
	 * @param string $method
	 * @return bool|string
	 */
	public function thumbnail($path, $width = null, $height = null, $quality = 100, $method = 'crop')
	{

		$webPath = $this->options['kernel.root_dir'] . '/../web';
		$cacheDir = $this->options['thumb_cache_dir'];
		if (is_null($width)) {
			$width = $this->options['thumb_default_width'];
		}

		if (!file_exists($webPath . $path)) {
			$path = $this->options['default_profile_image'];
		}

		$heightFilenameId = $height ?: 'auto';

		$thumbName = $width . 'x' . $heightFilenameId . '@' . $quality . '_' . $method . '_' . basename($path);

		if (!file_exists($webPath . $cacheDir . '/' . $thumbName)) {

			if (!file_exists($webPath . $cacheDir)) {
				mkdir($webPath . $cacheDir, 0777, true);
			}

			try {
				$imageInfo = getimagesize($webPath . $path);
			} catch (Exception $e) {
				return false;
			}

			$format = 'unknown';
			if (isset($imageInfo['mime'])) {
				switch ($imageInfo['mime']) {
					case 'image/jpeg':
					case 'image/jpg':
						$format = 'jpg';
						$image = imagecreatefromjpeg($webPath . $path);
						break;
					case 'image/png':
						$format = 'png';
						$image = imagecreatefrompng($webPath . $path);
						break;
					default:
						return false;
						break;
				}
			}

			$img_width = imagesx($image);
			$img_height = imagesy($image);

			$original_aspect = $img_width / $img_height;

			if (!$height) {
				$height = ($img_height * $width) / $img_width;
			}

			$thumb_aspect = $width / $height;

			if ( $original_aspect >= $thumb_aspect )
			{
				// If image is wider than thumbnail (in aspect ratio sense)
				$new_height = $height;
				$new_width = $img_width / ($img_height / $height);
			}
			else
			{
				// If the thumbnail is wider than the image
				$new_width = $width;
				$new_height = $img_height / ($img_width / $width);
			}

			$thumb = imagecreatetruecolor( $width, $height );

			// enable alpha blending on the destination image.
			imagealphablending($thumb, true);

			// Allocate a transparent color and fill the new image with it.
			// Without this the image will have a black background instead of being transparent.
			$transparent = imagecolorallocatealpha( $thumb, 0, 0, 0, 127 );
			imagefill( $thumb, 0, 0, $transparent );

			// Resize and crop
			if ($method == 'crop') {
				imagecopyresampled($thumb,
					$image,
					0 - ($new_width - $width) / 2, // Center the image horizontally
					0 - ($new_height - $height) / 2, // Center the image vertically
					0, 0,
					$new_width, $new_height,
					$img_width, $img_height);

			}
			if ($method == 'resize') {
				imagecopyresampled($thumb,
					$image,
					0, 0,
					0, 0,
					$width, $height,
					$img_width, $img_height);
			}

			switch ($format) {
				case 'jpg':
					imagejpeg($thumb, $webPath . $cacheDir . '/' . $thumbName, $quality);
					break;
				case 'png':
					imagealphablending($thumb, false);
					imagesavealpha($thumb, true);
					imagepng($thumb, $webPath . $cacheDir . '/' . $thumbName, $quality/10 - 1);
					break;
				default:
					return false;
					break;
			}

		}

		return $cacheDir . '/' . $thumbName;
//print_r($imageInfo);

	}

}