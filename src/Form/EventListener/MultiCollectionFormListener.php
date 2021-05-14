<?php
namespace KMGi\CommonBundle\Form\EventListener;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class MultiCollectionFormListener extends ResizeFormListener
{
    protected $types;
    protected $propertyAccessor;
    protected $typeProperty;
    protected $typeField;

    public function __construct(array $types = array(), PropertyAccessorInterface $propertyAccessor, $typeProperty, $typeField, array $options = array(), $allowAdd = false, $allowDelete = false)
    {
        $this->types = $types;
        $this->propertyAccessor = $propertyAccessor;
        $this->typeProperty = $typeProperty;
        $this->typeField = $typeField;
        $this->allowAdd = $allowAdd;
        $this->allowDelete = $allowDelete;
        $this->options = $options;
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if(null === $data)
        {
            $data = [];
        }

        if(!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess))
        {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // First remove all rows
        foreach($form as $name => $child)
        {
            $form->remove($name);
        }

        // Then add all rows again in the correct order
        foreach($data as $name => $value)
        {
            $type = $this->propertyAccessor->getValue($value, $this->typeProperty);
            if(array_key_exists($type, $this->types))
            {
                $form->add(
                    $name,
                    $this->types[$this->propertyAccessor->getValue($value, $this->typeProperty)],
                    array_replace(
                        [
                            'property_path' => '['.$name.']',
                        ],
                        $this->options
                    )
                );
            }
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if(null === $data || '' === $data)
        {
            $data = array();
        }

        if(!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess))
        {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // Remove all empty rows
        if($this->allowDelete)
        {
            foreach($form as $name => $child)
            {
                if(!isset($data[$name]))
                {
                    $form->remove($name);
                }
            }
        }

        // Add all additional rows
        if($this->allowAdd)
        {
            foreach($data as $name => $value)
            {
                if(!$form->has($name))
                {
                    $form->add(
                        $name,
                        $this->types[$value[$this->typeField]],
                        array_replace(
                            [
                                'property_path' => '['.$name.']',
                            ],
                            $this->options
                        )
                    );
                }
            }
        }
    }
}