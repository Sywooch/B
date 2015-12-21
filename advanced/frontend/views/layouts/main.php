<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\modules\user\models\LoginForm;
use kartik\icons\Icon;
use kartik\widgets\Typeahead;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Block;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);

//Icon::map($this);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php if (isset($this->blocks['meta-header'])) {
        echo $this->blocks['meta-header'];
    } ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= Alert::widget(
        [
                'options' => [
                        'style' => 'margin-top:-10px',
                ],
        ]
); ?>
<div class="wrap">

    <?php
    NavBar::begin(
            [

                    'brandLabel' => Yii::$app->name,
                    'brandUrl'   => Yii::$app->homeUrl,
                    'options'    => [
                            'id'    => 'global-nav',
                            'class' => 'navbar navbar-default navbar-fixed-top',
                    ],
            ]
    );


    $template = '<div class="search-hint">' . '<p class="search-hint-subject"><a href="{{url}}">{{subject}}</a></p>' . '<p class="search-hint-tags">{{tags}}</p>' . '</div>';

    $typeahead = Typeahead::widget(
            [
                    'name'          => 'keyword',
                    'options'       => [
                            'id'          => 'searchBox',
                            'class'       => 'search_input',
                            'placeholder' => '请输入要搜索的关键字...',
                    ],
                    'pluginOptions' => ['highlight' => true],
                    'dataset'       => [
                            [
                                    'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                    'display'        => 'value',
                                //'prefetch'       => Url::to(['test/data']),
                                    'remote'         => [
                                            'url'      => Url::to(['search/quick-search']) . '&keyword=%QUERY',
                                            'wildcard' => '%QUERY',
                                    ],
                                    'display'        => 'subject',
                                    'templates'      => [
                                            'notFound'   => '',
                                            'suggestion' => new JsExpression("Handlebars.compile('{$template}')"),
                                    ],
                            ],
                    ],

            ]
    );
    //<input type="text" value="" name="keyword" class="form-control search_input" id="navbar-search" placeholder="搜索..." data-placement="bottom" data-content="请输入要搜索的关键词！">
    echo sprintf(
            '<form id="quick-search" class="navbar-form navbar-left" action="%s" method="get">%s</form>',
            urldecode(Url::to(['search/query'])),
            $typeahead
    );

    echo Nav::widget(
            [
                    'options'      => ['class' => 'global-nav nav navbar-nav'],
                    'items'        => [
                        //        ['label' =>  Icon::show('th-large')  . '首页', 'url' => ['/site/index'] ],
                            ['label' => '社区', 'url' => ['/topic'], 'active' => true],

                    ],
                    'encodeLabels' => false,
            ]
    );


    $menuItems = [
            ['label' => 'Home', 'url' => ['/site/index']],
    ];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = [
                'label' => '消息',
                'url'   => ['/site/signup'],

        ];
        $menuItems[] = [
                'label' => '注册',
                'url'   => ['/user/registration/register'],

        ];
        $menuItems[] = [
                'label' => ' 登录',
                'url'   => ['/user/security/login'],

        ];
    } else {
        // 撰写
        $menuItems[] = [
                'label' => '撰写',
                'items' => [
                        [
                                'label' => '提问',
                                'url'   => ['/question/create'],
                        ],
                ],
        ];


        /*$menuItems[] = [
            'label'   => '<span class="sr-only">消息</span><span id="messageCount" class="glyphicon glyphicon-envelope"></span>',
            'url'     => false,
        ];*/

        // 个人中心
        $menuItems[] = [
                'label' => Yii::$app->user->identity->username,
                'items' => [
                        [
                                'label' => '我的主页',
                                'url'   => ['/user/profile/show', 'id' => Yii::$app->user->identity->id],
                        ],
                        [
                                'label' => '收藏夹',
                                'url'   => ['/user/default'],
                        ],
                        [
                                'label' => '帐号设置',
                                'url'   => ['/user/settings/profile'],
                        ],
                        '<li class="divider"></li>',
                        [
                                'label'       => '退出',
                                'url'         => ['/user/security/logout'],
                                'linkOptions' => ['data-method' => 'post'],
                        ],
                ],
        ];
    }
    /*
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/user/registration/register']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/user/security/login']];
    } else {
        $menuItems[] = [
            'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }*/


    echo Nav::widget(
            [
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items'   => $menuItems,
            ]
    );
    NavBar::end();
    ?>

    <?php if (isset($this->blocks['top-header'])) {
        echo $this->blocks['top-header'];
    } ?>


    <?php
    if (!isset($this->blocks['top-header'])) {
        echo '<div class="container">';
        echo Breadcrumbs::widget(
                [
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]
        );
        echo '</div>';
    } ?>
    <?= $content ?>
</div>

<footer id="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php if (Yii::$app->user->isGuest): ?>
    <?php
    Modal::begin(
            [
                    'header'       => '<h4 class="modal-title">用户登录</h4>',
                    'toggleButton' => false,
                    'size'         => Modal::SIZE_DEFAULT,
                    'options'      => [
                            'id' => 'quick-login-modal',
                    ],
            ]
    );

    ?>

    <div class="main bg-white login-modal">
        <div class="login-wrap">
            <?php $model = Yii::createObject(LoginForm::className()); ?>
            <?php $form = ActiveForm::begin(
                    [
                            'action'                 => ['user/security/login'],
                            'id'                     => 'quick-login-form',
                            'enableAjaxValidation'   => true,
                            'enableClientValidation' => false,
                            'validateOnBlur'         => false,
                            'validateOnType'         => false,
                            'validateOnChange'       => false,
                    ]
            ) ?>

            <?= $form->field(
                    $model,
                    'login',
                    ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]
            ) ?>

            <?= $form->field(
                    $model,
                    'password',
                    ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']]
            )->passwordInput()->label(
                    Yii::t('user', 'Password') . ' (' . Html::a(
                            Yii::t('user', 'Forgot password?'),
                            ['/user/recovery/request'],
                            ['tabindex' => '5']
                    ) . ')'
            ) ?>
            <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']) ?>
            <?= Html::submitButton(
                    Yii::t('user', 'Sign in'),
                    ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']
            ) ?>



            <?php ActiveForm::end(); ?>

            <p class="h4 text-muted visible-xs-block h4">快速登录</p>

            <div class="widget-login mt30">
                <p class="text-muted mt5 mr10 pull-left hidden-xs">快速登录</p>

                <a href="/user/oauth/qq"
                   class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-qq"><span class="icon-sn-qq"></span>
                    <strong class="visible-xs-inline">QQ 账号</strong></a>
                <a href="/user/oauth/weixin"
                   class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-wechat"><span class="icon-sn-wechat"></span> <strong
                            class="visible-xs-inline">微信账号</strong></a>

            </div>
        </div>
        <div class="text-center text-muted mt30">
            <?= \common\helpers\TemplateHelper::showRegisterBtn() ?>
        </div>
    </div>
    <?php Modal::end(); ?>
<?php endif; ?>

<script>
    (function () {
        var app = {
            user: {
                login:<?=(var_export(!Yii::$app->user->isGuest, true)) ?>
            }
        };

        window.app = app;
    })(window);
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
