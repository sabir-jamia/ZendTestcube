<?php
namespace RestClient\Http\Client;

use Zend\Http\Client as ZendHttpClient;
use Zend\Json\Json;
use Zend\ServiceManager\ServiceLocatorInterface;

final class Client implements ClientInterface
{
    private $zendClient;
    
    private $sm;
    
    public function __construct(ZendHttpClient $client = null, ServiceLocatorInterface $sm) 
    {
        $client = ($client instanceof ZendHttpClient) ? $client : new ZendHttpClient();
        $this->zendClient = $client;
        $this->sm = $sm;
    }
    
    public function setZendClient(ZendHttpClient $client)
    {
        $this->zendClient = $client;
        return $this;
    }
    
    public function getZendClient()
    {
        return $this->zendClient;
    }
    
    public function doRequest($path)
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8');
        $this->zendClient->setUri($path);
        $this->zendClient->getRequest()->getHeaders()->addHeaders($headers);
        $zendHttpResponse = $this->zendClient->send();
        $content = $zendHttpResponse->getContent();
        return Json::decode($content);
    }
    
    public function get(array $data = array(), $path) 
    {
        $this->zendClient->setMethod('GET')
                         ->setParameterGet($data);
        return $this->doRequest($path);
    }
    
    public function post(array $data = array())
    {
        $this->zendClient->setMethod('POST')
                         ->setRawBody(Json::encode($data));
        return $this->doRequest();
    }
    
    public function put(array $data = array())
    {
        $this->zendClient->setMethod('PUT')
                         ->setRawBody(Json::encode($data));
        return $this->doRequest();
    }
    
    public function patch(array $data = array())
    {
        $this->zendClient->setMethod('PATCH')
                         ->setRawBody(Json::encode($data));
        return $this->doRequest();
    }
    
    public function delete(array $data = array())
    {
        $this->zendClient->setMethod('DELETE');
        return $this->doRequest();
    }
}