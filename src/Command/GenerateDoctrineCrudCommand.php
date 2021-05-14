<?php
namespace KMGi\CommonBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand as GDCC;
use KMGi\CommonBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class GenerateDoctrineCrudCommand extends GDCC
{
    /**
     * {@inheritDoc}
     */
    protected function createGenerator($bundle = null)
    {
        return new DoctrineCrudGenerator($this->getContainer());
    }

    /**
     * {@inheritDoc}
     */
    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        return array_merge(
            [__DIR__.'/../Resources/SensioGeneratorBundle/skeleton'],
            parent::getSkeletonDirs($bundle)
        );
    }
}