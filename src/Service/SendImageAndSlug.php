<?php

namespace App\Service;

use App\Entity\Figure;
use App\Service\Slug;
class SendImageAndSlug
{

    public function send($form,$imageUpload,$figure)
    { 
         if (!$figure) {
            $figure = new Figure();
        }
          $images = $form->get('images')->getData();
            foreach ($images as $image) {
                if (!$image->getLink()) {
                    $fileName = $imageUpload->upload($image->getFile());
                    $image->setLink($fileName);
                    $figure->addImage($image);
                }
            }
            if (!$figure->getId()) {
                $figure->setCreatedAt(new \DateTime());
                $slug = new Slug();
                $slugName = $slug->createSlug($figure->getName());
                $figure->setSlug($slugName);
            } else {
                $figure->setUpdatedAt(new \DateTime());
                $lastSlug = $figure->getSlug();
                $slug = new Slug();
                $nameWithoutSlug = $slug->deleteSlug($lastSlug);
                if ($figure->getName() != $nameWithoutSlug) {
                    $slugName = $slug->createSlug($figure->getName());
                    $figure->setSlug($slugName);
                }
            }
    }
}
