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
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\tabs\TabsX;


?>


<div class="row">
    <div class="col-xs-12 col-md-9 main">
        <p class="main-title hidden-xs mt10">
            今天，你在深圳遇到了什么问题呢？
            <a id="goAsk" href="<?= Url::to(['/question/create']) ?>" class="btn btn-primary">我要提问</a>
        </p>

        <?php
        $items = [
                [
                        'label'   => '<i class="glyphicon glyphicon-home"></i> 最新的',
                        'content' => $question_latest,
                        'active'  => true,
                    //'linkOptions' => ['data-url' => Url::to(['/site/fetch-tab?tab=1'])],
                ],
                [
                        'label'       => '<i class="glyphicon glyphicon-user"></i> 热门的',
                        'content'     => '$content2',
                        'linkOptions' => ['data-url' => Url::to(['/default/fetch-hot'])],
                ],
                [
                        'label'       => '<i class="glyphicon glyphicon-user"></i> 未回答',
                        'content'     => '$content2',
                        'linkOptions' => ['data-url' => Url::to(['/default/fetch-un-answer'])],
                ],

        ];
        // Ajax Tabs Above
        echo TabsX::widget(
                [
                        'items'        => $items,
                        'position'     => TabsX::POS_ABOVE,
                        'encodeLabels' => false,
                ]
        );
        ?>

        <!-- /.stream-list -->

        <div class="text-center">
            <ul class="pager">
                <li>以上是部分更新，你还可以查看</li>
                <li><?= Html::a('全部问题', ['question/latest']) ?></li>
                <li>或者</li>
                <li><?= Html::a('热门问题', ['question/hottest']) ?></li>
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

