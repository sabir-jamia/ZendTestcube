<?php
return array (
		'controllers' => [ 
				'factories' => [ 
						'User\Controller\User' => 'User\Controller\Factory\UserControllerFactory' 
				] 
		],
		
		'router' => array (
				'routes' => array (
						'home' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'controller' => 'User\Controller\User',
												'action' => 'login' 
										) 
								) 
						),
						'user' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/user/[:action][/:id]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]*' 
										),
										'defaults' => array (
												'controller' => 'User\Controller\User',
        				/*'action' => 'login',*/
        			) 
								) 
						) 
				) 
		),
		
		'view_manager' => array (
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				),
				
				'strategies' => array (
						'ViewJsonStrategy' 
				) 
		),
		'module_config' => array (
				'upload_location' => $_SERVER ['DOCUMENT_ROOT'] . '/profilepics' 
		),
		'service_manager' => [
				'abstract_factories' => [
						'UserAbstractFactory' => 'User\Model\Factory\UserAbstractFactory'
				],
		]
);