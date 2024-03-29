<?php

require_once('ImageDriver.php');
class ImageDriverGD extends ImageDriver
{
  	public function resize(ImageManipulator $image, $newPath, $newWidth, $newHeight)
  	{
		$path = $image->getImagePath();
		$height = $image->getHeight();
		$width = $image->getWidth();

		$this->setMemoryForImage($path);

		$newimg = $this->getGDImage($path);

		if($newimg)
		{
			// resize large images in two steps - first resample, then resize
			// http://lt.php.net/manual/en/function.imagecopyresampled.php
			if ($width > 1500 || $height > 1200)
			{
				list($width, $height) = $this->resample($newimg, $image, $width, $height, 1024, 768, 0);
			}

			$resized = $this->resample($newimg, $image, $width, $height, $newWidth, $newHeight);

			if(!is_dir(dirname($newPath)))
			{
				mkdir(dirname($newPath), 0777, true);
			}

			// simply copy images that do not need to be resized
			if (!$resized)
			{
				copy($path, $newPath);
				return true;
			}

			$this->save($image->getType(), $newimg, $newPath, $image->getQuality());

	 		imagedestroy($newimg);
			return true;
		}
		else
		{
		  	return false;
		}
	}

	private function getGDImage($path)
	{
		$imageInfo = getimagesize($path);

		switch($imageInfo[2])
		{
			case IMAGETYPE_GIF:   return imagecreatefromgif($path); break;
			case IMAGETYPE_JPEG:  return imagecreatefromjpeg($path); break;
			case IMAGETYPE_PNG:   return imagecreatefrompng($path); break;
			default: throw new ApplicationException('Invalid image type: ' . $imageInfo[2]);
		}
	}

	public function getValidTypes()
	{
	  	return array(1, /* GIF */
	  				 2, /* JPEG */
	  				 3  /* PNG */
		  			 );
	}

	public function watermark(ImageManipulator $image, $watermarkImage, $isLeft, $isTop, $marginX, $marginY)
	{
		// Load the stamp and the photo to apply the watermark to
		$stamp = $this->getGDImage($watermarkImage);
		$im = $this->getGDImage($image->getPath());

		// Set the margins for the stamp and get the height/width of the stamp image
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);

		$left = $isLeft ? $marginX : imagesx($im) - $sx - $marginX;
		$top = $isTop ? $marginY : imagesy($im) - $sy - $marginY;

		// Copy the stamp image onto our photo using the margin offsets and the photo
		// width to calculate positioning of the stamp.
		imagecopy($im, $stamp, $left, $top, 0, 0, $sx, $sy);

		// Output and free memory
		$this->save($image->getType(), $im, $image->getPath(), $image->getQuality());

		imagedestroy($im);
	}

	private function save($type, $img, $path, $quality)
	{
		switch($type)
		{
			case IMAGETYPE_GIF: imagegif($img, $path); break;
			case IMAGETYPE_PNG: imagepng($img, $path);  break;
			case IMAGETYPE_JPEG:
			default:
				imagejpeg($img, $newPath, $quality);
			break;
		}
	}

	private function setMemoryForImage($filename)
	{
		$imageInfo = getimagesize($filename);
		$MB = 1048576;
		$K64 = 65536;
		$TWEAKFACTOR = 4;
		$memoryLimitMB = 32;
		if (!isset($imageInfo['channels']))
		{
			$imageInfo['channels'] = 4;
		}
		$memoryNeeded = round( ( $imageInfo[0] * $imageInfo[1]
											   * $imageInfo['bits']
											   * $imageInfo['channels'] / 8
								 + $K64
							   ) * $TWEAKFACTOR
							 );

		//ini_get('memory_limit') only works if compiled with "--enable-memory-limit" also
		//Default memory limit is 8MB so well stick with that.
		//To find out what yours is, view your php.ini file.
		$memoryLimit = $memoryLimitMB * $MB;
		$newLimit = memory_get_usage() + $memoryNeeded;
		$currentLimit = substr(ini_get('memory_limit'), 0, -1);
		if (function_exists('memory_get_usage') && ($newLimit > $memoryLimit) && ($newLimit > $currentLimit) )
		{
			$newLimit = $memoryLimitMB + ceil( ( memory_get_usage()
												+ $memoryNeeded
												- $memoryLimit
												) / $MB
											);
			ini_set('memory_limit', $newLimit . 'M');
			return true;
		}
		else
		{
			return false;
		}
	}

	private function resample(&$img, ImageManipulator $source, $owdt, $ohgt, $maxwdt, $maxhgt, $quality = 1)
	{
		// make sure the image doesn't get enlarged
		$maxwdt = min($maxwdt, $owdt);
		$maxhgt = min($maxhgt, $ohgt);

		if(!$maxwdt)
		{
			$divwdt = 1;
		}
		else
		{
			$divwdt = max(1, $owdt/$maxwdt);
		}

		if(!$maxhgt)
		{
			$divhgt = 1;
		}
		else
		{
			$divhgt = max(1, $ohgt/$maxhgt);
		}

		if($divwdt >= $divhgt)
		{
			$newwdt = round($owdt/$divwdt);
			$newhgt = round($ohgt/$divwdt);
		}
		else
		{
			$newhgt = round($ohgt/$divhgt);
			$newwdt = round($owdt/$divhgt);
		}

		// return same image if resizing is not necessary
		if (($newwdt == $owdt) && ($newhgt == $ohgt))
		{
			return false;
		}

		$tn = imagecreatetruecolor($newwdt, $newhgt);

		if (in_array($source->getType(), array(IMAGETYPE_GIF, IMAGETYPE_PNG)))
		{
			$trnprt_indx = imagecolortransparent($img);

			// If we have a specific transparent color
			if ($trnprt_indx >= 0)
			{
				// Get the original image's transparent color's RGB values
				$trnprt_color = imagecolorsforindex($img, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($tn, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($tn, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($tn, $trnprt_indx);
			}

			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($source->getType() == IMAGETYPE_PNG)
			{
				// Turn off transparency blending (temporarily)
				imagealphablending($tn, false);

				// Create a new transparent color for image
				$color = imagecolorallocatealpha($tn, 0, 0, 0, 127);

				// Completely fill the background of the new image with allocated color.
				imagefill($tn, 0, 0, $color);

				// Restore transparency blending
				imagesavealpha($tn, true);
			}
		}

		if ($quality)
		{
			imagecopyresampled($tn,$img,0,0,0,0,$newwdt,$newhgt,$owdt,$ohgt);
		}
		else
		{
			imagecopyresized($tn,$img,0,0,0,0,$newwdt,$newhgt,$owdt,$ohgt);
		}

		imagedestroy($img);

		$img = $tn;

		return array($newwdt, $newhgt);
	}

}

?>