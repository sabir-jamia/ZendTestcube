<?php
namespace RestClient\Service;

interface RestClientServiceInterface
{	
	public function callRestApi($functionName, $data = array());	
}