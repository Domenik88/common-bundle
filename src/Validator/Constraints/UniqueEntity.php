<?php
namespace KMGi\CommonBundle\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UE;

/**
 * @Annotation
 */
class UniqueEntity extends UE
{
    public $ignoreFalseLikeValues = false;

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'kmgi.common_bundle.validator.unique_entity';
    }
}