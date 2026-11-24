<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadFileService
{
    public function __construct(
        private string $targetDirectory,
        private readonly SluggerInterface $slugger
    ) {}

    public function upload(UploadedFile $file): string
    {
        //Récupération du fichier
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        //Renommage du fichier
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        //Déplacement du fichier
        try {
            $file->move($this->targetDirectory, $newFilename);
        } catch (FileException $e) {
           return 'Erreur de copie du fichier';
        }
        return $newFilename;
    }
}
