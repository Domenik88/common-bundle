<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class EntityTypeExtension extends AbstractTypeExtension
{
    private $propertyAccessor;
    private $router;

    public function __construct(PropertyAccessorInterface $propertyAccessor, RouterInterface $router)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'entity';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'view_route' => false,
                'view_route_key' => 'id',
            ])
            ->setAllowedTypes([
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['view_url'] = false;
        $data = $view->vars['data'];
        if(!is_null($data) && $options['view_route'])
        {
            $routeNameFunction = $options['view_route'];
            if($routeName = $routeNameFunction($data))
            {
                $view->vars['view_url'] = $this->router->generate($routeName, [$options['view_route_key'] => $this->propertyAccessor->getValue($data, $options['view_route_key'])]);
            }
        }
    }
}