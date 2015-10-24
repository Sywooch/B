<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/9/27
 * Time: 21:52
 * Version:
 * Created by PhpStorm.
 */
use yii\bootstrap\Tabs;
use yii\helpers\Url;

?>


<div class="row">
    <div class="col-xs-12 col-md-9 main">
        <p class="main-title hidden-xs">
            今天，你在开发时遇到了什么问题呢？
            <a id="goAsk" href="/ask" class="btn btn-primary">我要提问</a>
        </p>

        <?php
        echo Tabs::widget(
                [
                        'items' => [
                                [
                                        'label'   => '最新的',
                                        'content' => 'Anim pariatur cliche...',
                                        'active'  => true,
                                ],
                                [
                                        'label' => '热门的',
                                        'url'   => Url::to(['/question/index/hottest']),
                                ],
                                [
                                        'label' => '未回答的',
                                        'url'   => Url::to(['/question/index/unanswered']),
                                ],
                        ],
                ]
        );
        ?>


        <ul class="nav nav-tabs nav-tabs-zen mb10">
            <li class="active"><a href="/questions/newest">最新的</a></li>
            <li><a href="/questions/hottest">热门的</a></li>
            <li><a href="/questions/unanswered">未回答</a></li>
        </ul>

        <div class="stream-list question-stream">
            <section class="stream-list__item">
                <div class="qa-rank">
                    <div class="votes hidden-xs">
                        0
                        <small>投票</small>
                    </div>
                    <div class="answers">
                        0
                        <small>回答</small>
                    </div>
                    <div class="views hidden-xs">
                        1
                        <small>浏览</small>
                    </div>
                </div>
                <div class="summary">
                    <ul class="author list-inline">
                        <li>
                            <a href="/u/Woody">Woody</a>
                            <span class="split"></span>
                            <a href="/q/1010000003798861" class="askDate" data-created="1443361045">刚刚提问</a>
                        </li>
                    </ul>
                    <h2 class="title"><a href="/q/1010000003798861">Xcode7上传archive的时候报Invalid Bundle的错误。</a>
                    </h2>
                    <ul class="taglist--inline ib">
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/ios" data-toggle="popover"
                                                data-original-title="ios" data-id="1040000000089442">ios</a>
                        </li>
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/xcode7" data-toggle="popover"
                                                data-original-title="xcode7"
                                                data-id="1040000002959191">xcode7</a></li>
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/ios9" data-toggle="popover"
                                                data-original-title="ios9" data-id="1040000002988972">ios9</a>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="stream-list__item">
                <div class="qa-rank">
                    <div class="votes hidden-xs">
                        0
                        <small>投票</small>
                    </div>
                    <div class="answers">
                        0
                        <small>回答</small>
                    </div>
                    <div class="views hidden-xs">
                        7
                        <small>浏览</small>
                    </div>
                </div>
                <div class="summary">
                    <ul class="author list-inline">
                        <li>
                            <a href="/u/Woody">Woody</a>
                            <span class="split"></span>
                            <a href="/q/1010000003798842" class="askDate" data-created="1443360684">6 分钟前提问</a>
                        </li>
                    </ul>
                    <h2 class="title"><a href="/q/1010000003798842">用Xcode7上传archive的时候报unexpected
                                                                    CFBundleExecutable Key的错误。</a></h2>
                    <ul class="taglist--inline ib">
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/xcode7" data-toggle="popover"
                                                data-original-title="xcode7"
                                                data-id="1040000002959191">xcode7</a></li>
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/ios" data-toggle="popover"
                                                data-original-title="ios" data-id="1040000000089442">ios</a>
                        </li>
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/ios9" data-toggle="popover"
                                                data-original-title="ios9" data-id="1040000002988972">ios9</a>
                        </li>
                        <li class="tagPopup"><a class="tag tag-sm"
                                                href="/t/qq%E7%AC%AC%E4%B8%89%E6%96%B9%E7%99%BB%E5%BD%95"
                                                data-toggle="popover" data-original-title="qq第三方登录"
                                                data-id="1040000002704172">qq第三方登录</a></li>
                        <li class="tagPopup"><a class="tag tag-sm" href="/t/qq%E7%99%BB%E5%BD%95"
                                                data-toggle="popover" data-original-title="qq登录"
                                                data-id="1040000000519011">qq登录</a></li>
                    </ul>
                </div>
            </section>


        </div>
        <!-- /.stream-list -->

        <div class="text-center">
            <ul class="pager">
                <li>以上是部分更新，你还可以查看</li>
                <li><a href="/questions/newest">全部问题</a></li>
                <li>或者</li>
                <li><a href="/questions/hottest">热门问题</a></li>
                <li>列表</li>
            </ul>
        </div>
    </div>
    <!-- /.main -->
    <div class="col-xs-12 col-md-3 side mt30">
        <aside class="widget-welcome">
            <h2 class="h4 title">最专业的开发者社区</h2>

            <p>最前沿的技术问答，最纯粹的技术切磋。让你不知不觉中开拓眼界，提高技能，认识更多朋友。</p>
            <ul class="list-unstyled">
                <li><a href="/user/oauth/google" class="3rdLogin btn btn-default btn-block btn-sn-google"><span
                                class="icon-sn-google"></span> Google 账号登录</a></li>
                <li><a href="/user/oauth/weibo" class="3rdLogin btn btn-default btn-block btn-sn-weibo"><span
                                class="icon-sn-weibo"></span> 微博账号登录</a></li>
            </ul>
        </aside>


        <div class="sfad-sidebar">
            <div class="sfad-item" data-adn="ad-981183" id="adid-981183">
                <button class="close" type="button" aria-hidden="true">&times;</button>
            </div>

            <div class="sfad-item" data-adn="ad-981184" id="adid-981184">
                <button class="close" type="button" aria-hidden="true">&times;</button>
            </div>

            <div class="sfad-item" data-adn="ad-981694" id="adid-981694">
                <button class="close" type="button" aria-hidden="true">&times;</button>
            </div>

        </div>


        <div class="widget-box">
            <h2 class="h4 widget-box__title">热议标签 <a href="/tags" title="更多">&raquo;</a></h2>
            <ul class="taglist--inline multi">
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089436"
                                        data-original-title="javascript" href="/t/javascript">javascript</a>
                </li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089387"
                                        data-original-title="php" href="/t/php">php</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089534"
                                        data-original-title="python" href="/t/python">python</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089434"
                                        data-original-title="css" href="/t/css">css</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089442"
                                        data-original-title="ios" href="/t/ios">ios</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089658"
                                        data-original-title="android" href="/t/android">android</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089918"
                                        data-original-title="node.js" href="/t/node.js">node.js</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089409"
                                        data-original-title="html5" href="/t/html5">html5</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000090203"
                                        data-original-title="go" href="/t/go">go</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089488"
                                        data-original-title="mongodb" href="/t/mongodb">mongodb</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089431"
                                        data-original-title="redis" href="/t/redis">redis</a></li>
                <li class="tagPopup"><a class="tag" data-toggle="popover" data-id="1040000000089556"
                                        data-original-title="程序员" href="/t/程序员">程序员</a></li>
            </ul>
        </div>

        <div class="widget-box">
            <h2 class="h4 widget-box__title">最近热门的</h2>
            <ul class="widget-links list-unstyled">
                <li class="widget-links__item">
                    <a href="/q/1010000002499628">微信支付：扫码后，提示 “当前使用此业务的用户过多，请稍后再试。”</a>
                    <small class="text-muted">
                        1 回答
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003797228">如何访问谷歌？</a>
                    <small class="text-muted">
                        12 回答
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003797424">一只青蛙一次可以跳上1级台阶，也可以跳上2级……它也可以跳上n级。求该青蛙跳上一个n级的台阶总共有多少种跳法。</a>
                    <small class="text-muted">
                        4 回答 | 已解决
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000000268754">微信自定义表情在哪个文件夹？</a>
                    <small class="text-muted">
                        2 回答
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003797714">“指针”是成熟的编程语言必须具有的概念吗？</a>
                    <small class="text-muted">
                        2 回答
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000000685938">请问phantom-proxy如何设置代理ip</a>
                    <small class="text-muted">
                        2 回答 | 已解决
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003796726">java做web比python做web好在哪，差在哪？现在用java的还多吗？</a>
                    <small class="text-muted">
                        3 回答 | 已解决
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003797842">有哪些好看的monospace字体？</a>
                    <small class="text-muted">
                        2 回答
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003797796">求大神解答密码问题</a>
                    <small class="text-muted">
                        1 回答
                    </small>
                </li>
                <li class="widget-links__item">
                    <a href="/q/1010000003770689">为什么会不需要else</a>
                    <small class="text-muted">
                        9 回答
                    </small>
                </li>
            </ul>
        </div>
    </div>
    <!-- /.side -->
</div>

