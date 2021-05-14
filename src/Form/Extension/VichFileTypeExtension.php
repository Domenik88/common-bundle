<?php

namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver;
use Symfony\Component\Templating\Asset\PackageInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class VichFileTypeExtension extends AbstractTypeExtension
{
    const VICH_FILE_TYPE_NONE = 'vich_file_type_none';
    const VICH_FILE_TYPE_DOWNLOADABLE = 'vich_file_type_downloadable';
    const VICH_FILE_TYPE_IMAGE = 'vich_file_type_image';

    private $assetHelper;
    private $vichStorage;
    private $vichHelper;
    private $avalancheHelper;

    public function __construct(PackageInterface $assetHelper, StorageInterface $vichStorage = null, UploaderHelper $vichHelper = null, CachePathResolver $avalancheHelper = null)
    {
        $this->assetHelper = $assetHelper;
        $this->vichStorage = $vichStorage;
        $this->vichHelper = $vichHelper;
        $this->avalancheHelper = $avalancheHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'vich_file';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'file_type' => self::VICH_FILE_TYPE_NONE,
                'image_filter' => false,
                'always_required' => false,
                'assets_version' => null,
                'download_link' => false,
            ])
            ->setAllowedValues([
                'file_type' => [
                    self::VICH_FILE_TYPE_DOWNLOADABLE,
                    self::VICH_FILE_TYPE_IMAGE,
                    self::VICH_FILE_TYPE_NONE,
                ],
            ])
            ->setAllowedTypes([
                'image_filter' => [
                    'bool',
                    'string',
                ],
                'assets_version' => [
                    'null',
                    'bool',
                    'string',
                ],
                'always_required' => 'bool',
            ])
            ->setNormalizers([
                'image_filter' => function(Options $options, $value)
                {
                    return is_bool($value) ? false : $value;
                },
                'file_type' => function(Options $options, $value) //Convert Vich option to this one
                {
                    if($options['download_link'] && $value == self::VICH_FILE_TYPE_NONE)
                    {
                        return self::VICH_FILE_TYPE_DOWNLOADABLE;
                    }
                    return $value;
                },
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['file_type'] = $options['file_type'];
        if(!is_null($object = $form->getParent()->getData()) && $options['file_type'] != self::VICH_FILE_TYPE_NONE &&
            $this->vichStorage->resolvePath($object, $options['mapping']))
        {
            $path = $this->vichHelper->asset($object, $options['mapping']);
            if($options['file_type'] == self::VICH_FILE_TYPE_IMAGE)
            {
                $view->vars['image_url'] = $this->assetHelper->getUrl(
                    $this->avalancheHelper && $options['image_filter'] ?
                        $this->avalancheHelper->getBrowserPath($path, $options['image_filter']) :
                        $path,
                    $options['assets_version']
                );
            }
            $view->vars['original_url'] = $this->assetHelper->getUrl($path, $options['assets_version']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $object = $form->getParent()->getData();
        if(!is_null($object) && $this->vichStorage->resolvePath($object, $options['mapping']) && !$options['always_required'])
        {
            $view['file']->vars['required'] = false;
        }
    }

}