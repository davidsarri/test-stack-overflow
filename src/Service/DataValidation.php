<?php

namespace App\Service;

class DataValidation
{
    public function tagValidation($tagged,$mandatory,$tagseparator,$maxtags){
        $validantion = true;
        if($mandatory) {
            if($tagged === NULL or $tagged ==='' or strlen($tagged) == 0 ) {
                $validantion = false;
            }
        }
        if($tagseparator != '') {
            if(strpos($tagged,$tagseparator) !== false) {
                $tags = explode($tagseparator,$tagged);
                if(isset($maxtags) and $maxtags != '' and preg_match('/^\d+$/',$maxtags) == 1){
                    if(count($tags) > $maxtags) {
                        $validantion = false;
                    }
                }
            }
        }
        return $validantion;
    }

    public function dateValidation($date,$mandatory,$regex){
        $validantion = true;
        if($mandatory) {
            if($date === NULL or $date ==='' or strlen($date) == 0 ) {
                $validantion = false;
            }
        }
        if($date !=='' and $date != NULL  and preg_match($regex,$date) != 1){
            $validantion = false;
        }
        return $validantion;
    }
}