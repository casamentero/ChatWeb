<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'tqWf4X82P5J_-B6Ihnv966Eom_r-ayuJ',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		/*
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
		*/
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        
		'urlManager' => [
			'enablePrettyUrl' => true,
			'enableStrictParsing' => true,
			'showScriptName' => false,
			'rules' => [
				['class' => 'yii\rest\UrlRule', 'controller' => ['api/user','api/profile','api/language','api/chat']],
			],
		],
        
		'i18n' => [
			'translations' => [
				'app'=>array(
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => "@app/messages",
					'sourceLanguage' => 'en_US',
					'fileMap' => array(
						'app'=>'app.php'                    
					)
				),
				'user'=>array(
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => "@dektrium/user/messages",
					'sourceLanguage' => 'en_US',
					'fileMap' => array(
						'user'=>'user.php'    
					)
				)			
			],
		],
		
		/*
		'response' => [
			'class' => 'yii\web\Response',
			'on beforeSend' => function ($event) {
				$response = $event->sender;
				if ($response->data !== null) {
					$response->data = [
						'success' => $response->isSuccessful,
						'data' => $response->data,
					];
					$response->statusCode = 200;
				}
			},
		],
		*/
		
    'translate' => [
        'class' => 'richweber\google\translate\Translation',
        'key' => 'AIzaSyD3uWHu3Wc5b4SkZtVbsC9ExJleCS-6gNE',
    ],
			
		
    ],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
		],	
		'api' => [
			'class' => 'app\modules\api\Module',
		],
	],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
