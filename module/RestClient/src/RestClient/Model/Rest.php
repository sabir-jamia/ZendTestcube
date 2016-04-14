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
		$zendClient = $this->sm->get('Client');
		switch ($functionName) {
			case "getUser" :
				$queryParams = "?".$data['userType']."=".$data['username'];
				return $zendClient->get(array(), $this->path."api/v1/users".$queryParams);
				break;
			case "register" :
			    return $zendClient->post($data, $this->path."api/v1/users");
			    break;
			default:
				echo "default";
		}
	}
	
	public function callCategoryModule()
	{
		
	}
}