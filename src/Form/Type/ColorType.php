<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use KMGi\CommonBundle\Form\DataTransformer\ColorTransformer;

class ColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new ColorTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'color';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }
}