<?php
namespace Rest\Model;

use Zend\Json\Json;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Request;
use Zend\Http\Client;

class Rest
{
	public function callUserModule($functionName, $data)
	{
		unset($data['module']);
		switch ($functionName) {
			case "authenticateUser" :
				$method = "POST";
				$data = Json::encode($data);
				$this->callCurl($method, $data);
				break;
			default:
				echo "default";
		}
	}
	
	public function callCategoryModule()
	{
		
	}
	
	public function callCurl($method, $data) 
	{
		$request = new Request();
		$request->getHeaders()->addHeaders(array(
				'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
		));
		$request->setUri("http://localhost:8090/api/v1/users?username=sabirjamia1");
		$request->setMethod("GET");
		$client = new Client();
		$response = $client->dispatch($request);
		print_r(json_decode($response->getBody()));die;	
	}
}