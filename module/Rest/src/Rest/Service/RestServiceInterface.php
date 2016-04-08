<?php
namespace Rest\Service;

interface RestServiceInterface
{	
	public function callRestApi($functionName, $data = array());	
}