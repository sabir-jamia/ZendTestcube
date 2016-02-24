<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class DashboardController extends AbstractActionController
{

    protected $recentDetails = null;

    protected $dashUpdates = null;

    public function indexAction()
    {
        $userSession = new Container ('users');
        $userData2 = $this->getDashUpdates()->getClientUserByEmail ($userSession->email);
        $userSession->id = $userData2->id;
        $host = $userSession->id;
        $clienthost = $userSession->clientId;
        $recentLinkDetails= $this->getRecentDetails()->fetchRecentLinkDetails();
        
        $countData = $this->getRecentDetails()->fetchCounts();
        $count =0;
        foreach ($recentLinkDetails as $linkDetails) {
        	$linkId = $linkDetails['linkId'];
            $code   = $linkDetails['linkCode'];
            $showUntill = $linkDetails['showUntil'];
            $link[$count]['linkUrl'] = $this->getBaseUrl().'student/quiz/'. $clienthost.'/'.$linkId.'/'.$code;
            $link[$count]['showuntill'] = $showUntill;
            $count++;
        }

        $arrData['testData'] = $this->getRecentDetails()->fetchRecentTestDetails();
        $arrData['resultData'] = $this->getRecentDetails()->fetchRecentResultDetails();
        
        $resultDataTemp = array();
        
        foreach($arrData['resultData'] as $k => $v)
        {
        	$resultDataTemp[$v['testid']]= $v['testname'];
        }
        
        $resultDataTemp = array_unique($resultDataTemp);
       
        
        $arrData['linkData'] = isset($link)?$link:array();
        $arrData['updatesData'] = $this->getDashUpdates()->getdashUpdates();

        $view = new ViewModel(array('arrData' => $arrData, 
              						'countData' => $countData,
        		                    'resultDataTemp' => $resultDataTemp
              				  ));
        return $view;
    }

    public function getRecentDetails()
    {
        if (! $this->recentDetails) {
                	$sm = $this->getServiceLocator();
                	$this->recentDetails = $sm->get('Dashboard\Model\RecentDetails');

                }
                return $this->recentDetails;
    }

    public function getDashUpdates()
    {
        if (! $this->dashUpdates) {
        			$serviceManager = $this->getServiceLocator ();
        			$this->dashUpdates = $serviceManager->get ( 'Dashboard\Model\DashUpdates' );
        		}
        		return $this->dashUpdates;
    }

    public function getBaseUrl()
    {
        $sm = $this->getServiceLocator ();
        		$config = $sm->get ( 'config' );
        		return $config ['applicationSettings'] ['appLink'];
    }

    public function showAllUpdatesAction()
    {
    	$arrData = $this->getDashUpdates()->getAlldashUpdates();
        $view = new ViewModel(array('updatesData' => $arrData));
        return $view;
    }


}

