<?php

/**
 * Zend Resource Plugin that loads scripts to html head section
 */
class Jk_Resource_Xerocopy extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'storage' => array(
            'host' => null,
            'location' => null,
            ),
        'format' => array(),
    );

    public function init()
    {
        $this->_options = $this->getOptions();
        return $this;
    }

    public function saveImage($file)
    {
        $fileinfo = pathinfo($file);
        $imgdata = getimagesize($file);

        $filename = Jk_Url::normalize($fileinfo['filename']);
        $extension = $fileinfo['extension'];
        $filesize = filesize($file);
        
        $width = $imgdata[0];
        $height = $imgdata[1];
        $mime = $imgdata['mime'];

        $storage = $this->_options['storage']['location'];

        // $urlNormalizer = new Jk_Url_Normalizer($filename);
        // $filename = $urlNormalizer->normalize();

        // if (isset($format['type'])) {
        //     $extension = $format['type'];
        // }

        // add to database
        $attachmentGateway = new Jk_Model_Attachment_Gateway();
        $attachment = $attachmentGateway->create(array(
            'filename' => $filename . '.' . $extension,
            'added' => date('Y-m-d H:i:s'),
            'original_size_x' => $width,
            'original_size_y' => $height,
            'original_filesize' => $filesize,
            'original_mime' => $mime,
            'source' => $file,
            ));

        $id = $attachment->save();

        foreach ($this->_options['format'] as $key => $format) {
            if (isset($format['width'])) {
                $image = Jk_Image::resizeImage($file, $format['width']);
            } else {
                $image = Jk_Image::createImageFromFile($file);
            }

            $location = $storage . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $id;
            Jk_File::createDir($location);

            $destinationFilename = $filename . '.' . $extension;
            $destinationFile = $location . DIRECTORY_SEPARATOR . $destinationFilename;

            Jk_Image::saveImage($image, $destinationFile);
        }

        return $id;
    }

    public function image($id, $format = null)
    {
        if (null === $format) {
            $format = 'original';
        }

        $attachmentGateway = new Jk_Model_Attachment_Gateway();
        $attachment = $attachmentGateway->getById($id);

        // print_r($id);
        // die('sfds');

        return $this->_options['storage']['host'] . '/' . $format . '/' . $id . '/' . $attachment->filename;
    }
}