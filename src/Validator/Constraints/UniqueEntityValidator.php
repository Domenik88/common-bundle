<?php
namespace KMGi\CommonBundle\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator as UEV;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class UniqueEntityValidator extends UEV
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        if(!is_array($constraint->fields) && !is_string($constraint->fields))
        {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        $fields = (array) $constraint->fields;

        if($constraint->em)
        {
            $em = $this->registry->getManager($constraint->em);
        }
        else
        {
            $em = $this->registry->getManagerForClass(get_class($entity));
        }

        $className = $this->context->getClassName();
        /* @var $class \Doctrine\Common\Persistence\Mapping\ClassMetadata */
        $class = $em->getClassMetadata($className);

        foreach($fields as $fieldName)
        {
            if(!$class->hasField($fieldName) && !$class->hasAssociation($fieldName))
            {
                throw new ConstraintDefinitionException(sprintf("The field '%s' is not mapped by Doctrine, so it cannot be validated for uniqueness.", $fieldName));
            }

            if($constraint->ignoreFalseLikeValues && !$class->reflFields[$fieldName]->getValue($entity))
            {
                return;
            }
        }
        parent::validate($entity, $constraint);
    }
}