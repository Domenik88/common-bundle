<?php
namespace KMGi\CommonBundle\Extensions\Twig;

class ZendJsonEncode extends \Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('zend_json_encode', array($this, 'zend_json_encode') ),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'kmgi.common_bundle.twig.zend_json_encode';
    }

    public function zend_json_encode($value, $options = 0)
    {
        $javascriptExpressions = [];
        $encodedResult = json_encode($this->_recursiveJsonExprFinder($value, $javascriptExpressions), $options);
        if(count($javascriptExpressions) > 0)
        {
            $count = count($javascriptExpressions);
            for($i = 0; $i < $count; $i++)
            {
                $magicKey = $javascriptExpressions[$i]['magicKey'];
                $value    = $javascriptExpressions[$i]['value'];
                $encodedResult = str_replace('"' . $magicKey . '"', $value, $encodedResult);
            }
        }
        return $encodedResult;
    }

    protected function _recursiveJsonExprFinder($value, array &$javascriptExpressions, $currentKey = null)
    {
        if($value instanceof ZendJsonExpr)
        {
            $magicKey = "____" . $currentKey . "_" . (count($javascriptExpressions));
            $javascriptExpressions[] = [
                "magicKey" => $magicKey,
                "value"    => $value->__toString(),
            ];
            $value = $magicKey;
        }
        elseif(is_array($value))
        {
            foreach($value as $k => $v)
            {
                $value[$k] = $this->_recursiveJsonExprFinder($value[$k], $javascriptExpressions, $k);
            }
        }
        elseif(is_object($value))
        {
            foreach($value as $k => $v)
            {
                $value->$k = $this->_recursiveJsonExprFinder($value->$k, $javascriptExpressions, $k);
            }
        }
        return $value;
    }
}
