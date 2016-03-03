<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class HeadPlugin extends AbstractPlugin
{

    public function javaScript()
    {
        return $this->getController()
            ->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript');
    }
}