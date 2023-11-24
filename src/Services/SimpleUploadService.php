<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function Symfony\Component\Translation\t;

class SimpleUploadService
{
    //Access to service.yaml
    //Chemin de destination définis dans le fichier service.yaml parameters = images_directory;
    //images_directory: '%kernel.project_dir%/public/img/'
    public function __construct(private ParameterBagInterface $param){}

    public function uploadImage(UploadedFile $file){

        $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $new_file_name = $original_file_name.'-'.uniqid().'.'.$file->guessExtension();

        $path_destination = $this->param->get('images_directory');

        $file->move( // same as php moveUploadFile()
            $path_destination,
            $new_file_name
        );

        return $new_file_name;
    }

    public function deleteImage(string $file)
    {
        $path = $this->param->get('images_directory');
        $image_object = $path . "/" . $file;
        $success = false;

        if (file_exists($image_object))
        {
            unlink($image_object);
            $success = true;

            return $success;
        }

        return false;
    }

}