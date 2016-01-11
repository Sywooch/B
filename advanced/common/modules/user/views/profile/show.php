<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use common\helpers\TemplateHelper;
use common\modules\user\UserAsset;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var               $user
 */

$this->title = $user['username'];
$this->params['breadcrumbs'][] = $this->title;

UserAsset::register($this);
?>
<?php
$this->beginBlock('top-header');
?>
<header class="user-header bg-gray pt20">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-header media">
                    <div class="pull-left">
                        <?= TemplateHelper::showUserAvatar($user['id'], 128, true); ?>
                    </div>

                    <div class="media-body">
                        <h4 class="media-heading"><?= $user['username'] ?></h4>
                        <ul class="sn-inline">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <ul class="list-unstyled profile-links">
                    <li>现居城市：<a href="/user/settings">填写城市</a></li>
                    <li>现任职位：<a href="/user/profile">填写工作经历</a></li>
                    <li>院校专业：<a href="/user/profile">填写教育经历</a></li>
                    <li>个人网站：<a href="/user/settings">填写个人网站</a></li>
                </ul>
            </div>
            <div class="text-right col-md-3">
                <p class="mt30">
                    <strong>
                        <?= Html::a(
                            $user['count_fans'],
                            [''],
                            [
                                'class' => 'funsCount',
                            ]
                        ); ?>
                    </strong> 个粉丝
                </p>

                <p>
                    <a href="/u/liu_jing"
                       data-toggle="tooltip"
                       data-placement="bottom"
                       title=""
                       data-original-title="liu_jing">
                        <img class="avatar-24"
                             src="http://sfault-avatar.b0.upaiyun.com/362/203/3622039558-1030000000616304_small"></a>
                </p>
            </div>
        </div>
    </div>
</header>

