<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUpload
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function upload(UploadedFile $file)
    {
        $fileName =  md5(uniqid()) . '.' . $file->guessExtension();

        $file->move(
            $this->parameterBag->get('images_directory'),
            $fileName
        );

        return $fileName;
    }
}
