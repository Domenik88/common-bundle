<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use KMGi\CommonBundle\EventListener\LocaleListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;

class LocaleTimeTypeExtension extends AbstractTypeExtension
{
    private $pattern;
    private $locale;

    public function __construct(LocaleListener $listener)
    {
        $this->pattern = $listener->getTimePattern();
        $this->locale = $listener->getLocale();
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'time';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $pattern = $this->pattern;
        $resolver
            ->setDefaults([
                'minute_step' => 1,
                'format' => false,
                'datetimepicker_options' => [],
            ])
            ->setAllowedTypes([
                'minute_step' => 'integer',
                'format' => [
                    'bool',
                    'string',
                ],
                'datetimepicker_options' => 'array',
            ])
            ->setNormalizers([
                'format' => function(Options $options, $value) use ($pattern)
                    {
                        return is_bool($value) ? $pattern : $value;
                    },
                'with_minutes' => function(Options $options, $value) use ($pattern)
                    {
                        return $options['widget'] == 'single_text' ? strpos($options['format'], 'm') > 0 : $value;
                    },
                'with_seconds' => function(Options $options, $value) use ($pattern)
                    {
                        return $options['widget'] == 'single_text' ? strpos($options['format'], 's') > 0 : $value;
                    },
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if('single_text' === $options['widget'])
        {
            $format = preg_replace(
                ['/(?<!m)m{1,2}(?!m)/', '/(?<!s)s{1,2}(?!s)/', '/(?<!h)h(?!h)/', '/(?<!h)hh(?!h)/', '/(?<!H)H(?!H)/', '/(?<!H)HH(?!H)/', '/a/'],
                ['i', 's', 'g', 'h', 'G', 'H', 'A'],
                $options['format']
            );
            $builder
                ->resetViewTransformers()
                ->addViewTransformer(new DateTimeToStringTransformer('UTC', 'UTC', $format))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if('single_text' === $options['widget'])
        {
            $view->vars['attr'] = array_merge(
                $view->vars['attr'],
                [
                    'data-date-format' => str_replace('a', 'A', is_string($options['format']) && $options['format'] ? $options['format'] : $this->pattern),
                    'data-date-pickDate' => 'false',
                    'data-date-language' => $this->locale,
                    'data-date-minuteStepping' => $options['minute_step'],
                    'data-date-useMinutes' => $options['with_minutes'] === false ? 'false' : true,
                    'data-date-useSeconds' => $options['with_seconds'] === false ? 'false' : true,
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