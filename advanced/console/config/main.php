<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'crawler' => [
            'class' => 'console\modules\crawler\Module',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'     => [
            'class'    => 'yii\db\Connection',
            'dsn'      => 'mysql:host=127.0.0.1;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
        ],
    ],
    'params' => $params,
];
