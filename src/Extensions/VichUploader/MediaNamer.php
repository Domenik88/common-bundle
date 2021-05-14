<?php
namespace KMGi\CommonBundle\Extensions\VichUploader;

use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaNamer implements NamerInterface
{
    /**
     * {@inheritDoc}
     */
    public function name($object, PropertyMapping $mapping)
    {
        $file = $mapping->getFile($object);
        $extension = $file instanceof UploadedFile ? ($file->guessClientExtension() ?: $file->guessExtension()) : $file->guessExtension();
        return str_replace('.', '_', uniqid('', true)) . '.' . $extension;
    }
}