<?php
namespace KMGi\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ColorConstraint extends Constraint
{
    public $message = 'Color value should look like "#019ABF", i.e. 6 hexademical digits with # at beginning';
}