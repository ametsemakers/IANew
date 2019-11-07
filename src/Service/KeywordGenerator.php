<?php
namespace App\Service;

class KeywordGenerator
{
    public function getPageKeywords($pages)
    {
        foreach ($pages as $page)
        {
            $arr_temp[] = explode(' ',$page->getMetaKeyWords());
            $keywords = array();
            
            for ($i=0; $i<count($arr_temp); $i++)
            {
                for ($x=0; $x<count($arr_temp[$i]); $x++)
                {
                    if (!in_array($arr_temp[$i][$x], $keywords))
                    {
                        $keywords[] = $arr_temp[$i][$x];
                    }
                }
            }
        }
        return $keywords;
    }
}