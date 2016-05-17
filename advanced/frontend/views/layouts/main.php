<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\components\user\User;
use common\helpers\TimeHelper;
use common\modules\user\models\LoginForm;

use kartik\icons\Icon;
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
<body class="user-index">
<?php $this->beginBody() ?>
<!--[if lt IE 9]>
<div class="alert alert-danger topframe" role="alert">
    你的浏览器实在<strong>太太太太太太旧了</strong>，放学别走，升级完浏览器再说
    <a target="_blank" class="alert-link" href="http://browsehappy.com">立即升级</a>
</div>
<![endif]-->
<?= Alert::widget(
    [
        'options' => [
            //'style' => 'margin-top:-10px',
        ],
    ]
); ?>
<div class="global-nav">
    <nav class="container nav">
        <h1 class="logo"><a class="sf" href="/">SegmentFault</a></h1>
        <a href="/ask" class="visible-xs-block pull-right m-ask m-toptools">
            <span class="glyphicon glyphicon-pencil"></span>
        </a>
        <ul class="menu list-inline pull-left hidden-xs">
            <li class="menu__item"><?= Html::a('动态', ['/question']) ?></li>
            <li class="menu__item"><?= Html::a('问题', ['/question']) ?></li>
            <li class="menu__item"><?= Html::a('文章', ['/article']) ?></li>
        </ul>
        <ul class="opts pull-right list-inline hidden-xs">
            <li class="opts__item dropdown hoverDropdown write-btns">
                <a class="dropdownBtn"
                   data-toggle="dropdown"
                   href="/ask">撰写<span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right ">
                    <li><?= Html::a('提问题', ['/question/create']) ?></li>
                    <li><a href="/write">写文章</a></li>
                </ul>
            </li>
            <li class="opts__item message has-unread ">
                <a id="dLabel"
                   class="dropdown-toggle-message"
                   href="javascript:;"><span class="sr-only">消息</span>
                    <span id="messageCount"
                          class="fa fa-bell-o"></span><span
                        class="has-unread__count">1</span></a>
                
                <div class="opts__item--message hide">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs nav-tabs-message">
                                <li role="presentation" class="active">
                                    <a href="#messageGeneral"
                                       id="home-tab"
                                       role="tab"
                                       data-toggle="tab"
                                       aria-controls="home"
                                       aria-expanded="true"><i class="fa fa-bullhorn"></i><span
                                            class="notice-dot hide notice-dot-general"></span></a>
                                </li>
                                <li role="presentation" class="">
                                    <a href="#messageRanked"
                                       id="home-tab"
                                       role="tab"
                                       data-toggle="tab"
                                       aria-controls="home"
                                       aria-expanded="true"><i class="fa fa-thumbs-o-up"></i><span
                                            class="notice-dot hide notice-dot-ranked"></span></a>
                                </li>
                                <li role="presentation" class="">
                                    <a href="#messageFollowed"
                                       id="home-tab"
                                       role="tab"
                                       data-toggle="tab"
                                       aria-controls="home"
                                       aria-expanded="true"><i class="fa fa-user-plus"></i><span
                                            class="notice-dot hide notice-dot-followed"></span></a>
                                </li>
                                <li role="presentation" class="">
                                    <a href="#messageInbox"
                                       id="home-tab"
                                       role="tab"
                                       data-toggle="tab"
                                       aria-controls="home"
                                       aria-expanded="true"><i class="fa fa-commenting"></i><span
                                            class="notice-dot hide notice-dot-inbox"></span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="messageGeneral"></div>
                                <div role="tabpanel" class="tab-pane" id="messageRanked"></div>
                                <div role="tabpanel" class="tab-pane" id="messageFollowed"></div>
                                <div role="tabpanel" class="tab-pane" id="messageInbox"></div>
                                <script type="text/template" id="messageGeneralTpl">
                                    <ul
                                        class="mCustomScrollbar-message"
                                        data-proto="general"
                                        data-mcs-theme="minimal-dark"><% _.each(general,function(d){ %>
                                        <li class="<%= d.viewed>0 ?'':'bg-warning'%>"><%= d.sentence %>&nbsp;<a
                                                href="<%= d.url %>"><%= d.excerpt %></a></li>
                                                                      <% }) %>
                                    </ul></script>
                                <script type="text/template" id="item--general"><% _.each(general,function(d){ %>
                                    <li class="<%= d.viewed>0 ?'':'bg-warning'%>"><%= d.sentence %>&nbsp;<a
                                            href="<%= d.url %>"><%= d.excerpt %></a></li><% }) %></script>
                                <script type="text/template" id="messageRankedTpl">
                                    <ul
                                        class="mCustomScrollbar-message"
                                        data-proto="ranked"
                                        data-mcs-theme="minimal-dark"><% _.each(ranked,function(d){ %>
                                        <li class="<%= d.viewed>0 ?'':'bg-warning'%>"><span class="badge
                                                    <% if(d.voted && d.voted.rank!=0){ %><%= d.voted.rank > 0 ? 'green':'red' %><% } else { %> transparent <% } %>"><% if(d.voted){ %><%= d.voted.rank > 0 ? '+'+d.voted.rank:d.voted.rank %><% } %></span>
                                            <div class="rank-desc"><%= d.sentence %>&nbsp;<a href="<%= d.url %>"><%=
                                                                                                                 d.excerpt
                                                                                                                 %></a>
                                            </div>
                                        </li>
                                                                      <% }) %>
                                    </ul></script>
                                <script type="text/template" id="item--ranked"><% _.each(ranked,function(d){ %>
                                    <li class="<%= d.viewed>0 ?'':'bg-warning'%>"><span class="badge
                                                    <% if(d.voted && d.voted.rank!=0){ %><%= d.voted.rank > 0 ? 'green':'red' %><% } else { %> transparent <% } %>"><% if(d.voted){ %><%= d.voted.rank > 0 ? '+'+d.voted.rank:d.voted.rank %><% } %></span>
                                        <div class="rank-desc"><%= d.sentence %>&nbsp;<a href="<%= d.url %>"><%=
                                                                                                             d.excerpt
                                                                                                             %></a>
                                        </div>
                                    </li><% }) %></script>
                                <script type="text/template" id="messageFollowedTpl"><p class="follow-tips">他们最近关注了你</p>
                                    <ul class="mCustomScrollbar-message"
                                        data-proto="followed"
                                        data-mcs-theme="minimal-dark"><% _.each(followed,function(d){ %>
                                        <li class="<%= d.viewed>0 ?'':'bg-warning'%>">
                                            <img class="follower__img avatar-32"
                                                 src="<%= d.triggerUser[0].avatarUrl %>">
                                            <div class="follower__info"><% if(d.triggerUser[0].isFollowed){ %>
                                                <button data-id="<%= d.triggerUser[0].id %>"
                                                        class="btn btn-default btn-xs message__btn--unfollow pull-right active">
                                                    已关注
                                                </button>
                                                                        <% }else{ %>
                                                <button data-id="<%= d.triggerUser[0].id %>"
                                                        class="btn btn-default btn-xs message__btn--follow pull-right">
                                                    关注
                                                </button>
                                                                        <% } %><a href="<%= d.triggerUser[0].url %>"><%=
                                                                                                                     d.triggerUser[0].name
                                                                                                                     %></a><br><span><%= d.triggerUser[0].rank %> 声望</span>
                                            </div>
                                        </li>
                                                                      <% }) %>
                                    </ul></script>
                                <script type="text/template" id="item--followed"><% _.each(followed,function(d){ %>
                                    <li class="<%= d.viewed>0 ?'':'bg-warning'%>"><img class="follower__img avatar-32"
                                                                                       src="<%= d.triggerUser[0].avatarUrl %>">
                                        <div class="follower__info"><% if(d.triggerUser[0].isFollowed){ %>
                                            <button data-id="<%= d.triggerUser[0].id %>"
                                                    class="btn btn-default btn-xs message__btn--unfollow pull-right active">
                                                已关注
                                            </button>
                                                                    <% }else{ %>
                                            <button data-id="<%= d.triggerUser[0].id %>"
                                                    class="btn btn-default btn-xs message__btn--follow pull-right">
                                                关注
                                            </button>
                                                                    <% } %><a href="<%= d.triggerUser[0].url %>"><%=
                                                                                                                 d.triggerUser[0].name
                                                                                                                 %></a><br><span><%= d.triggerUser[0].rank %> 声望</span>
                                        </div>
                                    </li><% }) %></script>
                                <script type="text/template" id="messageInboxTpl">
                                    <p class="follow-tips">他们给你发了私信</p>
                                    <ul class="mCustomScrollbar-message"
                                        data-proto="inbox"
                                        data-mcs-theme="minimal-dark"><% _.each(inboxes,function(d){ %>
                                        <li class="<%= d.viewed>0 ?'':'bg-warning'%>" data-click="<%= d.url %>">
                                            <img class="follower__img avatar-32" src="<%= d.targetUser.avatarUrl %>">
                                            <div class="follower__info"><a href="<%= d.url %>"><%= d.targetUser.name
                                                                                               %></a><br>
                                                <span class="ellipsis inline-block" style="width: 245px;">
                                                    <%= d.lastMessage.content.content %></span>
                                            </div>
                                        </li>
                                                                      <% }) %><% if(inboxes.length ==0){ %>
                                        <p class="text-center">没有人给你发私信</p><% } %>
                                    </ul></script>
                                <script type="text/template" id="item--inboxes"><% _.each(inboxes,function(d){ %>
                                    <li class="<%= d.viewed>0 ?'':'bg-warning'%>" data-click="<%= d.url %>">
                                        <img class="follower__img avatar-32"
                                             src="<%= d.targetUser.avatarUrl %>">
                                        <div class="follower__info"><a href="<%= d.targetUser.url %>"><%=
                                                                                                      d.targetUser.name
                                                                                                      %></a><br><span
                                                class="ellipsis inline-block"
                                                style="width: 245px;"><%= d.content %></span></div>
                                    </li><% }) %></script>
                            </div>
                            <p class="opts__item--message-loading follow-tips">loading</p></div>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-sm-6"><a href="javascript:;"
                                                         class="message-ingore-all hide"><span class="glyphicon glyphicon-ok-sign"></span>
                                        全部标记为已读</a></div>
                                <div class="col-sm-6"><a class="opts__item--message-view-all"
                                                         href="/user/notifications">查看全部
                                                                                    »</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="opts__item user dropdown hoverDropdown">
                <a class="dropdownBtn user-avatar"
                   data-toggle="dropdown"
                   style="background-image: url('https://sfault-avatar.b0.upaiyun.com/218/005/2180055738-56aca6eb735e1_big64')"
                   href="/u/xiamao">

                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="/u/xiamao">我的主页</a></li>
                    <li><a href="/u/xiamao/profile">我的档案</a></li>
                    <li><a href="/blog/ouewqa">我的专栏</a></li>
                    <li><a href="/user/draft">草稿箱</a></li>
                    <li><a href="/user/bookmarks">收藏夹</a></li>
                    <li class="divider"></li>
                    <li><a href="/user/settings">账号设置</a></li>
                    <li class="divider"></li>
                    <li><a href="/api/user/logout?_=e34584efbc1d2d499f264479bda6c82a">退出</a></li>
                </ul>
            </li>
        </ul>
        <form action="/search" class="header-search pull-right hidden-sm hidden-xs">
            <button class="btn btn-link"><span class="sr-only">搜索</span><span class="glyphicon glyphicon-search"></span>
            </button>
            <input id="searchBox" name="q" type="text" placeholder="输入关键字搜索" class="form-control" value="">
        </form>
    </nav>
