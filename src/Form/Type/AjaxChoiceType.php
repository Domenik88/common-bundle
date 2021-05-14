<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use KMGi\CommonBundle\Form\DataTransformer\AjaxChoiceTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Routing\RouterInterface;

class AjaxChoiceType extends AbstractType
{
    private $em;
    private $propertyAccessor;
    private $router;

    public function __construct(ObjectManager $em, PropertyAccessorInterface $propertyAccessor, RouterInterface $router)
    {
        $this->em = $em;
        $this->propertyAccessor = $propertyAccessor;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new AjaxChoiceTransformer($this->em, $options['class']))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['search_route_name'] = $options['search_route_name'];
        $view->vars['search_param_name'] = $options['search_param_name'];
        $view->vars['empty_placeholder'] = $options['empty_placeholder'];
        $view->vars['view_url'] = false;
        $data = $view->vars['data'];
        if(!is_null($data))
        {
            $view->vars['property_value'] = $this->propertyAccessor->getValue($data, $options['property']);
            if($options['view_route'])
            {
                $routeNameFunction = $options['view_route'];
                if($routeName = $routeNameFunction($data))
                {
                    $view->vars['view_url'] = $this->router->generate($routeName, [$options['view_route_key'] => $this->propertyAccessor->getValue($data, $options['view_route_key'])]);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired([
                'search_route_name',
                'search_param_name',
                'property',
                'class',
            ])
            ->setDefaults([
                'empty_placeholder' => 'Type here to search',
                'view_route' => false,
                'view_route_key' => 'id',
            ])
            ->setAllowedTypes([
                'search_route_name' => 'string',
                'search_param_name' => 'string',
                'property' => 'string',
                'class' => 'string',
                'empty_placeholder' => 'string',
                'view_route' => ['bool', 'string', 'callable'],
                'view_route_key' => 'string',
            ])
            ->setNormalizers([
                'view_route' => function(Options $options, $value)
                    {
                        switch(true)
                        {
                            case is_bool($value):
                                return false;
                            case is_string($value):
                                return function($object) use ($value)
                                    {
                                        return $value;
                                    };
                            default:
                                return $value;
                        }
                    },
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ajax_choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }
}