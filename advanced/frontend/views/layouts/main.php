<?php

/* @var $this \yii\web\View */
/* @var $content string */

use kartik\icons\Icon;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
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

    echo '<form class="navbar-form navbar-left" role="search" action="/search" method="get">
                <div class="form-group">
                    <input type="text" value="" name="keyword" class="form-control search_input" id="navbar-search" placeholder="搜索..." data-placement="bottom" data-content="请输入要搜索的关键词！">
                </div>
            </form>';

    echo Nav::widget(
            [
                    'options'      => ['class' => 'nav navbar-nav '],
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


    <div class="container">
        <?php
        if (!isset($this->blocks['top-header'])) {
            echo Breadcrumbs::widget(
                    [
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]
            );
        } ?>
        <?= $content ?>
    </div>
</div>

<footer id="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php
Modal::begin(
        [
                'header'       => '<h4 class="modal-title">用户登录</h4>',
                'toggleButton' => ['label' => 'click me'],
        ]
);

?>

<div class="col-xs-12 main bg-white login-modal">
    <div class="login-wrap">
        <form action="/api/user/login" method="POST" role="form" class="mt30">
            <div class="form-group">
                <label class="control-label">Email</label>
                <input type="email"
                       class="form-control"
                       name="mail"
                       required=""
                       placeholder="hello@segmentfault.com"
                       autocomplete="off"
                       style="cursor: auto; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
            </div>
            <div class="form-group">
                <label class="control-label">密码</label>
                <input type="password"
                       class="form-control"
                       name="password"
                       required=""
                       placeholder="密码"
                       autocomplete="off"
                       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=);">
            </div>
            <div class="form-group clearfix">
                <div class="checkbox pull-left">
                    <label><input name="remember" type="checkbox" value="1" checked=""> 记住登录状态</label>
                </div>
                <button type="submit" class="btn btn-primary pull-right pl20 pr20">登录</button>
            </div>
        </form>
        <p class="h4 text-muted visible-xs-block h4">快速登录</p>

        <div class="widget-login mt30">
            <p class="text-muted mt5 mr10 pull-left hidden-xs">快速登录</p>
            <a href="/user/oauth/google"
               class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-google"><span class="icon-sn-google"></span> <strong
                        class="visible-xs-inline">Google 账号</strong></a>
            <a href="/user/oauth/github"
               class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-github"><span class="icon-sn-github"></span> <strong
                        class="visible-xs-inline">Github 账号</strong></a>
            <a href="/user/oauth/weibo"
               class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-weibo"><span class="icon-sn-weibo"></span> <strong
                        class="visible-xs-inline">新浪微博账号</strong></a>
            <a href="/user/oauth/qq"
               class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-qq"><span class="icon-sn-qq"></span>
                <strong class="visible-xs-inline">QQ 账号</strong></a>
            <a href="/user/oauth/weixin"
               class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-wechat"><span class="icon-sn-wechat"></span> <strong
                        class="visible-xs-inline">微信账号</strong></a>
            <button type="button" class="btn mb5 btn-default btn-sm btn-sn-more" id="loginShowMore">...</button>
            <a href="/user/oauth/twitter" class="btn pl5 pr5 mb5 btn-default btn-sn-twitter btn-sm hidden"><span
                        class="icon-sn-twitter"></span> <strong class="visible-xs-inline">Twitter 账号</strong></a>
            <a href="/user/oauth/facebook" class="btn pl5 pr5 mb5 btn-default btn-sn-facebook btn-sm hidden"><span
                        class="icon-sn-facebook"></span> <strong class="visible-xs-inline">Facebook 账号</strong></a>
            <a href="/user/oauth/douban"
               class="btn pl5 pr5 mb5 btn-default btn-sn-douban btn-sm hidden"><span class="icon-sn-douban"></span>
                <strong class="visible-xs-inline">豆瓣账号</strong></a>
        </div>
    </div>
    <div class="login-vline hidden-xs hidden-sm"></div>
</div>
<div class="text-center text-muted mt30">
    <a href="/user/forgot" class="ml5">找回密码</a>
</div>


<?php Modal::end();
?>
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
