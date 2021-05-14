<?php
namespace KMGi\CommonBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;

class CollectionTypeExtension extends AbstractTypeExtension
{
    const ADD_FIRST = 'add_first';
    const ADD_LAST = 'add_last';

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(!is_null($prototypeBuilder = $builder->getAttribute('prototype')))
        {
            $prototypeBuilder->setData($options['prototype_data']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'show_add_button' => true,
                'add_button_label' => 'Add Record',
                'no_data_message' => 'No records yet',
                'add_button_icon' => 'plus',
                'bootstrap_panel' => true,
                'prototype_data' => null,
                'add_type' => self::ADD_FIRST,
            ])
            ->setAllowedTypes([
                'show_add_button' => 'bool',
                'add_button_label' => 'string',
                'no_data_message' => 'string',
                'add_button_icon' => 'string',
            ])
            ->setAllowedValues([
                'add_type' => [
                    self::ADD_FIRST,
                    self::ADD_LAST,
                ]
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $id = $view->vars['id'];
        $view->vars['show_add_button'] = $options['show_add_button'];
        $view->vars['add_button_label'] = $options['add_button_label'];
        $view->vars['no_data_message'] = $options['no_data_message'];
        $view->vars['add_button_icon'] = $options['add_button_icon'];
        $view->vars['add_button_class'] = "js-{$id}-add_record";
        $view->vars['remove_button_class'] = "js-{$id}-remove_item";
        $view->vars['collection_container_id'] = "{$id}-collection_container";
        $view->vars['prototype_name'] = $options['prototype_name'];
        $view->vars['attr'] = array_merge((array)$view->vars['attr'], [
            'class' => trim((is_array($view->vars['attr']) && array_key_exists('class', $view->vars['attr']) ? $view->vars['attr']['class'] : '') . ' js-collection_row'),
            'data-allow-add' => $options['allow_add'],
            'data-allow-delete' => $options['allow_delete'],
            'data-add-button-class' => $view->vars['add_button_class'],
            'data-remove-button-class' => $view->vars['remove_button_class'],
            'data-prototype-name' => $options['prototype_name'],
            'data-container-id' => $view->vars['collection_container_id'],
            'data-add-type' => $options['add_type'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-next-child-index'] = count($view->children) === 0 ? 0 : (max(array_keys($view->children)) + 1);
    }
}