<?php

/**
 * Jk_Image tool class for image operations
 */
class Jk_Image
{
    /**
     * Default JPEG quality for saved images
     */
    private static $_jpegQuality = 88;

    /**
     * Resize image keeping proportions /ratio aspect
     */
    public function resizeImage($source, $newWidth = 100, $destination = null)
    {
        $image = self::createImageFromFile($source);
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        // calculate thumbnail size
        $newHeight = floor($height * ($newWidth / $width));

        // create a new temporary image
        $tmpImage = imagecreatetruecolor($newWidth, $newHeight);

        // copy and resize old image into new image 
        imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
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
}