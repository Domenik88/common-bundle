<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use KMGi\CommonBundle\Form\EventListener\MultiCollectionFormListener;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\OptionsResolver\Options;

class MultiCollectionType extends AbstractType
{
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['allow_add'] && $options['prototypes'])
        {
            $prototypes = [];
            foreach($options['types'] as $key => $_formType)
            {
                $prototypes[$key] = $builder
                    ->create(
                        $options['prototype_name'],
                        $_formType,
                        array_replace(
                            [
                                'label' => "{$options['prototype_name']}label__",
                            ],
                            $options['options']
                        )
                    )
                    ->getForm()
                    ->setData(array_key_exists($key, $options['prototypes_data']) ? $options['prototypes_data'][$key] : $options['prototype_data'])
                ;
            }
            $builder->setAttribute("prototypes", $prototypes);
        }

        foreach($builder->getEventDispatcher()->getListeners() as $_event => $_listeners)
        {
            foreach($_listeners as $_listener)
            {
                if($_listener[0] instanceof ResizeFormListener)
                {
                    $builder->getEventDispatcher()->removeListener($_event, $_listener);
                }
            }
        }

        $resizeListener = new MultiCollectionFormListener(
            $options['types'],
            $this->propertyAccessor,
            $options['object_type_property'],
            $options['type_hidden_field'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if($options['js_type_selector'])
        {
            $view->vars['attr']['data-type-selector'] = $options['js_type_selector'];
        }
        if($form->getConfig()->hasAttribute("prototypes"))
        {
            foreach($form->getConfig()->getAttribute("prototypes") as $_key => $_formType)
            {
                $view->vars["prototypes"][$_key] = $_formType->createView($view);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'types' => [],
                'prototype' => false,
                'prototypes' => true,
                'js_type_selector' => false,
                'prototypes_data' => [],
            ])
            ->setRequired([
                'type_hidden_field',
                'object_type_property',
            ])
            ->setAllowedTypes([
                'types' => 'array',
                'prototype' => 'bool',
                'prototypes' => 'bool',
                'js_type_selector' => ['string', 'bool'],
                'type_hidden_field' => 'string',
                'object_type_property' => 'string',
                'add_button_label' => ['string', 'array'],
                'prototypes_data' => 'array',
            ])
            ->setNormalizers([
                'js_type_selector' => function(Options $options, $value)
                {
                    return is_bool($value) ? false : $value;
                },
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'multi_collection';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'collection';
    }
}
