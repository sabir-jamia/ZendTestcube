<?php
return [
    'service_manager' => [
        'factories' => [
            'RestClient' => 'RestClient\Service\Factory\RestClientServiceFactory',
            'RestModel' => 'RestClient\Model\Factory\RestModelFactory',
            'Client' => 'RestClient\Http\Client\Factory\ClientFactory',
        ],
        'aliases' => [
            //'Rest' => 'RestClient\Model\Rest',
        ]
	],
    'TestCubeApi' => [
        'uri' => 'http://localhost:8090/',
        'options' => [
            'timeout'       => 60,
            'sslverifypeer' => false,
            'keepalive'     => true,
            'adapter'       => 'Zend\Http\Client\Adapter\Socket',
        ],
        'headers' => [
            'Accept'       => 'application/hal+json',
            'Content-Type' => 'application/json',
        ],
    ]
];