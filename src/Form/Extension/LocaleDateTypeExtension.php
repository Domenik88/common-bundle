<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use KMGi\CommonBundle\EventListener\LocaleListener;

class LocaleDateTypeExtension extends AbstractTypeExtension
{
    private $pattern;
    private $locale;

    public function __construct(LocaleListener $listener)
    {
        $this->pattern = $listener->getDatePattern();
        $this->locale = $listener->getLocale();
    }

    protected function transformDatePattern($intlPattern)
    {
        return preg_replace(['/d/', '/y/', '/(?<!Y)Y(?!Y)/', '/\'/'], ['D', 'Y', 'YYYY', ''], $intlPattern);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'date';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'format' => DateType::DEFAULT_FORMAT,
                'datetimepicker_options' => [],
            ])
            ->setAllowedTypes([
                'datetimepicker_options' => 'array',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if('single_text' === $options['widget'])
        {
            $view->vars['attr'] = array_merge(
                $view->vars['attr'],
                [
                    'data-date-format' => $this->transformDatePattern(is_string($options['format']) && $options['format'] ? $options['format'] : $this->pattern),
                    'data-date-pickTime' => 'false',
                    'data-date-language' => $this->locale,
                ],
                array_combine(
                    array_map(
                        function($key)
                        {
                            return "data-date-{$key}";
                        },
                        array_keys($options['datetimepicker_options'])
                    ),
                    $options['datetimepicker_options']
                )
            );
        }
    }
}