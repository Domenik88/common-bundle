<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use KMGi\CommonBundle\EventListener\LocaleListener;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class LocaleDateTimeTypeExtension extends AbstractTypeExtension
{
    private $pattern;
    private $locale;

    public function __construct(LocaleListener $listener)
    {
        $this->pattern = $listener->getDateTimePattern();
        $this->locale = $listener->getLocale();
    }

    protected function transformDateTimePattern($intlPattern)
    {
        return preg_replace(['/d/', '/y/', '/(?<!Y)Y(?!Y)/', '/\'/', '/Z+/', '/a/'], ['D', 'Y', 'YYYY', '', 'Z', 'A'], $intlPattern);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'datetime';
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
                'time_format' => null,
                'format' => $this->pattern,
                'datetimepicker_options' => [],
            ])
            ->setAllowedTypes([
                'minute_step' => 'integer',
                'datetimepicker_options' => 'array',
            ])
            ->setNormalizers([
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
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if('single_text' === $options['widget'])
        {
            $view->vars['attr'] = array_merge(
                $view->vars['attr'],
                [
                    'data-date-format' => $this->transformDateTimePattern(is_string($options['format']) && $options['format'] ? $options['format'] : $this->pattern),
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

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if('single_text' !== $options['widget'] && !is_null($options['time_format']))
        {
            $timeOptions = array_intersect_key(
                $builder->get('time')->getOptions(),
                array_flip(array(
                    'hours',
                    'minutes',
                    'seconds',
                    'with_minutes',
                    'with_seconds',
                    'empty_value',
                    'required',
                    'translation_domain',
                    'input',
                    'error_bubbling',
                    'widget',
                ))
            );
            $builder->add('time', 'time', array_merge($timeOptions, ['format' => $options['time_format']]));
        }
    }
}
