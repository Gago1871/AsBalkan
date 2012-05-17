<?php

class Jk_Url
{
    /**
     * Crete URL friendly uniqe id
     */
    public function generateUniqueId($len = 6)
    {   
        $hex = md5('jklibsaltyo1982' . uniqid('', true));

        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);

        $uid = preg_replace('#(*UTF8)[^a-z0-9]#', '', strtolower($tmp));

        $len = max(4, min(128, $len));

        while (strlen($uid) < $len) {
            $uid .= self::generateUniqueId(22);
        }

        return substr($uid, 0, $len);
    }
    
    /**
     * Sanitize string to fit URL RFC ####
     */
    public function normalize($string)
    {
        $string = preg_replace('/-+/', '-', strtolower(preg_replace('/[^A-Za-z0-9_]/', '-', $string)));

        // $string = preg_replace('/\W/', '', $string);

        return $string;
    }
}