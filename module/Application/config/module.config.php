<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(    
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        )
    ),
    
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            )
        )
    ),
    
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'Application' => __DIR__ . '/../public'
            )
        )
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        )
    ),
    
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/head' => __DIR__ . '/../view/layout/head.phtml',
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/header-first' => __DIR__ . '/../view/layout/header-first.phtml',
            'layout/header-second' => __DIR__ . '/../view/layout/header-second.phtml',
            'layout/header-third' => __DIR__ . '/../view/layout/header-third.phtml',
            'layout/dash-menu' => __DIR__ . '/../view/layout/dash-menu.phtml',
            'layout/footer' => __DIR__ . '/../view/layout/footer.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array()
        )
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'HeadPlugin' => 'Application\Controller\Plugin\HeadPlugin'
        )
    )
);