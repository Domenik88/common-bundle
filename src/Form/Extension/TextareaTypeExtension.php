<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class TextareaTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'textarea';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'show_as_html' => false,
            ])
            ->setAllowedTypes([
                'show_as_html' => 'bool',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['show_as_html'] = $options['show_as_html'];
    }
}