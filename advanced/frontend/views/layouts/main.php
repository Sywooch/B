<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\components\user\User;
use common\helpers\TimeHelper;
use common\modules\user\models\LoginForm;

use kartik\widgets\Typeahead;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
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
<!--[if lt IE 9]>
<div class="alert alert-danger topframe" role="alert">
    你的浏览器实在<strong>太太太太太太旧了</strong>，同学别走，升级完浏览器再说
    <a target="_blank" class="alert-link" href="http://browsehappy.com">立即升级</a>
</div>
<![endif]-->
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


        $notification_count = \common\services\UserService::getUserNotificationCount(Yii::$app->user->id);

        $menuItems[] = [
            'label'  => '<span id="messageCount" class="glyphicon glyphicon-envelope"></span>' .
                ($notification_count > 0 ?
                    sprintf(
                        '<span class="has-unread__count">%d</span>',
                        $notification_count
                    ) : ''),
            'url'    => ['/notification/index'],
            'encode' => false,
        ];

        // 个人中心
        $menuItems[] = [
            'label' => Yii::$app->user->identity->username,
            'items' => [
                [
                    'label' => '我的主页',
                    'url'   => ['/user/profile/show', 'username' => Yii::$app->user->identity->username],
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
            'encodeLabels' => false,
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
<?php
//注册提醒，只对未登陆用户显示3次
$cookies = Yii::$app->getResponse()->getCookies();
$cookie_key = 'widget-register';

if (!Yii::$app->request->cookies->has($cookie_key)) {
    $cookies->add(
        new Cookie(
            [
                'name'   => $cookie_key,
                'value'  => 1,
                'expire' => TimeHelper::getAfterTime(1),
            ]
        )
    );
} else {
    $cookies->add(
        new Cookie(
            [
                'name'  => $cookie_key,
                'value' => Yii::$app->request->cookies->getValue($cookie_key) + 1,
            ]
        )
    );
}

?>
<? if (Yii::$app->user->isGuest && !in_array(
        Yii::$app->user->step,
        [User::STEP_REGISTER, User::STEP_LOGIN]
    ) && ($cookies->getValue($cookie_key) <= 3)
) : ?>
    <div class="widget-register mt20 hidden-xs widget-welcome widget-register-slideUp">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="title h4">与我们一起探索更多的未知</h2>

                    <p>专业的开发者技术社区，为用户提供多样化的线上知识交流，丰富的线下活动及给力的工作机会</p>
                </div>

                <div class="col-sm-3">
                    <?= Html::a('加入我们！', ['user/registration/register'], ['class' => 'btn btn-primary btn-block']) ?>
                </div>

                <a class="close" href="javascript:void(0);" title="暂时不想注册!"><span>×</span></a>
            </div>
        </div>
    </div>
<? endif; ?>

<footer id="footer">
    <div class="container">
        <div class="row hidden-xs">
            <dl class="col-sm-2 site-link">
                <dt>网站相关</dt>
                <dd><a href="/about">关于我们</a></dd>
                <dd><a href="/tos">服务条款</a></dd>
                <dd><a href="/faq">帮助中心</a></dd>
                <dd><a href="/repu">声望与权限</a></dd>
                <dd><a href="/markdown">编辑器语法</a></dd>
                <dd><a href="//weekly.segmentfault.com/">每周精选</a></dd>
                <dd><a href="/app">App 下载</a></dd>
            </dl>
            <dl class="col-sm-2 site-link">
                <dt>联系合作</dt>
                <dd><a href="/contact">联系我们</a></dd>
                <dd><a href="/hiring">加入我们</a></dd>
                <dd><a href="/link">合作伙伴</a></dd>
                <dd><a href="/press">媒体报道</a></dd>
                <dd><a href="/0x">建议反馈</a></dd>
                <dd><a href="http://pan.baidu.com/share/link?shareid=604288&amp;uk=839272106" target="_blank">Logo
                                                                                                              下载</a>
                </dd>
            </dl>
            <dl class="col-sm-2 site-link">
                <dt>常用链接</dt>
                <dd>
                    <a href="https://chrome.google.com/webstore/detail/segmentfault-%E7%AC%94%E8%AE%B0/pjklfdmleagfaekibdccmhlhellefcfo"
                       target="_blank">笔记插件: Chrome</a></dd>
                <dd><a href="https://addons.mozilla.org/zh-CN/firefox/addon/sf-note-ext/" target="_blank">笔记插件:
                                                                                                          Firefox</a>
                </dd>
                <dd><a href="//mirrors.segmentfault.com/" target="_blank">文档镜像</a></dd>
                <dd>订阅：<a href="/feeds">问答</a> / <a href="/feeds/blogs">文章</a></dd>
                <dd><a href="/hackathon">黑客马拉松</a></dd>
                <dd><a href="/giveaways" target="_blank">开发者福利</a></dd>
                <!--             <dd><a href="http://zs.segmentfault.com/" target="_blank">一起涨姿势</a></dd> -->
                <dd><a href="https://namebeta.com/" target="_blank">域名搜索注册</a></dd>
            </dl>
            <dl class="col-sm-2 site-link">
                <dt>关注我们</dt>
                <dd><a href="https://github.com/SegmentFault" target="_blank">GitHub</a></dd>
                <dd><a href="https://twitter.com/segment_fault" target="_blank">Twitter</a></dd>
                <!-- <dd><a href="http://page.renren.com/699146294" target="_blank">人人网</a></dd> -->
                <dd><a href="https://www.linkedin.com/company/segmentfault" target="_blank">LinkedIn</a></dd>
                <dd><a href="http://weibo.com/segmentfault" target="_blank">新浪微博</a></dd>
                <dd><a href="http://i.youku.com/segmentfault" target="_blank">优酷主页</a></dd>
                <dd><a href="/blog/segmentfault" target="_blank">开发日志</a></dd>
            </dl>
            <dl class="col-sm-4 site-link" id="license">
                <dt>内容许可</dt>
                <dd>除特别说明外，用户内容均采用 <a rel="license"
                                      target="_blank"
                                      href="http://creativecommons.org/licenses/by-sa/3.0/cn/">知识共享署名-相同方式共享 3.0
                                                                                               中国大陆许可协议</a> 进行许可
                </dd>
                <dd>本站由 <a target="_blank" href="http://qingcloud.com/">青云 QingCloud</a> 提供云计算服务<br><a target="_blank"
                                                                                                       href="https://www.upyun.com/?utm_source=segmentfault&amp;utm_medium=link&amp;utm_campaign=upyun&amp;md=segmentfault">又拍云</a>
                    提供 CDN 存储服务
                </dd>
            </dl>
        </div>

    </div>

    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right">自豪地采用
            <a href="http://www.yiiframework.com/" rel="external" target="_blank">Yii Framework 2</a>
        </p>
    </div>
</footer>

<?php if (Yii::$app->user->isGuest): ?>
    <?php
    Modal::begin(
        [
            'header'       => '<h4 class="modal-title">用户登录</h4>',
            'toggleButton' => false,
            'size'         => Modal::SIZE_DEFAULT,
            'options'      => ['id' => 'quick-login-modal',],
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
                [
                    'inputOptions' => [
                        'autofocus' => 'autofocus',
                        'class'     => 'form-control',
                        'tabindex'  => '1',
                    ],
                ]
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
                login:<?=(var_export(!Yii::$app->user->isGuest, true)) ?>,
                avatar: '<?=(Yii::$app->user->isGuest || empty(Yii::$app->user->identity->avatar))?
                'https://sf-static.b0.upaiyun.com/v-56810165/global/img/user-64.png': Yii::$app->user->identity->avatar ?>'
            }
        };

        window.app = app;
    })(window);
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
