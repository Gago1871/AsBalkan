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

    public function download($url, $dir = null, $prefix = 'jk')
    {
        $tmpdir = (null === $dir?sys_get_temp_dir():$dir);
        $tmpname = tempnam($tmpdir, $prefix);

        $fp = fopen($tmpname, 'w+');//This is the file where we save the information
        $ch = curl_init($url);//Here is the file we are downloading
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (!curl_exec($ch)) {

            curl_close($ch);
            fclose($fp);
            throw new Exception("Error downloading file", 1);
        }
        curl_close($ch);
        fclose($fp);

        return $tmpname;
    }

    /**
     * Get mime type of given file
     */
    public function getMimeType($filename)
    {
        $finfo = finfo_open(FILEINFO_MIME);
        $data = explode('; ', finfo_file($finfo, $filename)); 
        finfo_close($finfo);
        return $data[0];

        // return mime_content_type($filename);
    }


}