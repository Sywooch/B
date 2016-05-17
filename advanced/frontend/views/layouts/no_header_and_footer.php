<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-04-06
 * Time: 20:57
 */

?>



<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1"/>
    <meta name="renderer" content="webkit"/>
    <meta property="qc:admins" content="15317273575564615446375"/>
    <meta property="og:image" content="https://sf-static.b0.upaiyun.com/v-5703995e/global/img/touch-icon.png"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta name="alexaVerifyID" content="LkzCRJ7rPEUwt6fVey2vhxiw1vQ"/>
    <meta name="apple-itunes-app" content="app-id=958101793, app-argument=">
    <title>创建新账号 - SegmentFault</title>


    <meta name="description" content="专注于程序员开发者的社区平台，提供开发相关的高质量问答，专栏，笔记，招聘，线下活动等服务。"/>

    <meta name="keywords" content="SegmentFault 开发者 程序员 问答 专栏 博客 笔记 招聘 活动 黑客马拉松"/>

    <link rel="search" type="application/opensearchdescription+xml" href="/opensearch.xml" title="SegmentFault"/>
    <link rel="shortcut icon" href="https://sf-static.b0.upaiyun.com/v-5703995e/global/img/favicon.ico"/>
    <link rel="apple-touch-icon" href="https://sf-static.b0.upaiyun.com/v-5703995e/global/img/touch-icon.png">

    <meta name="msapplication-TileColor" content="#009a61"/>
    <meta name="msapplication-square150x150logo" content="https://sf-static.b0.upaiyun.com/v-5703995e/global/img/touch-icon.png"/>

    <meta name="userId" value="" id="SFUserId"/>
    <meta name="userRank" value="" id="SFUserRank"/>

    <link rel="alternate" type="application/atom+xml" title="SegmentFault 最新问题" href="/feeds/questions">
    <link rel="alternate" type="application/atom+xml" title="SegmentFault 最新文章" href="/feeds/blogs">




    <link rel="stylesheet" href="https://sf-static.b0.upaiyun.com/v-5703995e/global/css/global.css"/>
    <link rel="stylesheet" href="https://sf-static.b0.upaiyun.com/v-5703995e/login/css/login_all.css"/>
    <link rel="stylesheet" href="https://sf-static.b0.upaiyun.com/v-5703995e/global/css/responsive.css"/>


    <!--[if lt IE 9]>
    <link rel="stylesheet" href="https://sf-static.b0.upaiyun.com/v-5703995e/global/css/ie.css"/>
    <script src="https://sf-static.b0.upaiyun.com/v-5703995e/global/js/html5shiv.js"></script>
    <script src="https://sf-static.b0.upaiyun.com/v-5703995e/global/js/respond.js"></script>
    <![endif]-->

    <script src="https://sf-static.b0.upaiyun.com/v-5703995e/global/js/debug.js"></script>

</head>

<body class="login-register">
<!--[if lt IE 9]>
<div class="alert alert-danger topframe" role="alert">你的浏览器实在<strong>太太太太太太旧了</strong>，放学别走，升级完浏览器再说 <a target="_blank" class="alert-link" href="http://browsehappy.com">立即升级</a>
</div>
<![endif]-->

<div class="container">
    <div class="header text-center">
        <h1>
            <a href="/" class="logo">
                <img src="https://sf-static.b0.upaiyun.com/v-5703995e/global/img/logo-b.svg" alt="SegmentFault">
            </a>
        </h1>
        <p class="description text-muted">欢迎加入专业的中文开发者社区</p>
    </div>
    <div class="col-md-6 col-md-offset-3 bg-white login-wrap">
        <h1 class="h4 text-center text-muted login-title mb30">创建新账号</h1>
        <form role="form" id="user" >
            <input type="hidden" name="ref" value="">
            <div class="form-group">
                <label class="required">用户名</label>
                <input type="text" class="form-control" name="name" required placeholder="字母、数字等，用户名唯一">
            </div>
            <div class="form-group">
                <label class="required">Email</label>
                <input type="email" class="form-control" name="mail" required placeholder="hello@segmentfault.com">
            </div>
            <div class="form-group">
                <label for="" class="required">密码</label>
                <input type="password" class="form-control" name="password" required placeholder="不少于 6 位">
            </div>
            <div class="form-group" style="display:none;">
                <label class="required">验证码</label>
                <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入下方的验证码" disabled>
                <div class="mt10"><a id="reloadCaptcha"  href="javascript:void(0)"><img src="/user/captcha?w=240&h=50" width="240" height="50" /></a></div>
            </div>
            <div class="form-group">
                同意并接受<a href="/tos" target="_blank">《服务条款》</a>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-lg">注册</button>
            </div>
        </form>
    </div>

    <div class="hide col-md-6 col-md-offset-3 bg-white login-wrap" id="success">
        <h1 class="h4 text-center login-title mb30">注册成功</h1>
        <p class="mb20">感谢你注册 SegmentFault，我们发送了一封验证邮件到你的邮箱：<a id="successMail"></a>，请及时激活（1 小时内有效）。</p>
        <div class="text-center">
            <a href="javascript:void(0);" id="successGotoMail" class="btn btn-lg btn-primary mr10">前往邮箱</a>
            <a href="javascript:void(0);" id="successBack" class="btn btn-lg btn-default">回到之前页面</a>
        </div>
    </div>

    <div class="text-center col-md-12 login-link">
        <a href="/user/login">用户登录</a>
        |
        <a href="/">首页</a>
        |
        <a href="/user/forgot">找回密码</a>
    </div>
