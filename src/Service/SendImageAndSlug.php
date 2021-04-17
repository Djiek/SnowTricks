<?php

namespace App\Service;

use App\Service\Slug;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SendImageAndSlug
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    public function send($form, $imageUpload, $figure, $manager, $oldImages)
    {
        $images = $form->get('images')->getData();
        foreach ($images as $image) {
            if (!$image->getLink()) {
                $fileName = $imageUpload->upload($image->getFile());
                $image->setLink($fileName);
                $figure->addImage($image);
                $oldImages[] = $image;
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
        foreach ($oldImages as $key => $value) {
            if (!in_array($value, $figure->getImages()->toArray())) {
                $name = $value->getLink();
                unlink($this->parameterBag->get('images_directory') . '/' . $name);
                $manager->remove($value);
            }
        }
    }
}
