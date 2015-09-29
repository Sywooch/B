<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);

$controller_name = Yii::$app->controller->getUniqueId();
switch ($controller_name) {
    #提问页面
    case 'question' :
        $body_class = 'qa-ask';
        break;

    #主页
    default:
        $body_class = 'qa-index';
}

?>
<?php $this->beginPage() ?>
    <!DOCTYPE HTML>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1"/>
        <meta name="renderer" content="webkit"/>
        <meta name="viewport"
              content="width=device-width, initial-scale=1, minimal-ui, maximum-scale=1, user-scalable=no"/>
        <meta name="alexaVerifyID" content="LkzCRJ7rPEUwt6fVey2vhxiw1vQ"/>

        <meta name="description" content=""/>
        <meta name="keywords" content=""/>
        <link rel="shortcut icon" href="//static.segmentfault.com/global/img/favicon.30f7204d.ico"/>
        <meta name="msapplication-TileColor" content="#009a61"/>
        <?php $this->head() ?>
    </head>
    <body class="<?= $body_class ?>">
    <?php $this->beginBody() ?>
    <!--[if lt IE 9]>
    <div class="alert alert-danger topframe" role="alert">你的浏览器实在<strong>太太太太太太旧了</strong>，放学别走，升级完浏览器再说 <a
            target="_blank"
            class="alert-link"
            href="http://browsehappy.com">立即升级</a>
    </div>
    <![endif]-->


    <?php
    NavBar::begin(
            [
                //'brandLabel' => Html::img('/img/logo-w.svg'),
                'brandLabel'   => 'TEST',
                'brandUrl'     => false,
                'brandOptions' => ['class' => 'sf'],
                'options'      => [
                        'class' => 'global-nav',
                ],
            ]
    );
    $keyword = '';
    echo '<form action="/search" class="header-search pull-left hidden-sm hidden-xs">
                <button type="submit" class="btn btn-link"><span class="sr-only">搜索</span><span class="glyphicon glyphicon-search"></span></button>
                <input id="searchBox" name="q" type="text" placeholder="输入关键字搜索" class="form-control" value="">
            </form>';

    echo Nav::widget(
            [
                    'options'      => ['class' => 'nav-pills menu list-inline pull-left hidden-xs'],
                    'items'        => [
                            [
                                    'label'   => '首页',
                                    'url'     => ['/site/index'],
                                    'options' => ['class' => 'menu__item']
                            ],
                            [
                                    'label'   => '社区',
                                    'url'     => ['/topic'],
                                    'options' => ['class' => 'menu__item'],
                                    'active'  => true
                            ],
                            [
                                    'label'   => '动弹',
                                    'url'     => ['/tweet'],
                                    'options' => ['class' => 'menu__item'],
                                    'active'  => false
                            ],
                    ],
                    'encodeLabels' => false
            ]
    );
    $notifyCount = 1;
    if (Yii::$app->user->isGuest) {
        $menuItems[] = [
                'label'   => '<span class="sr-only">消息</span><span id="messageCount" class="glyphicon glyphicon-envelope"></span>',
                'url'     => ['/site/signup'],
                'options' => [
                        'class' => 'menu__item'
                ]

        ];
        $menuItems[] = [
                'label'       => '注册 &middot; 登录',
                'url'         => ['/user/security/login'],
                'options'     => [
                        'class' => 'menu__item'
                ],
                'linkOptions' => ['class' => 'SFLogin em ml10']

        ];
    } else {
        // 撰写
        $menuItems[] = [
                'label'   => '撰写',
                'items'   => [
                        [
                                'label' => '提问',
                                'url'   => ['/user/default']
                        ],
                ],
                'options' => [
                        'class' => 'menu__item'
                ]
        ];


        $menuItems[] = [
                'label'   => '<span class="sr-only">消息</span><span id="messageCount" class="glyphicon glyphicon-envelope"></span>',
                'url'     => false,
                'options' => [
                        'class' => 'menu__item'
                ]

        ];


        /* $menuItems[] = [
                 'label'       => Html::tag('i', '', ['class' => 'fa fa-bell']) . Html::tag(
                                 'span',
                                 $notifyCount ? $notifyCount : null
                         ),
                 'url'         => ['/notification/index'],
                 'linkOptions' => ['class' => $notifyCount ? 'new' : null],
                 'options'     => ['class' => 'notification-count'],
         ];*/


        // 个人中心
        $menuItems[] = [
                'label'   => Yii::$app->user->identity->username/* . ' ' . Html::img(
                                Yii::$app->user->identity->getAvatar(
                                        24,
                                        true
                                )
                        )*/,
                /*'linkOptions' => [
                        'class' => 'user-avatar',
                        'style' => 'background-image: url(' .  . ')'
                ],*/
                'items'   => [
                        [
                                'label' => '我的主页',
                                'url'   => ['/user/profile/show', 'id' => Yii::$app->user->identity->id]
                        ],
                        [
                                'label' => '收藏夹',
                                'url'   => ['/user/default']
                        ],
                        [
                                'label' => '帐号设置',
                                'url'   => ['/user/settings/profile']
                        ],
                        '<li class="divider"></li>',
                        [
                                'label'       => '退出',
                                'url'         => ['/user/security/logout'],
                                'linkOptions' => ['data-method' => 'post']
                        ],

                ],
                'options' => [
                        'class' => 'menu__item'
                ]
        ];
    }

    echo Nav::widget(
            [
                    'encodeLabels'    => false,
                    'options'         => ['class' => 'nav-pills opts list-inline pull-right hidden-xs'],
                    'items'           => $menuItems,
                    'activateParents' => true,
            ]
    );
    NavBar::end();
    ?>


    <div class="wrap">
        <div class="container">
            <?= Breadcrumbs::widget(
                    [
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]
            ) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <!--<div class="container text-right mb20 hidden-xs">
        <a href="/feeds" class="feed-link">订阅本页 RSS</a>
    </div>-->

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
                    <dd><a href="http://weekly.segmentfault.com/">每周精选</a></dd>
                    <dd><a href="/app">App 下载</a></dd>
                </dl>
                <dl class="col-sm-2 site-link">
                    <dt>联系合作</dt>
                    <dd><a href="/contact">联系我们</a></dd>
                    <dd><a href="/hiring">加入我们</a></dd>
                    <dd><a href="/link">合作伙伴</a></dd>
                    <dd><a href="/press">媒体报道</a></dd>
                    <dd><a href="http://0x.segmentfault.com">建议反馈</a></dd>
                    <dd><a href="http://pan.baidu.com/share/link?shareid=604288&uk=839272106" target="_blank">Logo
                            下载</a>
                    </dd>
                </dl>
                <dl class="col-sm-2 site-link">
                    <dt>常用链接</dt>
                    <dd><a href="http://mirrors.segmentfault.com/" target="_blank">文档镜像</a></dd>
                    <dd>订阅：<a href="/feeds">问答</a> / <a href="/feeds/blogs">文章</a></dd>
                    <dd><a href="http://segmentfault.com/events?category=4">黑客马拉松</a></dd>
                    <!--             <dd><a href="http://zs.segmentfault.com/" target="_blank">一起涨姿势</a></dd> -->
                    <dd><a href="http://namebeta.com/" target="_blank">域名搜索注册</a></dd>
                </dl>
                <dl class="col-sm-2 site-link">
                    <dt>关注我们</dt>
                    <dd><a href="http://twitter.com/segment_fault" target="_blank">Twitter</a></dd>
                    <!-- <dd><a href="http://page.renren.com/699146294" target="_blank">人人网</a></dd> -->
                    <dd><a href="https://www.linkedin.com/company/segmentfault" target="_blank">LinkedIn</a></dd>
                    <dd><a href="http://weibo.com/segmentfault" target="_blank">新浪微博</a></dd>
                    <dd><a href="http://i.youku.com/segmentfault" target="_blank">优酷主页</a></dd>
                    <dd><a href="/giveaways" target="_blank">开发者福利</a></dd>
                    <dd><a href="/blog/segmentfault" target="_blank">开发日志</a></dd>
                </dl>
                <dl class="col-sm-4 site-link" id="license">
                    <dt>内容许可</dt>
                    <dd>除特别说明外，用户内容均采用 <a rel="license" target="_blank"
                                          href="http://creativecommons.org/licenses/by-sa/3.0/cn/">知识共享署名-相同方式共享 3.0
                            中国大陆许可协议</a> 进行许可
                    </dd>
                    <dd><a key="55e59bd4efbfb0241247d4e4" logo_size="83x30" logo_type="realname"
                           href="http://www.anquan.org">
                            <script src="http://static.anquan.org/static/outer/js/aq_auth.js"></script>
                        </a></dd>
                </dl>
            </div>
            <div class="copyright">
                Copyright &copy; 2011-2015 SegmentFault. <?= Yii::powered() ?><br><a href="http://www.miibeian.gov.cn/"
                                                                                     rel="nofollow">浙ICP备15005796号-2</a>
            </div>
        </div>
    </footer>


    <div id="fixedTools" class="hidden-xs hidden-sm">
        <a id="backtop" class="hidden border-bottom" href="#">回顶部</a>

        <div class="qrcodeWraper">
            <a href="/app#qrcode"><span class="glyphicon glyphicon-qrcode"></span></a>
            <img id="qrcode" class="border" alt="sf-wechat"
                 src="//static.segmentfault.com/page/img/app/appQrcode.92de1876.png">

            <p class="qrcode-text">扫扫下载 App</p>
        </div>
    </div>


    <div class="app-promotion-bar">
        <a href="javascript:;"><i class="fa fa-close close"></i></a>

        <div class="icon"></div>
        <h5 class="title h5">SegmentFault</h5>

        <p class="describe">一起探索更多未知</p>
        <a class="download-btn btn btn-sm btn-primary" href="/app#qrcode">下载 App</a>
    </div>


    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>