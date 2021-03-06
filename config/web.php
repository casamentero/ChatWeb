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
		
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
			'enableSession'=>false
        ],
		
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		
		/*
yiiplugs@gmail.com
inder1245678
*/		
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.gmail.com',
				'username' => 'yiiplugs@gmail.com',
				'password' => 'inder12345678',
				'port' => '465',
				'encryption' => 'ssl',
			],			
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
			'enableStrictParsing' => false,
			'showScriptName' => false,
			'rules' => [
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => ['api/user','api/profile','api/language','api/chat','api/authenticate'],
					//'extraPatterns' => ['POST authenticate' => 'authenticate']
				],
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
		
		'response' => [
			'class' => 'yii\web\Response',
			'on beforeSend' => function ($event) {
				$response = $event->sender;
				if ((strpos(Yii::$app->request->url, 'api/') !== false) && ($response->data !== null)) {
						$headers = $response->getHeaders();
						$moredata = false;
						if(isset($headers['x-pagination-page-count']) && isset($headers['x-pagination-current-page'])){
							if(($headers['x-pagination-current-page'])<($headers['x-pagination-page-count'])){
								$moredata = true;
							}
						}
				
					$response->data = [
						'success' 	=> $response->isSuccessful,
						'moredata' 	=> $moredata,
						'data' 		=> $response->data,
					];
					$response->statusCode = 200;
				}
			},
		],
		
		'translate' => [
			'class' => 'richweber\google\translate\Translation',
			'key' => 'AIzaSyD3uWHu3Wc5b4SkZtVbsC9ExJleCS-6gNE',
		],
		
        'amqp' => [
            'class' => 'webtoucher\amqp\components\Amqp',
            'host' => '162.144.209.190',
            'port' => 5672,
            'user' => 'test',
            'password' => 'test',
            'vhost' => '/',
        ],

		'view' => [
			'theme' => [
				'pathMap' => [
					'@dektrium/user/views' => '@app/views/user'
				],
			],
		],
			
		
    ],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'modelMap' => [
				'RegistrationForm' 	=> 'app\models\user\RegistrationForm',
				'Profile' 			=> 'app\models\user\Profile',
			],
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
