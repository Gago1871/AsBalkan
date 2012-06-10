<?php

/**
 * Xerocopy_Image tool class for image operations
 */
class Xerocopy_Image
{
    /**
     * Default JPEG quality for saved images
     */
    private static $_jpegQuality = 88;

    /**
     * Resize image keeping proportions /ratio aspect
     */
    public function resizeImage($source, $newWidth = 100, $destination = false, $minWidthResize = 0)
    {
        $image = self::createImageFromFile($source);
        
        $width = imagesx($image);
        $height = imagesy($image);

        // only resize images bigger than $minWidthResize
        if ($width >= $minWidthResize) {

            // calculate thumbnail size
            $newHeight = floor($height * ($newWidth / $width));
            // create a new temporary image
            $tmpImage = imagecreatetruecolor($newWidth, $newHeight);

            // copy and resize old image into new image 
            imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        } else {
            $tmpImage = $image;
        }
        
        if ($destination) {
            self::saveImage($tmpImage, $destination);
            return true;
        }
        
        return $tmpImage;
    }

    /**
     * Create image from given file
     */
    public function createImageFromFile($file) {
        $data = getimagesize($file);
        
        switch ($data['mime']) {
            case 'image/jpeg':
               $image = imagecreatefromjpeg($file); //jpeg file
               break;
            case 'image/gif':
                $image = imagecreatefromgif($file); //gif file
                break;
            case 'image/png':
                $image = imagecreatefrompng($file); //png file
                break;
            default: 
                $image = false;
                break;
            }
            
        return $image;
    }
    
    /**
     * Save image to a file
     */
    public function saveImage($image, $destination)
    {
        $imageInfo = pathinfo($destination);
        
        switch ($imageInfo['extension']) {
            case 'png':
                self::savePng($image, $destination);
                break;
            
            default:
                self::saveJpeg($image, $destination);
                break;
        }
    }
    
    /**
     * Save image as a PNG file
     */
    public function savePng($image, $destination)
    {
        imagepng($image, $destination);
    }

    /**
     * Save image as a JPEG file
     */
    public function saveJpeg($image, $destination, $quality = null)
    {
        if (null === $quality) {
            $quality = self::$_jpegQuality;
        }

        imagejpeg($image, $destination, $quality);
    }

    public function setImageQuality($quality)
    {
        $this->_imageQuality = $quality;
    }

    /**
     * Glue watermark in the bottom of image
     */
    public function watermark($image, $watermarkFile)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $watermark = self::resizeImage($watermarkFile, $width);
        $watermarkHeight = imagesy($watermark);

        $mergedImage = imagecreatetruecolor($width, $height + $watermarkHeight);

        //imagecopymerge ( resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h , int $pct )
        imagecopymerge($mergedImage, $image, 0, 0, 0, 0, $width, $height, 100);
        imagecopymerge($mergedImage, $watermark, 0, $height, 0, 0, $width, $watermarkHeight, 100);

        return $mergedImage;
    }
}