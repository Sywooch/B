<?php
use common\config\RedisKey;

return [
    'name'          => 'WebSite',
    'language'      => 'zh-cn',
    'vendorPath'    => dirname(dirname(__DIR__)) . '/vendor',
    'modules'       => [
        #yii2 user 模块设置
        'user'    => [
            'class'                    => 'common\modules\user\Module',
            'modelMap'                 => [
                'User'             => 'common\entities\UserEntity',
                'Profile'          => 'common\entities\UserProfileEntity',
                'RegistrationForm' => 'common\modules\user\models\RegistrationForm',

            ],
            'controllerMap'            => [
                'admin'        => 'common\modules\user\controllers\AdminController',
                'settings'     => 'common\modules\user\controllers\SettingsController',
                'registration' => 'common\modules\user\controllers\RegistrationController',
                'default'      => 'common\modules\user\controllers\DefaultController',
            ],
            'mailer'                   => [
                #发件人
                'sender'                => ['admin@bo-u.cn' => '系统邮件'],
                // or ['no-reply@myhost.com' => 'Sender name']
                'welcomeSubject'        => '欢迎注册',
                'confirmationSubject'   => '账号激活邮件',
                'reconfirmationSubject' => '更改邮件地址',
                'recoverySubject'       => '更改密码',

            ],
            //不允许显示信息
            'enableFlashMessages'      => false,
            'enableUnconfirmedLogin'   => true,
            'confirmWithin'            => 86400,
            'cost'                     => 12,
            #自动生成密码，并通过邮件发送
            'enableGeneratingPassword' => false,
            #开启邮件确认
            'enableConfirmation'       => true,
            #未认证用户是否可以登陆
            'enableUnconfirmedLogin'   => true,
            #cookie有效期，2周
            'rememberFor'              => 1209600,
            #管理员账号
            'admins'                   => ['admin'],


        ],
        'rbac'    => [
            'class' => 'dektrium\rbac\Module',
        ],
        #站点设置
        'setting' => [
            'class'               => 'funson86\setting\Module',
            'controllerNamespace' => 'funson86\setting\controllers',
        ],
        ########## 以下为项目模块　##########

    ],
    'components'    => [
        'formatter'   => [ //for the showing of date datetime
            'class'             => 'common\helpers\FormatterHelper',
            'dateFormat'        => 'yyyy-MM-dd',
            'locale'            => 'zh-CN',
            'datetimeFormat'    => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator'  => ',',
            'thousandSeparator' => ' ',
            'currencyCode'      => 'CNY',
        ],
        'cache'       => [
            'class' => 'yii\caching\FileCache',
        ],
        'session'     => [
            'class'        => 'common\components\redis\Session',
            'keyPrefix'    => RedisKey::REDIS_KEY_SESSION, #需要在redis.php文件中配置

            //'class'        => 'yii\web\Session',
            //'timeout'      => 7200,
            'name'         => 'PHPSESSID',
            'cookieParams' => [
                'domain'   => '.yii2.com', #todo 记得修改
                'httponly' => true,
                'path'     => '/',
            ],

        ],
        #日志设置
        'log'         => [
            'traceLevel' => YII_DEBUG ? 0 : 3,
            'targets'    => [
                'file'        => [
                    'class'       => 'yii\log\FileTarget',
                    'levels'      => [
                        'warning',
                        'error',
                    ],
                    'maxLogFiles' => 20,
                    'logVars'     => [],
                ],
                /*'email'       => [
                    'class'   => 'common\components\EmailTarget',
                    'levels'  => ['error'],
                    'mailer'  => 'mailer',
                    'message' => [
                        'from'    => ['admin@bo-u.cn'],
                        'to'      => ['6202551@qq.com'],
                        'subject' => 'Log message',
                    ],
                ],*/
                'trace'       => [
                    'class'       => 'yii\log\FileTarget',
                    'levels'      => ['trace'],
                    'maxLogFiles' => 20,
                    'categories'  => [
                        'service',
                        'behavior',
                        'event',
                        'notification',
                        'attachment',
                        'notifier',
                        'counter',
                        'updater',
                        'redis',
                        'log',
                        'rbac',
                        'user_event',
                    ],
                    'logFile'     => '@app/runtime/logs/trace_' . date('Y-m-d') . '.log',
                    'maxFileSize' => 1024 * 2,
                    'logVars'     => [],
                ],
                'error'       => [
                    'class'       => 'yii\log\FileTarget',
                    'levels'      => ['error'],
                    'maxLogFiles' => 20,
                    'logFile'     => '@app/runtime/logs/error_' . date('Y-m-d') . '.log',
                    'maxFileSize' => 1024 * 2,
                    'logVars'     => [],
                ],
                'performance' => [
                    'class'       => 'yii\log\FileTarget',
                    'levels'      => ['trace'],
                    'maxLogFiles' => 20,
                    'categories'  => ['Performance'],
                    'logFile'     => '@app/runtime/logs/performance_' . date('Y-m-d') . '.log',
                    'maxFileSize' => 1024 * 2,
                    'logVars'     => [],
                ],
            ],
        ],
        #邮件发送配置
        'mailer'      => [
            'class'            => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false, #true 测试用，在runtime/mail文件夹下生成
            'transport'        => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'smtp.163.com',
                'username'   => 'misper@163.com',
                'password'   => 'misper77016060;',
                'port'       => 465,
                'encryption' => 'ssl',
            ],
            'messageConfig'    => [
                'charset' => 'UTF-8',
                'from'    => ['admin@bo-u.cn' => 'admin'],
            ],
        ],
        #redis定义
        'redis'       => [
            'class'  => 'common\components\redis\Connection',
            'prefix' => 'YIIREDIS',
            'config' => RedisKey::buildConfig(),
        ],
        #授权管理
        'authManager' => [
            'class'        => 'common\components\AuthManager',
            'defaultRoles' => ['guest'],
        ],
        #站点设置
        'setting'     => [
            'class' => 'common\components\setting\Setting',
        ],
        #自定义模板目录
        'view'        => [
            'theme' => [
                'pathMap' => [
                    #用户模板
                    '@dektrium/user/views' => '@common/modules/user/views',
                ],
            ],
        ],
        #第三方接入
        /*'authClientCollection' => [
            'class'   => 'yii\authclient\Collection',
            'clients' => [
                'qq' => [
                    'class'        => '@common\components\authclients\QqOAuth',
                    'clientId'     => 'your_qq_clientid',
                    'clientSecret' => 'your_qq_secret',
                ],
            ],
        ],*/
        #xunsearch
        'xunsearch'   => [
            'class'        => 'hightman\xunsearch\Connection',
            'iniDirectory' => '@common/config/xunsearch',    // 搜索 ini 文件目录，默认：@vendor/hightman/xunsearch/app
            'charset'      => 'utf-8',
        ],
        'cws'         => [
            'class' => 'common\components\cws\CWS',
        ],
    ],
    'controllerMap' => [
        'ueditor' => [
            'class'     => 'crazydb\ueditor\UEditorController',
            'thumbnail' => false,//如果将'thumbnail'设置为空，将不生成缩略图。
            'watermark' => [    //默认不生存水印
                'path'  => '', //水印图片路径
                'start' => [0, 0] //水印图片位置
            ],
            'zoom'      => ['height' => 500, 'width' => 500], //缩放，默认不缩放
            'config'    => [
                //server config @see http://fex-team.github.io/ueditor/#server-config
                'imagePathFormat'      => '/uploads/tmp_attachments/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                'scrawlPathFormat'     => '/uploads/tmp_attachments/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                'snapscreenPathFormat' => '/uploads/tmp_attachments/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                'catcherPathFormat'    => '/uploads/tmp_attachments/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                'videoPathFormat'      => '/uploads/tmp_attachments/video/{yyyy}{mm}{dd}/{time}{rand:6}',
                'filePathFormat'       => '/uploads/tmp_attachments/file/{yyyy}{mm}{dd}/{rand:4}_{filename}',
                'imageManagerListPath' => '/uploads/tmp_attachments/image/',
                'fileManagerListPath'  => '/uploads/tmp_attachments/file/',
            ],
        ],
    ],
    #i18n
    /*'i18n' => [
        'translations' => [
            'user*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@common/modules/user/messages', // my custom message path.
                'sourceLanguage' => 'zh-CN',
                'fileMap' => [
                    'user' => 'user.php', // I put this file on folder common/messages/ms/user.php so yours zh-CN
                ],
            ]
        ],
    ],*/

];
