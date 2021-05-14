<?php
namespace KMGi\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;

class CollectionUniqueTransformer implements DataTransformerInterface
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
        if(!$value instanceof Collection)
        {
            return $value;
        }
        $ids = [];
        foreach($value as $_value)
        {
            if(in_array($_value->getId(), $ids))
            {
                $value->removeElement($_value);
            }
            else
            {
                $ids[] = $_value->getId();
            }
        }
        return $value;
    }
}