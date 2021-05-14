<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class TabbedFormType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'tabbed_form';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'form';
    }
}