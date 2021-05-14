<?php
namespace KMGi\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class AjaxChoiceTransformer implements DataTransformerInterface
{
    private $em;
    private $class;
    private $property;

    public function __construct(ObjectManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        $class = $this->em->getClassMetadata($this->class)->getReflectionClass()->name;
        if(is_null($value) || !$value instanceof $class)
        {
            return null;
        }
        return $value->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
		if(is_null($value))
		{
			return null;
		}
		return $this->em->getRepository($this->class)->find($value);
    }
}