<?php
namespace Ice;

defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

/**
 * Class to handle an image and modify it.
 */
class IceImage
{
    private $name;
    private $image;
    private $type;
    private $cachepath = '../cache/img_';

    public function getWidth()
    {
        return imagesx($this->image);
    }
    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function __construct($filename)
    {
        $size = getimagesize($filename);
        $this->type = $size[2];
        $this->name = $filename;

        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filename);
                break;

            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;

            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;

            default:
                throw new Exception("Unrecognized file type",1);
                break;
        }

    }

    public function __destruct()
    {
        imagedestroy($this->image);
    }

    public function output($type=IMAGETYPE_JPEG)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image);
                break;

            case IMAGETYPE_GIF:
                imagegif($this->image);
                break;

            case IMAGETYPE_PNG:
                imagepng($this->image);
                break;

            default:
                throw new Exception("Unrecognized file type",1);
                break;
        }

        return 0;
    }

    public function outputAndCache()
    {
        $this->cache();
        readfile($this->cachepath);
    }

    /**
     * Save the image to $this->cachepath
     */
    public function cache()
    {
        switch ($this->type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $this->cachepath);
                break;

            case IMAGETYPE_GIF:
                imagegif($this->image, $this->cachepath);
                break;

            case IMAGETYPE_PNG:
                imagepng($this->image, $this->cachepath);
                break;

            default:
                throw new Exception("Unrecognized file type",1);
                break;
        }

        return 0;
    }
	/**
	 * Simple resizing of the image
	 */
    public function resize($width, $height)
    {
        $new = imagecreatetruecolor($width, $height);
        imagecopyresampled($new,
            $this->image,
            0, 0, 0, 0,
            $width,
            $height,
            $this->getWidth(),
            $this->getHeight());

        imagedestroy($this -> image);
        $this->image = $new;
    }
	
	/**
	 * Resize the width, keeping the aspect ratio
	 */
    public function resizeWidth($width)
    {
        $height = $this->getHeight() * ($width / $this->getWidth());
        $this->resize($width, $height);
    }
	/**
	 * Resize the height, keeping the aspect ratio
	 */
    public function resizeHeight($height)
    {
        $width = $this->getWidth() * ($height / $this->getHeight());
        $this->resize($width, $height);
    }

    /**
     * Resize an image to the specified dimensions. If the source and
     * target aspect-ratio doesn't match, the image will be cropped
     * to fill the entire area without stretching.
     */
    public function resizeToFit($targetWidth, $targetHeight)
    {
        $sourceWidth = $this->getWidth();
        $sourceHeight = $this->getHeight();

        $sourceAR = $sourceWidth / $sourceHeight;
        $targetAR = $targetWidth / $targetHeight;
        $height = 0; $width = 0;

        if ($sourceAR > $targetAR) {
            $height = $targetHeight;
            $width = (int) ($targetHeight * $sourceAR);
        } else {
            $width = $targetWidth;
            $height = (int) ($targetWidth / $sourceAR);
        }

        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $tmp, $this->image,
            0, 0, 0, 0,
            $width, $height,
            $sourceWidth, $sourceHeight);

        $x0 = ( $width - $targetWidth ) / 2;
        $y0 = ( $height - $targetHeight ) / 2;

        $result = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopy(
            $result, $tmp,
            0, 0, $x0, $y0,
            $targetWidth, $targetHeight);

        imagedestroy($tmp);
        imagedestroy($this->image);
        $this->image = $result;
    }

    public function setCachePath($pre) {$this->cachepath = $pre; }

    public function getCachePath()
    {
        return $this->cachepath;
    }

    /**
     * Checks if the filetype of the specified path is allowed in the
     * config.
     * @global $config
     * @param  string  $path The path to check
     * @return boolean isAllowed
     */
    public static function isAllowedType($path)
    {
        global $config;

        $parts = pathinfo($path);

        return in_array(strtolower($parts['extension']), $config['allowed_ext']);
    }

    /**
     * Returns all the images in the specified path which are allowed by
     * isAllowedType
     *
     * @param  string   $patt The path to search
     * @return string[] Paths to the images
     */
    public static function getImagePaths($patt='../media/*.*')
    {
        $images = array();
        foreach (glob($patt) as $v) {
            if (self::isAllowedType($v)) {
                $images[] = basename($v);
            }
        }

        return $images;
    }
}
