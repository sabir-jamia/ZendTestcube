<?php
namespace RestClient\Model;

use Zend\ServiceManager\ServiceLocatorInterface;

class Rest
{
    protected $sm;
    
    protected $url;
    
    public function __construct(ServiceLocatorInterface $sm, $path) 
    {
        $this->sm = $sm;
        $this->path = $path;
    }
    
	public function callUserModule($functionName, $data)
	{
		unset($data['module']);
		$zendClient = $this->sm->get('Client');
		switch ($functionName) {
			case "authenticateUser" :
				$queryParams = "";
				
				if($data['userType'] == 'username') {
				    $queryParams = "?username=".$data['username'];
				} else {
				    $queryParams = "?username=".$data['email'];
				}
				return $zendClient->get(array(), $this->path."api/v1/users".$queryParams);
				break;
			default:
				echo "default";
		}
	}
	
	public function callCategoryModule()
	{
		
	}
}