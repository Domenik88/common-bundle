<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class IncludeTwigType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'include_twig';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'mapped' => false,
            ])
            ->setRequired([
                'twig_template',
            ])
            ->setAllowedTypes([
                'twig_template' => 'string',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['twig_template'] = $options['twig_template'];
    }
}