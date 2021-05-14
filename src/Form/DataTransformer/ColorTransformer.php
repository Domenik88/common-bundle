<?php
namespace KMGi\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ColorTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if(is_string($value) && strlen($value) > 0)
        {
            if($value[0] !== '#')
            {
                $value = "#{$value}";
            }
            if(strlen($value) > 7)
            {
                $value = substr($value, 0, 7);
            }
        }
        return $value;
    }
}