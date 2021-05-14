<?php
namespace KMGi\CommonBundle\Menu\Voter;

use Knp\Menu\Matcher\Voter\VoterInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

class RouteVoter implements VoterInterface
{
    private $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function matchItem(ItemInterface $item)
    {
        if($item->isCurrent())
        {
            return true;
        }
        foreach($item->getExtra('routes', array()) as $_route)
        {
            if(isset($_route['route']) && strpos($this->request->attributes->get('_route'), $_route['route']) === 0)
            {
                if($this->request->attributes->get('_route') == $_route['route'])
                {
                    return null;
                }
                return true;
            }
        }
        return null;
    }
}