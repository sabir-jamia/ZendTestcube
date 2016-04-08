<?php
return array (
		'service_manager' => [ 
				'factories' => [ 
						'RestServiceFactory' => 'Rest\Service\Factory\RestServiceFactory',
						'RestModelFactory' => 'Rest\Model\Factory\RestModelFactory' 
				] 
		] 
);