<?php
$this->endBlock();
?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-4 profile">
                <ul class="list-unstyled profile-ranks">
                    <li title="声望">
                        <strong><?= $user['score'] ?></strong>
                        <span class="text-muted">声望值</span>

                    </li>
                    <li title="财富">
                        <strong style=""><?= TemplateHelper::showHumanCurrencyValue($user['currency']) ?></strong>
                        <span class="text-muted"><?= TemplateHelper::showHumanCurrencyUnit($user['currency']) ?></span>
                    </li>
                    <li title="支持">
                        <strong><?= $user['count_useful'] ?></strong>
                        <span class="text-muted">次被赞</span>
                    </li>
                </ul>
                <ul class="rep-rects clearfix">
                    <?php foreach ($score_list as $date => $score): ?>
                        <?php if (array_sum($score) == 0): ?>
                            <?= Html::tag(
                                'li',
                                '',
                                [
                                    'class' => 'rect bg-gray',
                                    'data'  => [
                                        'toggle'    => 'tooltip',
                                        'placement' => 'top',
                                    ],
                                    'title' => '没有获得声望或货币',
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= Html::tag(
                                'li',
                                '',
                                [
                                    'class' => (array_sum($score) > 0) ? 'rect bg-green' : 'rect bg-red',
                                    'style' => sprintf(
                                        "opacity: %f",
                                        max(
                                            round(
                                                ($score['currency'] > 0 ? ($score['currency'] / $total_currency) : 0) + ($score['credit'] > 0 ? ($score['credit'] / $total_credit) : 0),
                                                1
                                            ),
                                            0.3
                                        )
                                    ),
                                    'data'  => [
                                        'html'      => 'true',
                                        'toggle'    => 'tooltip',
                                        'placement' => 'top',
                                    ],
                                    'title' => sprintf(
                                        "声望：%d<br />货币：%d<br /> %s ",
                                        $score['credit'],
                                        $score['currency'],
                                        $date
                                    ),
                                ]
                            ) ?>

                        <?php endif; ?>

                    <?php endforeach; ?>
                </ul>
                <div class="profile-bio mono">
                    <?php if (isset($user['description'])) : ?>
                        <?= $user['description'] ?>
                    <?php else: ?>
                        <p>个人简介都不给 &lt;(￣ ﹌ ￣)&gt;</p>
                    <?php endif; ?>
                </div>
                <?php if ($tag_list): ?>
                    <div class="profile-goodjob">
                        <strong>擅长标签</strong>

                        <ul class="taglist--inline multi">
                            <?php foreach ($tag_list as $tag): ?>
                                <li class="tagPopup">
                                    <?= Html::a(
                                        $tag['name'],
                                        ['tag/view', 'id' => $tag['id']],
                                        [
                                            'class' => 'tag',
                                        ]
                                    ); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!--<div class="joindate">
                            始于 3月20日
                        </div>-->
                    </div>
                <?php endif; ?>
            </div>

            <!-- Nav tabs -->
            <div class="col-md-8">


                <ul class="nav nav-pills">
                    <li class="active"><a href="/u/xiamao"><span>动态</span></a></li>
                    <li><a href="/u/xiamao/rank"><span>声望</span></a></li>
                    <li><a href="/u/xiamao/tags"><span>标签</span></a></li>
                    <li><a href="/u/xiamao/answers"><span>回答</span> <span class="badge">16</span></a></li>
                    <li><a href="/u/xiamao/questions"><span>提问</span> <span class="badge">17</span></a></li>
                    <li><a href="/u/xiamao/blogs"><span>专栏文章</span> <span class="badge">0</span></a></li>
                    <li><a href="/u/xiamao/bookmarks"><span>收藏</span> <span class="badge">6</span></a></li>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <span>关注的</span> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/user/questions/following">关注的问题</a></li>
                            <li><a href="/u/xiamao/following/tags">关注的标签 <span class="badge">2</span></a></li>
                            <li><a href="/u/xiamao/following/users">关注的人 <span class="badge">2</span></a></li>

                            <li><a href="/u/xiamao/following/blogs">关注的专栏</a></li>
                        </ul>
                    </li>

                </ul>
                <h2 class="h4">最近动态</h2>

                <div class="widget-active clearfix">

                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月24日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                                <span class="pull-right badge green">+1</span>
                            </p>
                            <div class="widget-active--right__title">
                                <span class="label label-success pull-left mr5">采纳</span>
                                <h4><a href="/q/1010000004026787/a-1020000004037848">PHP redis hIncrBy 递增出现问题</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/redis"
                                                            data-toggle="popover"
                                                            data-id="1040000000089431"
                                                            data-original-title="redis">redis</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/hincrby"
                                                            data-toggle="popover"
                                                            data-id="1040000004026740"
                                                            data-original-title="hincrby">hincrby</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                我找到问题了 This is expected, you can't run INCR, INCRBY, or HINCRBY on serialized data.
                                初始化r...
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月21日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000004026787">PHP redis hIncrBy 递增出现问题</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/redis"
                                                            data-toggle="popover"
                                                            data-id="1040000000089431"
                                                            data-original-title="redis">redis</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/hincrby"
                                                            data-toggle="popover"
                                                            data-id="1040000004026740"
                                                            data-original-title="hincrby">hincrby</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月16日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <span class="label label-success pull-left mr5">采纳</span>
                                <h4><a href="/q/1010000003992718/a-1020000004000132">Yii2
                                                                                     setFlash后redirect后，getFlash总是为空</a>
                                </h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/yii2"
                                                            data-toggle="popover"
                                                            data-id="1040000000409363"
                                                            data-original-title="yii2">yii2</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                我的解决方法是：在Yii::$app-&gt;controller-&gt;redirect()后，Yii::$app-&gt;end()
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月15日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                                <span class="pull-right badge green">+1</span>
                            </p>
                            <div class="widget-active--right__title">
                                <span class="label label-success pull-left mr5">采纳</span>
                                <h4><a href="/q/1010000003944200/a-1020000003997610">帮忙推荐一款PHP接收邮件的类</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E9%82%AE%E4%BB%B6%E6%8E%A5%E6%94%B6"
                                                            data-toggle="popover"
                                                            data-id="1040000003944198"
                                                            data-original-title="邮件接收">邮件接收</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                找到一款 php-imap　可以在github上搜索，还蛮好用的。
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月14日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  2
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003993146/a-1020000003993511">如何让Github管理我网站服务器上的文件？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/github"
                                                            data-toggle="popover"
                                                            data-id="1040000000091226"
                                                            data-original-title="github">github</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                楼主方向搞反了，你是不是自己用虚拟主机，然后自己的用FTP修改服务器上的文件？ 正常的模式是 修改本地...
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月13日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003992718">Yii2 setFlash后redirect后，getFlash总是为空</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/yii2"
                                                            data-toggle="popover"
                                                            data-id="1040000000409363"
                                                            data-original-title="yii2">yii2</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月9日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003972085">Yii2框架中model基本上都是static方法</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/yii2"
                                                            data-toggle="popover"
                                                            data-id="1040000000409363"
                                                            data-original-title="yii2">yii2</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月9日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003970586">知乎的获取推荐邀请回答用户的算法是什么样的？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E7%9F%A5%E4%B9%8E"
                                                            data-toggle="popover"
                                                            data-id="1040000000090573"
                                                            data-original-title="知乎">知乎</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月5日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003956096">linux 安装了xunsearch服务，但是PHP连接显示：目标计算机积极拒绝</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/xunsearch"
                                                            data-toggle="popover"
                                                            data-id="1040000000513099"
                                                            data-original-title="xunsearch">xunsearch</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月3日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                                <span class="pull-right badge red">-1</span>
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003946420/a-1020000003947475">选什么前端框架？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/web%E5%89%8D%E7%AB%AF%E5%BC%80%E5%8F%91"
                                                            data-toggle="popover"
                                                            data-id="1040000000117807"
                                                            data-original-title="web前端开发">web前端开发</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E5%89%8D%E7%AB%AF%E6%A1%86%E6%9E%B6"
                                                            data-toggle="popover"
                                                            data-id="1040000000457749"
                                                            data-original-title="前端框架">前端框架</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/javascript"
                                                            data-toggle="popover"
                                                            data-id="1040000000089436"
                                                            data-original-title="javascript">javascript</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/html"
                                                            data-toggle="popover"
                                                            data-id="1040000000089571"
                                                            data-original-title="html">html</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E5%89%8D%E7%AB%AF%E5%BC%80%E5%8F%91"
                                                            data-toggle="popover"
                                                            data-id="1040000002426727"
                                                            data-original-title="前端开发">前端开发</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                就Angularjs了。
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月3日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                                <span class="pull-right badge green">+1</span>
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003944200">帮忙推荐一款PHP接收邮件的类</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E9%82%AE%E4%BB%B6%E6%8E%A5%E6%94%B6"
                                                            data-toggle="popover"
                                                            data-id="1040000003944198"
                                                            data-original-title="邮件接收">邮件接收</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">11月2日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  1
                                <span class="pull-right badge green">+1</span>
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003939675">你的PHP项目中还在用时间戳么？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月29日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003926761">PHP self::$abc，如果$abc是一个拼凑变量，怎么办？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月27日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  1
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003912117/a-1020000003912194">【求助】请教各位前辈关于竞业协议和保密协议会影响就业求职吗？</a>
                                </h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E6%B1%82%E8%81%8C"
                                                            data-toggle="popover"
                                                            data-id="1040000000334705"
                                                            data-original-title="求职">求职</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E5%B0%B1%E4%B8%9A"
                                                            data-toggle="popover"
                                                            data-id="1040000000700984"
                                                            data-original-title="就业">就业</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                看你的公司实力，还要看你的在项目中在作用，如果是头或核心人员，会有约束。 小公司，或自身能力一般的...
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月13日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003852703">IDE中如何设置，可以让yii2 createObject创建的实例自动提示</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/yii2"
                                                            data-toggle="popover"
                                                            data-id="1040000000409363"
                                                            data-original-title="yii2">yii2</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月13日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  1
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003849810">Yii2框架中，有必要再分离service层么？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/php"
                                                            data-toggle="popover"
                                                            data-id="1040000000089387"
                                                            data-original-title="php">php</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/yii2"
                                                            data-toggle="popover"
                                                            data-id="1040000000409363"
                                                            data-original-title="yii2">yii2</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月9日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003833775">关于检索类似话题机制</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E7%B1%BB%E4%BC%BC%E8%AF%9D%E9%A2%98"
                                                            data-toggle="popover"
                                                            data-id="1040000003833737"
                                                            data-original-title="类似话题">类似话题</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月8日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003829501">关于项目中使用markdown编辑器</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/markdown"
                                                            data-toggle="popover"
                                                            data-id="1040000000090338"
                                                            data-original-title="markdown">markdown</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>


                    <section class="widget-active__answer">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-comment"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">10月5日回答
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003794405/a-1020000003819838">网站前后端分离问题</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/javascript"
                                                            data-toggle="popover"
                                                            data-id="1040000000089436"
                                                            data-original-title="javascript">javascript</a>
                                    </li>
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/%E5%89%8D%E5%90%8E%E7%AB%AF%E5%88%86%E7%A6%BB"
                                                            data-toggle="popover"
                                                            data-id="1040000000736938"
                                                            data-original-title="前后端分离">前后端分离</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="widget-active--right__quote">
                                作为PHP开发人员我经历了两个阶段前后端分离。 一是PHP写完mc，等跟前端开发人员开发完v，我在套进来。...
                            </p>
                        </div>
                    </section>


                    <section class="widget-active__ask">
                        <div class="widget-active--left">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </div>
                        <div class="widget-active--right">
                            <p class="widget-active--right__info">9月30日提问
                                <small class="ml10 glyphicon glyphicon-comment"></small>
                                                                  0
                                <span class="pull-right badge green">+1</span>
                            </p>
                            <div class="widget-active--right__title">
                                <h4><a href="/q/1010000003810891">BachEditor如何配置？</a></h4>
                                <ul class="taglist--inline ib">
                                    <li class="tagPopup"><a class="tag tag-sm"
                                                            href="/t/bacheditor"
                                                            data-toggle="popover"
                                                            data-id="1040000002495285"
                                                            data-original-title="bacheditor">bacheditor</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>


