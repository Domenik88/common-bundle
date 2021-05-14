<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class BootstrapPanelFormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'bootstrap_panel' => false,
                'bootstrap_grid_type' => 'sm',
                'bootstrap_grid_size' => 12,
                'bootstrap_widget_grid_size' => 10,
            ])
            ->setAllowedTypes([
                'bootstrap_panel' => 'bool',
                'bootstrap_grid_size' => 'int',
                'bootstrap_widget_grid_size' => 'int',
            ])
            ->setAllowedValues([
                'bootstrap_grid_type' => [
                    'xs',
                    'sm',
                    'md',
                    'lg',
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['bootstrap_grid_row'] = "col-{$options['bootstrap_grid_type']}-{$options['bootstrap_grid_size']}";
        if($options['bootstrap_panel'])
        {
            $class = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'] : '';
            $view->vars['attr']['class'] = trim("{$class} {$view->vars['bootstrap_grid_row']}");
        }
        $view->vars['bootstrap_panel'] = $options['bootstrap_panel'];
        $view->vars['bootstrap_widget_grid_size'] = $options['bootstrap_widget_grid_size'];
    }
}