<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\Common\Persistence\ObjectManager;

class TreeEntityType extends AbstractType
{
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tree_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $self = $this;
        if($selectedPath = $options['selected_path'])
        {
            $view->vars['selected_dictionary'] = array_map(
                function ($element) use ($self, $selectedPath)
                {
                    return $self->propertyAccessor->getValue($element, $selectedPath);
                },
                $options['choice_list']->getChoices()
            );
        }
        $choices = $options['choice_list']->getChoices();
        $treeData = [];
        foreach($choices as $_id => $_item)
        {
            $treeData[$_id] =
            [
                'id' => $_id,
                'parentId' => array_search($this->propertyAccessor->getValue($_item, $options['parent_path']), $choices),
                'text' => $this->propertyAccessor->getValue($_item, $options['property']),
            ];
        }
        foreach($treeData as &$_item)
        {
            if($_item['parentId'] !== false)
            {
                $treeData[$_item['parentId']]['children'][] = &$_item;
            }
        }
        foreach($treeData as $_id => &$_item)
        {
            if($_item['parentId'] !== false)
            {
                unset($treeData[$_id]);
            }
            unset($_item['parentId']);
        }
        $treeData = array_values($treeData);
        $view->vars['tree_data'] = $treeData;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'selected_path' => false,
                'parent_path' => 'parent',
            ])
            ->addAllowedTypes([
                'parent_path' => 'string',
                'selected_path' => [
                    'bool',
                    'string',
                ],
            ])
            ->setNormalizers([
                'selected_path' => function(Options $options, $value)
                {
                    return is_bool($value) ? false : $value;
                },
            ])
        ;
    }

}