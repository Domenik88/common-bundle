<?php

namespace KMGi\CommonBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use KMGi\CommonBundle\DependencyInjection\KMGiCommonExtension;

class KMGiCommonBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension)
        {
            $this->extension = new KMGiCommonExtension();
        }
        return $this->extension;
    }
}
