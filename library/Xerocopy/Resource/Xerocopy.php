<?php

/**
 * Zend Resource Plugin to handle image attachments
 */
class Xerocopy_Resource_Xerocopy extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Available options
     */
    protected $_options = array(
        'storage' => array(
            'host' => null,
            'location' => null,
            ),
        'format' => array(),
        'storeOriginal' => 0,
    );

    /**
     * Init resource
     */
    public function init()
    {
        $this->_options = $this->getOptions();
        return $this;
    }

    /**
     * Saves image to attachments database and creates thumbnails
     */
    public function saveImage($file, $originalSource = null)
    {
        if (null === $originalSource) {
            $originalSource = $file;
        }

        $fileinfo = pathinfo($file);
        $imgdata = getimagesize($file);

        $filename = Xerocopy_Tools::normalize($fileinfo['filename']);

        $filesize = filesize($file);
        
        $width = $imgdata[0];
        $height = $imgdata[1];
        $mime = $imgdata['mime'];

        if (isset($fileinfo['extension'])) {
            $extension = $fileinfo['extension'];
        } else {
            $exploded = explode('/', $mime);
            $extension = $exploded[1];
        }

        $storage = $this->_options['storage']['location'];

        // if (isset($format['type'])) {
        //     $extension = $format['type'];
        // }

        // add to database
        $attachmentGateway = new Xerocopy_Model_Attachment_Gateway();
        $attachment = $attachmentGateway->create(array(
            'filename' => $filename . '.' . $extension,
            'added' => date('Y-m-d H:i:s'),
            'original_size_x' => $width,
            'original_size_y' => $height,
            'original_filesize' => $filesize,
            'original_mime' => $mime,
            'source' => $originalSource,
            ));

        // save attachment to database
        $id = $attachment->save();

        foreach ($this->_options['format'] as $key => $format) {
            if (isset($format['width'])) {
                $image = Xerocopy_Image::resizeImage($file, $format['width']);
            } else {
                $image = Xerocopy_Image::createImageFromFile($file);
            }

            $location = $storage . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . Xerocopy_Tools::getHashedDir($id) . DIRECTORY_SEPARATOR . $id;
            $destinationFilename = $filename . '.' . $extension;
            $destinationFile = $location . DIRECTORY_SEPARATOR . $destinationFilename;

            Xerocopy_Tools::createDir($location);
            Xerocopy_Image::saveImage($image, $destinationFile);
        }

        // handle original file if needed
        if (1 == $this->_options['storeOriginal']) {
            $location = $storage . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . Xerocopy_Tools::getHashedDir($id) . DIRECTORY_SEPARATOR . $id;
            $destinationFilename = $filename . '.' . $extension;
            $destinationFile = $location . DIRECTORY_SEPARATOR . $destinationFilename;

            Xerocopy_Tools::createDir($location);
            copy($file, $destinationFile);
        }

        return $id;
    }

    /**
     * Return image URL
     */
    public function image($id, $format = null)
    {
        if (null === $format) {
            $format = 'original';
        }

        $attachmentGateway = new Xerocopy_Model_Attachment_Gateway();
        $attachment = $attachmentGateway->getById($id);
        return $this->_options['storage']['host'] . '/' . $format . '/' . Xerocopy_Tools::getHashedDir($id) . '/' . $id . '/'. $attachment->filename;
    }
}