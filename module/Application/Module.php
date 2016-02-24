<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Model\ApplicationTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\Pdo\Pdo;
use Zend\Db\Adapter\Adapter;

use Zend\Session\Container;

class Module
{
    protected $config;

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $sm = $e->getApplication()->getServiceManager();
        $this->config  = $sm->get('config');
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->attach('dispatch', array($this, 'loadConfiguration' ),1);
        
        /* $eventManager->attach('dispatch.error', function($event){
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('ApplicationServiceErrorHandling');
                $service->logException($exception);
            }
        });*/
      
        
    } 

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Application\Model\ApplicationTable' => function($sm) {
    
    						$tableGateway = $sm->get('ApplicationTableGateway');
    						$table        = new ApplicationTable($tableGateway);
    						return $table;
    					},
    					'ApplicationTableGateway' => function($sm) {
    						$dbAdapter          = $sm->get('clientdb');
    						//$resultSetPrototype = new ResultSet();
    						//$resultSetPrototype->setArrayObjectPrototype(new Application());
    						return new TableGateway('activity', $dbAdapter, null);
    					},
    			),
    	);
    
    }
    
 	public function loadConfiguration(MvcEvent $e) 
 	{
 		$eventManager = $e->getApplication()->getEventManager();
 		$sm = $e->getApplication()->getServiceManager();
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);    
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
       
        $controller->layout()->modulenamespace = $moduleNamespace;
        
        
        $req = $e->getRequest();
       
       if($req->isXmlHttpRequest()){
        return;
       }
        $userSession = new Container('users');
        

        if($moduleNamespace == 'Application'){
        	if($userSession->offsetExists('id')){
        		
        		$controller->plugin('redirect')->toRoute('dashboard');
        	}
        } else {
            $userSession = new Container ( 'users' );
            
            if($userSession->OffSetExists('clientId')) {
            	$userid = $userSession->clientId;
            	$selectedtheme = $sm->get('Application\Model\ApplicationTable')->fetchtheme($userid);
            	$countData = $sm->get('Application\Model\ApplicationTable')->fetchCounts();
            	$controller->layout()->themeselected = $selectedtheme[0];
        		$controller->layout()->countData = $countData[0];
            }
            
        	if(!$userSession->offsetExists('id') && $moduleNamespace == "Student"){
        		return;
        		//\Zend\Debug\Debug::dump($controller)  ;
        		//die("kill");
        		// $controller->plugin('redirect')->toRoute('student');
        		//   $this->redirect()->toRoute('student');
        	}
        
        	else if(!$userSession->offsetExists('id')){
        		$controller->plugin('redirect')->toRoute('home');
        	}
        }
        }   
}