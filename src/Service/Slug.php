<?php

namespace App\Service;

use App\Entity\Figure;

class Slug
{
    public function createSlug($name)
    {   
        $slug = str_replace(" ","-",$name);
        return $slug;
    }

    public function deleteSlug($name)
    {   
        $slug = str_replace("-"," ",$name);
        return $slug;
    }
}
