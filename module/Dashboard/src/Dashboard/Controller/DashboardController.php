<?php
namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class DashboardController extends AbstractActionController
{
    public function indexAction()
    {
        $userSession = new Container('users');
        $clienthost = $userSession->clientId;
        $recentDetails = $this->serviceLocator->get('Dashboard\Model\RecentDetails');
        $dashUpdates= $this->serviceLocator->get('Dashboard\Model\DashUpdates');
        $recentLinkDetails = $recentDetails->fetchRecentLinkDetails();   
        $countData = $recentDetails->fetchCounts();
        $count = 0;
        
        foreach ($recentLinkDetails as $linkDetails) {
            $linkId = $linkDetails['linkId'];
            $code = $linkDetails['linkCode'];
            $showUntill = $linkDetails['showUntil'];
            $link[$count]['linkUrl'] = $this->getBaseUrl() . 'student/quiz/' . $clienthost . '/' . $linkId . '/' . $code;
            $link[$count]['showuntill'] = $showUntill;
            $count ++;
        }
        
        $arrData['testData'] = $recentDetails->fetchRecentTestDetails();
        $arrData['resultData'] = $recentDetails->fetchRecentResultDetails();
        $resultDataTemp = array();
        
        foreach ($arrData['resultData'] as $k => $v) {
            $resultDataTemp[$v['testid']] = $v['testname'];
        }
        
        $resultDataTemp = array_unique($resultDataTemp);
        $arrData['linkData'] = isset($link) ? $link : array();
        $arrData['updatesData'] = $dashUpdates->getdashUpdates();
        
        $view = new ViewModel(array(
            'arrData' => $arrData,
            'countData' => $countData,
            'resultDataTemp' => $resultDataTemp
        ));
        return $view;
    }

    public function showAllUpdatesAction()
    {
        $dashUpdates= $this->serviceLocator->get('Dashboard\Model\DashUpdates');
        $arrData = $dashUpdates->getAlldashUpdates();
        $view = new ViewModel(array(
            'updatesData' => $arrData            
        ));
        return $view;
    }

    public function getBaseUrl()
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get('config');
        return $config['applicationSettings']['appLink'];
    }
}