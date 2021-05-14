<?php
namespace KMGi\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Doctrine\Common\Collections\ArrayCollection;

class TagTransformer implements DataTransformerInterface
{
    private $em;
    private $propertyAccessor;
    private $options;

    public function __construct(EntityManager $em, PropertyAccessorInterface $propertyAccessor, $options)
    {
        $this->em = $em;
        $this->propertyAccessor = $propertyAccessor;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if(is_null($value))
        {
            return $value;
        }
        if($value instanceof Collection)
        {
            $tagToStringTransformer = $this->options['tag_to_string_transformer'];
            return implode(',', array_map(
                function($elem) use ($tagToStringTransformer)
                {
                    return str_replace(',', '&#44;', $tagToStringTransformer($elem));
                },
                $value->toArray()
            ));
        }
        throw new \InvalidArgumentException("Unsupported value");
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if(is_null($value))
        {
            return new ArrayCollection();
        }
        $value = array_map(
            function($elem)
            {
                return str_replace('&#44;', ',', $elem);
            },
            explode(',', $value)
        );
        $value = array_unique($value);
        $foundTags = $this->findTagsByValue($value);
        $result = [];
        $pa = $this->propertyAccessor;
        $property = $this->options['tag_label'];
        $tagClass = $this->em->getClassMetadata($this->options['tag_data_class'])->getReflectionClass()->name;
        foreach($value as $_key => $_value)
        {
            $tag = array_filter(
                $foundTags,
                function($elem) use ($_value, $pa, $property)
                {
                    return $_value === $pa->getValue($elem, $property);
                }
            );
            if(count($tag) != 0)
            {
                $result[$_key] = reset($tag);
                unset($foundTags[key($tag)]);
            }
            elseif($this->options['allow_new_tags'])
            {
                $tag = new $tagClass();
                foreach(array_merge([$this->options['tag_label'] => $_value], $this->options['additional_fields']) as $_field => $_fieldValue)
                {
                    $this->propertyAccessor->setValue($tag, $_field, $_fieldValue);
                }
                $result[$_key] = $tag;
            }
        }
        ksort($result);
        return new ArrayCollection($result);
    }

    /**
     * Get all possible tags from database
     *
     * @param array $value array of strings
     * @return array array of Tag objects
     */
    protected function findTagsByValue($value)
    {
        return $this->em->getRepository($this->options['tag_data_class'])
            ->findBy(
                array_merge(
                    [
                        $this->options['tag_label'] => $value,
                    ],
                    $this->options['additional_fields']
                )
            )
        ;
    }
}