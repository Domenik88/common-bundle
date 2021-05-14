<?php
namespace KMGi\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener as ResizeFormListenerOriginal;
use KMGi\CommonBundle\Form\DataTransformer\TagTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Routing\RouterInterface;
use KMGi\CommonBundle\Extensions\Twig\ZendJsonExpr;
use Symfony\Component\Translation\TranslatorInterface;
use KMGi\CommonBundle\Extensions\Twig\ZendJsonEncode;

class TagsType extends AbstractType
{
    private $em;
    private $properyAccessor;
    private $translator;
    private $router;
    private $jsonEncoder;

    public function __construct(EntityManager $em, PropertyAccessorInterface $propertyAccessor, TranslatorInterface $translator, RouterInterface $router, ZendJsonEncode $jsonEncoder)
    {
        $this->em = $em;
        $this->properyAccessor = $propertyAccessor;
        $this->translator = $translator;
        $this->router = $router;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new TagTransformer($this->em, $this->properyAccessor, $options));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $propertyAccessor = $this->properyAccessor;
        $resolver
            ->setDefaults([
                'allow_new_tags' => false,
                'tag_label' => 'name',
                'empty_placeholder' => 'Enter tag here',
                'tag_search_router' => '',
                'tag_search_router_params' => [],
                'tag_search_param' => 'tag',
                'existing_tag_list' => [],
                'required' => false,
                'tag_to_string_transformer' => null,
                'minimum_input_length' => 3,
                'additional_fields' => [],
            ])
            ->setRequired(['tag_data_class'])
            ->setAllowedTypes([
                'allow_new_tags' => 'bool',
                'tag_data_class' => 'string',
                'tag_label' => 'string',
                'tag_search_router' => 'string',
                'tag_search_router_params' => 'array',
                'tag_search_param' => 'string',
                'existing_tag_list' => 'array',
                'tag_to_string_transformer' => 'callable',
                'minimum_input_length' => 'int',
                'additional_fields' => 'array',
            ])
            ->setNormalizers([
                'tag_to_string_transformer' => function(Options $options, $value) use ($propertyAccessor)
                {
                    if(is_null($value))
                    {
                        $value = function($tag) use ($propertyAccessor, $options)
                        {
                            return $propertyAccessor->getValue($tag, $options['tag_label']);
                        };
                    }
                    return $value;
                },
                'existing_tag_list' => function(Options $options, $value)
                {
                    return array_map(
                        function($element) use ($options)
                        {
                            $element = $options['tag_to_string_transformer']($element);
                            return [
                                'id' => str_replace(',', '&#44;', $element),
                                'text' => $element,
                            ];
                        },
                        $value
                    );
                },
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $initSelection = new ZendJsonExpr(str_replace("\n", '',
<<<ZendJsonExpr
function (element, callback) {
    var data = [];
    $(element.val().split(",")).each(function () {
        data.push({id: this, text: this.split('&#44;').join(',')});
    });
    callback(data);
}
ZendJsonExpr
        ));
        $select2Options = [
            'placeholder' => $this->translator->trans($options['empty_placeholder'], [], $options['translation_domain']),
            'minimumInputLength' => $options['minimum_input_length'],
            'tags' => [],
            'initSelection' => $initSelection,
        ];
        if($options['allow_new_tags'])
        {
            $createSearchChoice = new ZendJsonExpr(str_replace("\n", '',
<<<ZendJsonExpr
function(term, data) {
    if($(data).filter(function(){return this.text.localeCompare(term)===0;}).length===0) {
        return {id:term, text:term};
    }
}
ZendJsonExpr
            ));
            $select2Options['createSearchChoice'] = $createSearchChoice;
        }
        if($options['tag_search_router'])
        {
            $data = new ZendJsonExpr(str_replace("\n", '',
<<<ZendJsonExpr
function(term){
    return {
        {$options['tag_search_param']}: term
    };
}
ZendJsonExpr
            ));
            $results = new ZendJsonExpr(str_replace("\n", '',
<<<ZendJsonExpr
function(data) {
    var results = [];
    $.each(data, function(index, item){
        results.push({
            id: item.split(',').join('&#44;'),
            text: item
        });
    });
    return {
        results: results
    };
}
ZendJsonExpr
            ));
            $select2Options['ajax'] = [
                'url' => $this->router->generate($options['tag_search_router'], $options['tag_search_router_params']),
                'dataType' => 'json',
                'quietMillis' => 100,
                'data' => $data,
                'results' => $results,
            ];
        }
        elseif($options['existing_tag_list'])
        {
            $select2Options['data'] = $options['existing_tag_list'];
        }
        $view->vars['attr'] = array_merge(
            (array)$view->vars['attr'],
            [
                'class' => trim((is_array($view->vars['attr']) && array_key_exists('class', $view->vars['attr']) ? $view->vars['attr']['class'] : '') . ' js-select2'),
                'data-select2-options' => $this->jsonEncoder->zend_json_encode($select2Options),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'tags';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }
}