</div>
<div class="global-navTags">
    <div class="container">
        <nav class=" nav">
            <ul class="nav__list">
                <li class="nav__item"><a href="/" class="active"><i class="fa fa-home"></i>home</a></li>
                <li class="nav__item"><a href="/timeline"><i class="fa fa-list-alt"></i>feed
                    </a></li>
                <li class="nav__item nav__item--split"><a><span class="split"></span></a></li>
                <li class="nav__item tag-nav__item"><a href="/t/%E7%A8%8B%E5%BA%8F%E5%91%98">程序员</a></li>
                <li class="nav__item tag-nav__item"><a href="/t/yii2">yii2</a></li>
                <li class="nav__item tag-nav__item"><a href="/t/php">php</a></li>
                <li class="nav__item nav__item--more" data-open="0"><a class="nav__item--more-link"
                                                                       href="javascript:void(0)">
                        <div class="tag__more">
                            <i class="tag__more--icon"></i><i class="tag__more--icon"></i><i class="tag__more--icon"></i>
                        </div>
                    </a></li>
            </ul>
            <div class="tag-mgr__box hide"><input class="tag-mgr__query"
                                                  type="text"
                                                  placeholder="搜索关注的标签"
                                                  data-tags="[{&quot;id&quot;:&quot;1040000000089556&quot;,&quot;name&quot;:&quot;\u7a0b\u5e8f\u5458&quot;,&quot;url&quot;:&quot;\/t\/%E7%A8%8B%E5%BA%8F%E5%91%98&quot;},{&quot;id&quot;:&quot;1040000000409363&quot;,&quot;name&quot;:&quot;yii2&quot;,&quot;url&quot;:&quot;\/t\/yii2&quot;},{&quot;id&quot;:&quot;1040000000089387&quot;,&quot;name&quot;:&quot;php&quot;,&quot;url&quot;:&quot;\/t\/php&quot;}]">
                <div class="mCustomScrollbar _mCS_1 mCS-autoHide mCS_no_scrollbar"
                     data-mcs-theme="minimal-dark"
                     style="position: relative; overflow: visible;">
                    <div id="mCSB_1"
                         class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside"
                         style="max-height: 0px;"
                         tabindex="0">
                        <div id="mCSB_1_container"
                             class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y"
                             style="position:relative; top:0; left:0;"
                             dir="ltr">
                            <ul class="tag-mgr__list">
                                <li><a href="/t/%E7%A8%8B%E5%BA%8F%E5%91%98">程序员</a></li>
                                <li><a href="/t/yii2">yii2</a></li>
                                <li><a href="/t/php">php</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="mCSB_1_scrollbar_vertical"
                         class="mCSB_scrollTools mCSB_1_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical"
                         style="display: none;">
                        <div class="mCSB_draggerContainer">
                            <div id="mCSB_1_dragger_vertical"
                                 class="mCSB_dragger"
                                 style="position: absolute; min-height: 50px; top: 0px;"
                                 oncontextmenu="return false;">
                                <div class="mCSB_dragger_bar" style="line-height: 50px;"></div>
                            </div>
                            <div class="mCSB_draggerRail"></div>
                        </div>
                    </div>
                </div>
                <a href="/tags" class="btn btn-primary btn-sm tag-mgr__btn">标签管理</a></div>
        </nav>
    </div>
</div>

<div class="wrap">


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
                avatar: '<?=(Yii::$app->user->isGuest || empty(Yii::$app->user->identity->avatar)) ?
                    'https://sf-static.b0.upaiyun.com/v-56810165/global/img/user-64.png' : Yii::$app->user->identity->avatar ?>'
            }
        };

        window.app = app;
    })(window);
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
