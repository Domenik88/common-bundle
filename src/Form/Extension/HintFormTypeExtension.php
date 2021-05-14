<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;

class HintFormTypeExtension extends AbstractTypeExtension
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
                'text_hint' => false,
                'text_hint_escaped' => true,
            ])
            ->setAllowedTypes([
                'text_hint' => ['bool', 'string', 'array'],
                'text_hint_escaped' => 'bool',
            ])
            ->setNormalizers([
                'text_hint' => function(Options $options, $value)
                    {
                        if(is_bool($value))
                        {
                            return false;
                        }
                        $value = (array)$value;
                        $result = ['text' => $value[0]];
                        unset($value[0]);
                        $result['params'] = $value;
                        $result['text_hint_escaped'] = $options['text_hint_escaped'];
                        return $result;
                    },
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['text_hint'] = $options['text_hint'];
    }
}