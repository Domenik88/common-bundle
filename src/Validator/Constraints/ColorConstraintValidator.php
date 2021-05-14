<?php
namespace KMGi\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ColorConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if(!preg_match('/^(#[0-9a-fA-F]{6})?$/', $value))
        {
            $this->context->addViolation($constraint->message);
        }
    }
}