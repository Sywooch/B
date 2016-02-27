<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id'                  => 'app-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'console\controllers',
    'modules'             => [
        /* 'crawler' => [
             'class' => 'console\modules\crawler\Module',
         ],
         'user'    => null,*/
    ],
    'components'          => [
        'redis'   => null,
        'log'     => [
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'gearman' => [
            'class'   => 'shakura\yii2\gearman\GearmanComponent',
            'servers' => [
                ['host' => '10.5.23.123', 'port' => 4730],
            ],
            'user'    => 'www',
            //任务组
            'jobs'    => [
                'sync1' => [
                    'class' => 'console\jobs\Sync',
                ],
                'sync2' => [
                    'class' => 'console\jobs\Sync',
                ],
            ],
        ],
        /*'db'  => [
            'class'    => 'yii\db\Connection',
            'dsn'      => 'mysql:host=127.0.0.1;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
        ],*/
    ],
    'controllerMap'       => [
        'gearman' => [
            'class'            => 'shakura\yii2\gearman\GearmanController',
            'gearmanComponent' => 'gearman',
        ],
    ],
    'params'              => $params,
    //'i18n'                => null,
];
