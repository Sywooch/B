<?php
return [
        'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
        'modules'    => [
                'user' => [
                        'class'                    => 'dektrium\user\Module',
                        'mailer'                   => [
                                'sender'                => 'no-reply@myhost.com',
                                // or ['no-reply@myhost.com' => 'Sender name']
                                'welcomeSubject'        => 'Welcome subject',
                                'confirmationSubject'   => 'Confirmation subject',
                                'reconfirmationSubject' => 'Email change subject',
                                'recoverySubject'       => 'Recovery subject',

                        ],
                        'enableUnconfirmedLogin'   => true,
                        'confirmWithin'            => 86400,
                        'cost'                     => 12,
                        'enableGeneratingPassword' => false,#�Զ��������룬��ͨ���ʼ�����
                        'enableConfirmation'       => true, #�����ʼ�ȷ��
                        'enableUnconfirmedLogin'   => true, #δ��֤�û��Ƿ���Ե�½
                        'rememberFor'              => 1209600, #cookie��Ч�ڣ�2��
                        'admins'                   => ['admin'],#����Ա�˺�

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
                                'path'     => '/admin',
                        ],

                ],
                'mailer'  => [
                        'class'            => 'yii\swiftmailer\Mailer',
                        'useFileTransport' => false,//���һ���У�false�����ʼ���trueֻ�������ʼ���runtime�ļ����£������ʼ�
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
                                'from'    => ['admin@qq.com' => 'admin']
                        ],
                ],
        ],
];
