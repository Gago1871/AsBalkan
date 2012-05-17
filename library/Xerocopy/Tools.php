<?php

/**
* 
*/
class Xerocopy_Tools
{
    /**
     * Sanitize string to fit URL RFC ####
     */
    public function normalize($string)
    {
        // replace all character but given to a dash, lowercase it, and cut consecutive dashes
        $string = preg_replace('/-+/', '-', strtolower(preg_replace('/[^A-Za-z0-9]/', '-', $string)));
        // $string = preg_replace('/\W/', '', $string);

        return $string;
    }

    /**
     * Create directory from given path, and set a+rwx rights
     */
    public function createDir($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        return true;
    }

    /**
     * Crete URL friendly uniqe id
     */
    public function generateUniqueId($id, $len = 6)
    {   
        $hex = md5('xerocopy' . $id);

        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);

        $uid = preg_replace('#(*UTF8)[^a-z0-9]#', '', strtolower($tmp));

        $len = max(3, min(128, $len));

        while (strlen($uid) < $len) {
            $uid .= self::generateUniqueId($id, 22);
        }

        return substr($uid, 0, $len);
    }

    public function getHashedDir($id, $len = 6)
    {
        $hashedDir = '';
        $hash = self::generateUniqueId($id, $len);

        for ($i=0; $i < strlen($hash); $i++) { 
            $hashedDir .= substr($hash, $i, 1) . DIRECTORY_SEPARATOR;
        }

        $hashedDir = trim($hashedDir, DIRECTORY_SEPARATOR);

        return $hashedDir;
    }
}
