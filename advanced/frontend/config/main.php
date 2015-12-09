<?php
use common\components\Monitor;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id'                  => 'app-frontend',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'defaultRoute'        => 'default/index',
    'controllerNamespace' => 'frontend\controllers',
    'modules'             => [
        'user' => [
            // following line will restrict access to admin page
            'as frontend' => 'dektrium\user\filters\FrontendFilter',
        ],
    ],
    'components'          => [
        /*'urlManager'   => [
            'class'           => 'yii\web\UrlManager',
            'showScriptName'  => true,
            'enablePrettyUrl' => true,
        ],*/
        'user'         => [
            'class'           => 'common\components\user\User',
            'identityClass'   => 'common\entities\UserEntity',
            'enableAutoLogin' => true,
            'loginUrl'        => ['user/security/login'],
            'identityCookie'  => [
                'name'     => '_frontendIdentity',
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
    ],
    'params'              => $params,
    'on beforeRequest'    => function () {
        Monitor::startMonitor();
    },
    'on beforeAction'     => function ($event) {
        /*$action = $controller = $module = null;
        if (!empty($event->action)) {
            $action = $event->action->id;
            if (!empty($event->action->controller)) {
                $controller = $event->action->controller->id;
                if (!empty($event->action->controller->module)) {
                    $module = $event->action->controller->module->id;
                }
            }
        }

        var_dump($module, $controller, $action);exit('dd');*/

        Monitor::checkMonitorStatus();
    },
];
