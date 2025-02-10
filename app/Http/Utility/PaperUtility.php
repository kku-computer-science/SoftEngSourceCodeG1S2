<?php

namespace App\Http\Utility;


class PaperUtility
{
    public static function isDuplicatePaper($paper, $papers)
    {
        foreach ($papers as $p) {
            if ($p->title == $paper->title && $p->author == $paper->author) {
                return true;
            }
        }
        return true;
    }

    public static function validatePaperObjectFormat($paper)
    {
        
        return true;
    }
}