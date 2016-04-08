<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Zend\Session\Container;

$userSession = new Container('users');
$clientId = ($userSession->offsetExists('clientId')) ? $userSession->offsetGet('clientId') : null;

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=testcubedb;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'adapters' => array(
            'clientdb' => array(
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=clientdb0' . $clientId . ';host=localhost',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ),
                'username' => 'root',
                'password' => 'tolexo'
            ),
            
            'testcubedb' => array(
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=testcubedb;host=localhost',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ),
                'username' => 'root',
                'password' => 'tolexo'
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
        ),
        'abstract_factories' => array(
            'Zend\Db\Adapter\AdapterAbstractServiceFactory'
        )
    ),
    'applicationSettings' => array(
        'appLink' => 'http://' . $_SERVER['HTTP_HOST'] . '/',
        'appRoot' => $_SERVER['DOCUMENT_ROOT']
    )
);