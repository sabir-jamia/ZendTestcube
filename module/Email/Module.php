<?php
namespace Email;

use Email\Model\EmailTemplate;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
         return array(
            'invokables' => array(
                'EmailService' => 'Email\Service\EmailService',
            ),
         	'factories' => array(
         				//for test join question table
						'Email\Model\EmailTemplate' => function ($sm) {
							$dbAdapter = $sm->get ( 'testcubedb' );
							$table = new EmailTemplate( $dbAdapter );
							return $table;
						},
         		),
        );
    }
}
