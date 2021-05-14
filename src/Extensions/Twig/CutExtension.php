<?php
namespace KMGi\CommonBundle\Extensions\Twig;

use Twig\Extension\AbstractExtension;

class CutExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'cut' => new \Twig_SimpleFilter('filterCut', array($this, 'filterCut')),
        );
    }

    /**
     * @param string $text
     * @param integer $length
     * @param boolean $wordCut
     * @param string $appendix
     * @return string
     */
    public function filterCut($text, $length = 160, $wordCut = true, $appendix = '...')
    {
        $maxLength = (int)$length - mb_strlen($appendix,'UTF-8');
        if (mb_strlen($text, 'UTF-8') > $maxLength) {
            if($wordCut){
                $text = mb_substr($text, 0, $maxLength + 1,'UTF-8');
                $text = mb_substr($text, 0, mb_strrpos($text, ' ','UTF-8'),'UTF-8');
            }
            else {
                $text = mb_substr($text, 0, $maxLength,'UTF-8');
            }
            $text .= $appendix;
        }

        return $text;
    }

    public function getName()
    {
        return 'kmgi.common_bundle.twig.cut_extension';
    }
}