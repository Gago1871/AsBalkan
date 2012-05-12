<?php

class Jk_Paginator extends Zend_Paginator
{
    public function normalizePageNumber($pageNumber)
    {
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        
        $pageCount = $this->count();
        
        if ($pageCount > 0 and $pageNumber > $pageCount) {
            // $pageNumber = $pageCount;
            throw new Exception("Error Processing Request", 1);
            
        }

        return $pageNumber;
    }
}