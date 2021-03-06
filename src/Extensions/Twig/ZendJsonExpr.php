<?php
namespace KMGi\CommonBundle\Extensions\Twig;

class ZendJsonExpr
{
    protected $_expression;

    public function __construct($expression)
    {
        $this->_expression = (string)$expression;
    }

    public function __toString()
    {
        return $this->_expression;
    }
}