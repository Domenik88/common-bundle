<?php 
namespace KMGi\CommonBundle\Extensions\Twig;

class Sha1Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sha1_encode', array($this, 'sha1Encode')),
        );
    }

    public function sha1Encode($string)
    {
        return sha1($string);
    }

    public function getName()
    {
        return 'sha1_extension';
    }
}