<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);
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
        <meta property="qc:admins" content="15317273575564615446375"/>
        <meta name="viewport"
              content="width=device-width, initial-scale=1, minimal-ui, maximum-scale=1, user-scalable=no"/>
        <meta name="alexaVerifyID" content="LkzCRJ7rPEUwt6fVey2vhxiw1vQ"/>

        <meta name="description" content="最前沿的技术问答，最纯粹的技术切磋。让你不知不觉中开拓眼界，提高技能。在巨人的肩膀上，成为一名优秀开发者。"/>
        <meta name="keywords" content="SegmentFault 程序员 问答"/>
        <link rel="shortcut icon" href="//static.segmentfault.com/global/img/favicon.30f7204d.ico"/>
        <meta name="msapplication-TileColor" content="#009a61"/>

        <meta name="userId" value="" id="SFUserId"/>
        <meta name="userRank" value="" id="SFUserRank"/>

        <!--[if lt IE 9]>
        <link rel="stylesheet" href="http://static.segmentfault.com/global/css/ie.css?"/>
        <script src="http://static.segmentfault.com/global/js/html5shiv.js?"></script>
        <script src="http://static.segmentfault.com/global/js/respond.js?"></script>
        <![endif]-->
        <?php $this->head() ?>
    </head>
    <body class="qa-index">
    <?php $this->beginBody() ?>
    <!--[if lt IE 9]>
    <div class="alert alert-danger topframe" role="alert">你的浏览器实在<strong>太太太太太太旧了</strong>，放学别走，升级完浏览器再说 <a
        target="_blank"
        class="alert-link"
        href="http://browsehappy.com">立即升级</a>
    </div>
    <![endif]-->

    <div class="global-nav">
        <nav class="container nav">
            <div class="dropdown m-menu">
                <a href="javascript:void(0);" id="dLabel" class="visible-xs-block m-toptools" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <span class="glyphicon glyphicon-align-justify"></span>
                    <span class="mobile-menu__unreadpoint"></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <li class="mobile-menu__item"><a href="/questions/newest">问答</a></li>
                    <li class="mobile-menu__item"><a href="/blogs">文章</a></li>
                    <li class="mobile-menu__item"><a href="/events">活动</a></li>
                    <li class="mobile-menu__item"><a href="/notes">笔记</a></li>
                    <li class="mobile-menu__item"><a href="/jobs">职位</a></li>
                    <li class="mobile-menu__item"><a href="/tags">标签</a></li>
                    <li class="mobile-menu__item"><a href="/users">榜单</a></li>
                    <li class="mobile-menu__item"><a href="/sites">子站</a></li>
                </ul>
            </div>

            <h1 class="logo"><a class="sf" href="/">SegmentFault</a></h1>

            <a href="/user/login" class="visible-xs-block pull-right m-ask m-toptools"><span
                    class="glyphicon glyphicon-log-in"></span></a>

            <form action="/search" class="header-search pull-left hidden-sm hidden-xs">
                <button type="submit" class="btn btn-link"><span class="sr-only">搜索</span><span
                        class="glyphicon glyphicon-search"></span></button>
                <input id="searchBox" name="q" type="text" placeholder="输入关键字搜索" class="form-control" value="">
            </form>

            <ul class="menu list-inline pull-left hidden-xs">
                <li class="menu__item"><a href="/questions/newest">问答</a></li>
                <li class="menu__item"><a href="/blogs">文章</a></li>
                <li class="menu__item"><a href="/events">活动</a></li>
                <li class="menu__item"><a href="/users">榜单</a></li>
                <li class="menu__item dropdown hoverDropdown">
                    <a data-toggle="dropdown" href="/sites" class="more dropdownBtn">
                        &middot;&middot;&middot;<span class="sr-only">更多</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/notes">笔记</a></li>
                        <li><a href="/jobs">职位</a></li>
                        <li><a href="/tags">标签</a></li>
                        <li><a href="/sites">子站</a></li>
                        <li><a href="/app">移动 App</a></li>
                    </ul>
                </li>
            </ul>

            <?php if (Yii::$app->user->isGuest): ?>
                <ul class="opts pull-right list-inline hidden-xs">
                    <!-- <li class="opts__item">赶快加入吧 <a id="sfLogin" href="/user/login" class="SFLogin em ml10" onClick="_gaq.push(['_trackEvent', 'Button', 'Click', 'Login']);">立即登录</a></li> -->
                    <li class="opts__item message has-unread">
                        <a target="_blank" href="/tour">
                            <span class="sr-only">消息</span>
                            <span id="messageCount" class="glyphicon glyphicon-envelope"></span>
                        </a>
                    </li>
                    <li class="opts__item">
                        <a href="/user/login" class="SFLogin em ml10"
                           onClick="_gaq.push(['_trackEvent', 'Button', 'Click', 'Login']);">注册 &middot; 登录</a>
                    </li>
                </ul>
            <?php else : ?>


                <ul class="opts pull-right list-inline hidden-xs">
                    <li class="opts__item dropdown hoverDropdown write-btns">
                        <a class="dropdownBtn" data-toggle="dropdown" href="/ask">撰写<span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="/ask">提问题</a></li>
                            <li><a href="/write">写文章</a></li>

                            <li><a href="/record">记笔记</a></li>
                        </ul>
                    </li>
                    <li class="opts__item message has-unread">
                        <a href="/user/notifications">
                            <span class="sr-only">消息</span>
                            <span id="messageCount" class="glyphicon glyphicon-envelope"></span>
                        </a>
                    </li>
                    <li class="opts__item user dropdown hoverDropdown">
                        <a class="dropdownBtn user-avatar" data-toggle="dropdown"
                           style="background-image: url('<?= Yii::$app->user->identity->getAvatar(24, true) ?>')"
                           href="javascript:;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="<?= Url::to(['/user/profile/show', 'id' => Yii::$app->user->identity->id]) ?>">我的主页</a>
                            </li>
                            <li><a href="<?= Url::to(['/user/settings/profile']) ?>">收藏夹</a></li>
                            <li><a href="<?= Url::to(['/user/settings/profile']) ?>">帐号设置</a></li>
                            <li class="divider"></li>
                            <li><a href="<?= Url::to(['/user/security/logout']) ?>" data-method="post">退出</a></li>
                            <li><a href="/faq">帮助中心</a></li>
                            <li><a href="/0x">建议反馈</a></li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
    </div>

    <div class="wrap">
        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
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
                    <dd>本站由 <a target="_blank" href="http://qingcloud.com/">青云 QingCloud</a> 提供云计算服务<br><a
                            target="_blank"
                            href="https://www.upyun.com/?utm_source=segmentfault&utm_medium=link&utm_campaign=upyun&md=segmentfault">又拍云</a>
                        提供 CDN 存储服务
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

    <!--登陆窗口-->
    <?php if (Yii::$app->user->isGuest): ?>
        <script id="loginModal" type="text/template">
            <div class="row bg-white login-modal">
                <div class="col-md-4 col-sm-12 col-md-push-7 login-wrap">
                    <h1 class="h4 text-muted login-title">用户登录</h1>

                    <form action="/api/user/login" method="POST" role="form" class="mt30">
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control" name="mail" required
                                   placeholder="hello@segmentfault.com">
                        </div>
                        <div class="form-group">
                            <label class="control-label">密码</label>
                            <input type="password" class="form-control" name="password" required placeholder="密码">
                        </div>
                        <div class="form-group clearfix">
                            <div class="checkbox pull-left">
                                <label><input name="remember" type="checkbox" value="1" checked> 记住登录状态</label>
                            </div>
                            <button type="submit" class="btn btn-primary pull-right pl20 pr20">登录</button>
                        </div>
                    </form>
                    <p class="h4 text-muted visible-xs-block h4">快速登录</p>

                    <div class="widget-login mt30">
                        <p class="text-muted mt5 mr10 pull-left hidden-xs">快速登录</p>
                        <a href="/user/oauth/google" class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-google"><span
                                class="icon-sn-google"></span> <strong class="visible-xs-inline">Google 账号</strong></a>
                        <a href="/user/oauth/github" class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-github"><span
                                class="icon-sn-github"></span> <strong class="visible-xs-inline">Github 账号</strong></a>
                        <a href="/user/oauth/weibo" class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-weibo"><span
                                class="icon-sn-weibo"></span> <strong class="visible-xs-inline">新浪微博账号</strong></a>
                        <a href="/user/oauth/qq" class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-qq"><span
                                class="icon-sn-qq"></span> <strong class="visible-xs-inline">QQ 账号</strong></a>
                        <a href="/user/oauth/weixin" class="btn pl5 pr5 mb5 btn-default btn-sm btn-sn-wechat"><span
                                class="icon-sn-wechat"></span> <strong class="visible-xs-inline">微信账号</strong></a>
                        <button type="button" class="btn mb5 btn-default btn-sm btn-sn-more" id="loginShowMore">...
                        </button>
                        <a href="/user/oauth/twitter"
                           class="btn pl5 pr5 mb5 btn-default btn-sn-twitter btn-sm hidden"><span
                                class="icon-sn-twitter"></span> <strong class="visible-xs-inline">Twitter
                                账号</strong></a>
                        <a href="/user/oauth/facebook"
                           class="btn pl5 pr5 mb5 btn-default btn-sn-facebook btn-sm hidden"><span
                                class="icon-sn-facebook"></span> <strong class="visible-xs-inline">Facebook 账号</strong></a>
                        <a href="/user/oauth/douban"
                           class="btn pl5 pr5 mb5 btn-default btn-sn-douban btn-sm hidden"><span
                                class="icon-sn-douban"></span> <strong class="visible-xs-inline">豆瓣账号</strong></a>
                    </div>
                </div>
                <div class="login-vline hidden-xs hidden-sm"></div>
                <div class="col-md-4 col-md-pull-3 col-sm-12 login-wrap">
                    <h1 class="h4 text-muted login-title">创建新账号</h1>

                    <form action="/api/user/register" method="POST" role="form" class="mt30">
                        <div class="form-group">
                            <label for="name" class="control-label">用户名</label>
                            <input type="text" class="form-control" name="name" required placeholder="字母、数字等，用户名唯一">
                        </div>
                        <div class="form-group">
                            <label for="mail" class="control-label">Email</label>
                            <input type="hidden" style="display:none" name="mail">
                            <input type="email" autocomplete="off" class="form-control register-mail" name="mail"
                                   required
                                   placeholder="hello@segmentfault.com">
                        </div>
                        <div class="form-group">
                            <label for="password" class="control-label">密码</label>
                            <input type="password" class="form-control" name="password" required placeholder="不少于 6 位">
                        </div>
                        <div class="form-group" style="display:none;">
                            <label class="required control-label">验证码</label>
                            <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入下方的验证码">

                            <div class="mt10"><a id="loginReloadCaptcha" href="javascript:void(0)"><img
                                        data-src="/user/captcha?w=240&h=50" class="captcha" width="240"
                                        height="50"/></a></div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="checkbox pull-left">
                                同意并接受<a href="/tos" target="_blank">《服务条款》</a>
                            </div>
                            <button type="submit" class="btn btn-primary pl20 pr20 pull-right">注册</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-muted mt30">
                <a href="/user/forgot" class="ml5">找回密码</a>
            </div>
        </script>
    <?php endif; ?>

    <script>
        (function (w) {
            w.SF = {
                staticUrl: "//static.segmentfault.com",
            };
            w.SF.token = (function () {
                var _FGWHC = '2'//'p'
                    +//'XG4'
                    '4c' +//'8Rb'
                    '8Rb' + 'iLV'//'iLV'
                    + 'f50'//'ABn'
                    +//'jo'
                    'adc' +//'RwZ'
                    '13b' +//'Dk'
                    'f' + '46e'//'XT0'
                    +//'CtS'
                    '47' + 'bc'//'4TB'
                    + '4dc'//'43'
                    + 'c'//'IUx'
                    +//'X'
                    '907' + '2e3'//'Dq'
                    + '1'//'vjL'
                    + ''///*'YI'*/'YI'
                    +//'Q7'
                    'd', _vgT2Ja4 = [[3, 6], [3, 6]];

                for (var i = 0; i < _vgT2Ja4.length; i++) {
                    _FGWHC = _FGWHC.substring(0, _vgT2Ja4[i][0]) + _FGWHC.substring(_vgT2Ja4[i][1]);
                }

                return _FGWHC;
            })();
        })(window);
    </script>

    <script src="http://static.segmentfault.com/build/3rd/assets.ce4fe392.js"></script>
    <script>
        requirejs.config({
            baseUrl: "http://static.segmentfault.com/build/global/js"
        });
    </script>
    <script src="http://static.segmentfault.com/build/qa/js/index.6213ef0c.js"></script>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>