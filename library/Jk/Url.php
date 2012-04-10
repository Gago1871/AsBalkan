<?php

class Jk_Url
{
    /**
     * Crete URL friendly uniqe id
     */
    public function createUniqueId()
    {   
        return substr(strrev(uniqid()), 0, 7);
    }
    
    /**
     * Sanitize string to fit URL RFC ####
     */
    public function normalize($string)
    {
        $string = str_replace(array('#', ' '), '-', $string);
        return $string;
    }
}