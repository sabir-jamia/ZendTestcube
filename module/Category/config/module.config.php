<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Category\Controller\Category' => 'Category\Controller\CategoryController'
        )      
    ),
    'router' => array(
        'routes' => array(
            'category' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/category[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Controller\Category',
                        'action' => 'index'
                    )
                )
            )
        )
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'CategoryFactory' => 'Category\Model\Factory\CategoryFactory',
        ),
    ),
);