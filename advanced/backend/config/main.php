<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id'                  => 'app-backend',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap'           => ['log'],
    'modules'             => [
        //'user' => [
            // following line will restrict access to admin page
            //'as backend' => 'dektrium\user\filters\BackendFilter',
        //],
    ],
    'components'          => [
        'user'         => [
            'class'           => 'common\components\user\User',
            'identityClass'   => 'common\entities\UserEntity',
            'enableAutoLogin' => true,
            'loginUrl'        => ['/user/security/login'],
            'identityCookie'  => [
                'name'     => '_backendIdentity',
                'path'     => '/',
                'httpOnly' => true,
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
       /*'view'         => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app',
                    //'@app/views' => '@vendor/prawee/yii2-adminlte-theme/views',
                ],
            ],
        ],*/
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-green-light',
                ],
            ],
        ],
    ],
    'params'              => $params,
];
