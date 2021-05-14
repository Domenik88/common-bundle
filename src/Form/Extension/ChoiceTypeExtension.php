<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use KMGi\CommonBundle\Extensions\Twig\ZendJsonExpr;
use Symfony\Component\Translation\TranslatorInterface;
use KMGi\CommonBundle\Extensions\Twig\ZendJsonEncode;

class ChoiceTypeExtension extends AbstractTypeExtension
{
    private $translator;
    private $jsonEncoder;

    public function __construct(TranslatorInterface $translator, ZendJsonEncode $jsonEncoder)
    {
        $this->translator = $translator;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'use_select2' => false,
                'empty_placeholder' => false,
                'hide_search_box' => false,
                'minimum_results_for_search' => 0,
                'extra_attributes' => false,
            ])
            ->setAllowedTypes([
                'use_select2' => 'bool',
                'empty_placeholder' => [
                    'bool',
                    'string',
                ],
                'hide_search_box' => 'bool',
                'minimum_results_for_search' => 'int',
                'extra_attributes' => [
                    'bool',
                    'array',
                ],
            ])
            ->setNormalizers([
                'empty_placeholder' => function(Options $options, $value)
                    {
                        return is_bool($value) ? false : $value;
                    },
                'minimum_results_for_search' => function(Options $options, $value)
                    {
                        return $options['hide_search_box'] || $value < 0 ? -1 : $value;
                    },
                'extra_attributes' => function(Options $options, $value)
                    {
                        return is_bool($value) ? false : $value;
                    },
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['use_select2'] = $options['use_select2'];
        $view->vars['empty_placeholder'] = $options['empty_placeholder'];

        if($options['use_select2'])
        {
            $select2Options = array_merge(
                [
                    'placeholder' => $this->translator->trans($options['empty_placeholder'], [], $options['translation_domain']),
                ],
                !$options['multiple'] && $options['empty_placeholder'] && $options['empty_value'] ?
                    [
                        'placeholderOption' => new ZendJsonExpr('function(select){return select.children("option[value=placeholder]").first();}'),
                    ] :
                    [],
                !$options['multiple'] && $options['minimum_results_for_search'] ?
                    [
                        'minimumResultsForSearch' => $options['minimum_results_for_search'],
                    ] :
                    [],
                $options['extra_attributes'] ?: []
            );
            $view->vars['attr'] = array_merge(
                (array)$view->vars['attr'],
                [
                    'class' => trim((is_array($view->vars['attr']) && array_key_exists('class', $view->vars['attr']) ? $view->vars['attr']['class'] : '') . ' js-select2'),
                    'data-select2-options' => $this->jsonEncoder->zend_json_encode($select2Options),
                ]
            );
        }
    }
}