</div>

<script id="loginModal" type="text/template">
    <div class="row bg-white login-modal">
        <div class="col-md-4 col-sm-12 col-md-push-7 login-wrap">
            <h1 class="h4 text-muted login-title">用户登录</h1>
            <form action="/api/user/login" method="POST" role="form" class="mt30">
                <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="email" class="form-control" name="mail" required placeholder="hello@segmentfault.com">
                </div>
                <div class="form-group">
                    <label class="control-label">密码</label>
                    <input type="password" class="form-control" name="password" required placeholder="密码">
                </div>
                <div class="form-group clearfix">
                    <div class="checkbox pull-left">
                        <label><input name="remember" type="checkbox" value="1" checked> 记住登录状态</label>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right pl20 pr20" onclick='ga("send", "event", "email login button", "clicked", "login modal");mixpanel.track("login modal - email login clicked");'>登录</button>
                </div>
            </form>
            <p class="h4 text-muted visible-xs-block h4">快速登录</p>
            <div class="widget-login mt30">
                <p class="text-muted mt5 mr10 pull-left hidden-xs">快速登录</p>
                <a href="/user/oauth/google" class="btn btn-default btn-sm btn-sn-google" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "google"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-google"></span> <strong class="visible-xs-inline">Google 账号</strong></a>
                <a href="/user/oauth/github" class="btn btn-default btn-sm btn-sn-github" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "github"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-github"></span> <strong class="visible-xs-inline">Github 账号</strong></a>
                <a href="/user/oauth/weibo" class="btn btn-default btn-sm btn-sn-weibo" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "weibo"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-weibo"></span> <strong class="visible-xs-inline">新浪微博账号</strong></a>
                <a href="/user/oauth/qq" class="btn btn-default btn-sm btn-sn-qq" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "qq"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-qq"></span> <strong class="visible-xs-inline">QQ 账号</strong></a>
                <a href="/user/oauth/weixin" class="btn btn-default btn-sm btn-sn-weixin" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "qq"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-weixin"></span> <strong class="visible-xs-inline">微信账号</strong></a>
                <button id="loginShowMore" href="javascript:void(0);" class="btn mb5 btn-default btn-sm btn-sn-dotted"><span class="icon-sn-bg-dotted"></span><strong class="visible-xs-inline">•••</strong></button>
                <a href="/user/oauth/twitter" class="btn btn-default btn-sn-twitter btn-sm hidden" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "twitter"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-twitter"></span> <strong class="visible-xs-inline">Twitter 账号</strong></a>
                <a href="/user/oauth/facebook" class="btn btn-default btn-sn-facebook btn-sm hidden" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "facebook"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-facebook"></span> <strong class="visible-xs-inline">Facebook 账号</strong></a>
                <a href="/user/oauth/douban" class="btn btn-default btn-sn-douban btn-sm hidden" onclick='ga("send", "event", "3rd login button", "clicked", "login modal", {media: "douban"});mixpanel.track("login modal - 3rd login clicked");'><span class="icon-sn-bg-douban"></span> <strong class="visible-xs-inline">豆瓣账号</strong></a>
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
                    <input type="email" autocomplete="off" class="form-control register-mail" name="mail" required placeholder="hello@segmentfault.com">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">密码</label>
                    <input type="password" class="form-control" name="password" required placeholder="不少于 6 位">
                </div>
                <div class="form-group" style="display:none;">
                    <label class="required control-label">验证码</label>
                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入下方的验证码">
                    <div class="mt10"><a id="loginReloadCaptcha"  href="javascript:void(0)"><img data-src="/user/captcha?w=240&h=50" class="captcha" width="240" height="50" /></a></div>
                </div>
                <div class="form-group clearfix">
                    <div class="checkbox pull-left">
                        同意并接受<a href="/tos" target="_blank">《服务条款》</a>
                    </div>
                    <button type="submit" class="btn btn-primary pl20 pr20 pull-right" onclick='ga("send", "event", "email register button", "clicked", "login modal");mixpanel.track("login modal - email register clicked");'>注册</button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center text-muted mt30">
        <a href="/user/forgot" class="ml5">找回密码</a>
    </div>
</script>

<script crossorigin src="https://sf-static.b0.upaiyun.com/v-5703995e/3rd/assets.js"></script>
<script crossorigin src="https://sf-static.b0.upaiyun.com/v-5703995e/login/js/register.js"></script>

</body>
</html>

