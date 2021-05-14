<?php

namespace KMGi\CommonBundle\Grid\Column;

use APY\DataGridBundle\Grid\Column\Column;
use Symfony\Component\Templating\Asset\PackageInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver;

class ImageColumn extends Column
{
    private $assetHelper;
    private $vichStorage;
    private $vichHelper;
    private $avalancheHelper;

    protected $altTextField;
    protected $mapping;
    protected $imageFilter;
    protected $placeholder;
    protected $assetVersion;

    public function __construct(PackageInterface $assetHelper, StorageInterface $vichStorage = null, UploaderHelper $vichHelper = null, CachePathResolver $avalancheHelper = null)
    {
        $this->assetHelper = $assetHelper;
        $this->vichStorage = $vichStorage;
        $this->vichHelper = $vichHelper;
        $this->avalancheHelper = $avalancheHelper;
    }

    public function getType()
    {
        return 'image';
    }

    public function getAltTextField()
    {
        return $this->altTextField;
    }

    public function __initialize(array $params)
    {
        parent::__initialize($params);

        $this->altTextField = $this->getParam('altTextField', false);
        $this->mapping = $this->getParam('mapping');
        $this->imageFilter = $this->getParam('image_filter', false);
        $this->placeholder = $this->getParam('placeholder', false);
        $this->assetVersion = $this->getParam('asset_version');

        $this->setFilterable(false);
        $this->setSortable(false);
    }

    public function renderCell($value, $row, $router)
    {
        /* @var $row \APY\DataGridBundle\Grid\Row */
        $hasImage = false;
        if(!is_null($this->vichStorage) && !is_null($this->vichHelper) && !is_null($this->mapping) && ($object = $row->getEntity())
            && $this->vichStorage->resolvePath($object, $this->mapping))
        {
            $path = $this->vichHelper->asset($object, $this->mapping);
            $originalUrl = $this->assetHelper->getUrl($path, $this->assetVersion);
            $imageUrl = !is_null($this->avalancheHelper) && $this->imageFilter ?
                $this->assetHelper->getUrl($this->avalancheHelper->getBrowserPath($path, $this->imageFilter)) :
                $originalUrl
            ;
            $row
                ->setField("{$this->getId()}_original_image", $originalUrl)
                ->setField("{$this->getId()}_image", $imageUrl)
            ;
            $hasImage = true;
        }
        elseif($value)
        {
            $imageUrl = $this->assetHelper->getUrl($value, $this->assetVersion);
            $row
                ->setField("{$this->getId()}_original_image", $imageUrl)
                ->setField("{$this->getId()}_image", $imageUrl)
            ;
            $hasImage = true;
        }
        if(!$hasImage && $this->placeholder)
        {
            $imageUrl = $this->assetHelper->getUrl($this->placeholder, $this->assetVersion);
            $row
                ->setField("{$this->getId()}_original_image", $imageUrl)
                ->setField("{$this->getId()}_image", $imageUrl)
            ;
        }
        return parent::renderCell($value, $row, $router);
    }
}
