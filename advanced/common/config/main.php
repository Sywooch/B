<?php
return [
        'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
        'modules'    => [
                'user' => [
                        'class'                    => 'dektrium\user\Module',
                        'mailer'                   => [
                                'sender'                => '6202551@qq.com',
                                // or ['no-reply@myhost.com' => 'Sender name']
                                'welcomeSubject'        => '欢迎注册',
                                'confirmationSubject'   => '账号激活邮件',
                                'reconfirmationSubject' => '更改邮件地址',
                                'recoverySubject'       => '更改密码',

                        ],
                        'enableFlashMessages'      => false,
                        'enableUnconfirmedLogin'   => true,
                        'confirmWithin'            => 86400,
                        'cost'                     => 12,
                        'enableGeneratingPassword' => false,#自动生成密码，并通过邮件发送
                        'enableConfirmation'       => true, #开启邮件确认
                        'enableUnconfirmedLogin'   => true, #未认证用户是否可以登陆
                        'rememberFor'              => 1209600, #cookie有效期，2周
                        'admins'                   => ['admin'],#管理员账号

                ],
                'rbac' => [
                        'class' => 'dektrium\rbac\Module',
                ],
        ],
        'components' => [
                'cache'   => [
                        'class' => 'yii\caching\FileCache',
                ],
                'session' => [
                        'class'        => 'yii\web\Session',
                        'timeout'      => 7200,
                        'name'         => 'PHPSESSID',
                        'cookieParams' => [
                                'domain'   => '.yii2.com',
                                'httponly' => true,
                                'path'     => '/',
                        ],

                ],
                'mailer'  => [
                        'class'            => 'yii\swiftmailer\Mailer',
                        'useFileTransport' => false,//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
                        'transport'        => [
                                'class'      => 'Swift_SmtpTransport',
                                'host'       => 'smtp.163.com',
                                'username'   => '15618380091@163.com',
                                'password'   => '*******',
                                'port'       => '25',
                                'encryption' => 'tls',

                        ],
                        'messageConfig'    => [
                                'charset' => 'UTF-8',
                                //'from'    => ['admin@qq.com' => 'admin']
                        ],
                ],
        ],
];
