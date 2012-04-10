<?php

class Jk_File
{
    /**
     * Create string representing directory structure
     * 
     * Example:
     *      $hash = abcd1234
     *      $depth = 3
     *      return a/b/c/d1234
     */
    public function getHashedDirStructure($hash, $depth = 3)
    {
        if (strlen($hash) < $depth) {
            throw new Exception('You`re doing it wrong. $hash length should be greater than $depth.');
        }
        
        $dirName = '';
        
        for ($i=0; $i < $depth; $i++) { 
            $dirName .= $hash[$i] . DIRECTORY_SEPARATOR;
        }

        return $dirName . substr($hash, $depth, strlen($hash) - $depth);
    }
    
    
    /**
     * Create directory from given path, and set a+rwx rights
     */
    public function createDir($path) {

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        return true;
    }
}