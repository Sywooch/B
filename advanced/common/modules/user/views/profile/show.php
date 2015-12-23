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
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-09-26"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-09-27"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.56122448979592"
                        title=""
                        data-original-title="+6 声望<br>2015-09-28"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-09-29"></li>
                    <li class="rect bg-green"
                        style="opacity: 1"
                        title=""
                        data-original-title="+49 声望<br>2015-09-30"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.60204081632653"
                        title=""
                        data-original-title="+10 声望<br>2015-10-01"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-02"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-03"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-04"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-10-05"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-06"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-07"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.53061224489796"
                        title=""
                        data-original-title="+3 声望<br>2015-10-08"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.56122448979592"
                        title=""
                        data-original-title="+6 声望<br>2015-10-09"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.63265306122449"
                        title=""
                        data-original-title="+13 声望<br>2015-10-10"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-11"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-12"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.61224489795918"
                        title=""
                        data-original-title="+11 声望<br>2015-10-13"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-14"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-15"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-16"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-17"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-18"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-19"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-20"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-21"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-22"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-23"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-24"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-25"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.55102040816327"
                        title=""
                        data-original-title="+5 声望<br>2015-10-26"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-10-27"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-28"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.54081632653061"
                        title=""
                        data-original-title="+4 声望<br>2015-10-29"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-10-30"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-10-31"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.52040816326531"
                        title=""
                        data-original-title="+2 声望<br>2015-11-01"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.72448979591837"
                        title=""
                        data-original-title="+22 声望<br>2015-11-02"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.53061224489796"
                        title=""
                        data-original-title="+3 声望<br>2015-11-03"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.56122448979592"
                        title=""
                        data-original-title="+6 声望<br>2015-11-04"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.52040816326531"
                        title=""
                        data-original-title="+2 声望<br>2015-11-05"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-06"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-07"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.57142857142857"
                        title=""
                        data-original-title="+7 声望<br>2015-11-08"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.52040816326531"
                        title=""
                        data-original-title="+2 声望<br>2015-11-09"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-10"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-11"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.66326530612245"
                        title=""
                        data-original-title="+16 声望<br>2015-11-12"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-13"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-14"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.80612244897959"
                        title=""
                        data-original-title="+30 声望<br>2015-11-15"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.52040816326531"
                        title=""
                        data-original-title="+2 声望<br>2015-11-16"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-17"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-18"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-19"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-20"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-21"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-22"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-23"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.53061224489796"
                        title=""
                        data-original-title="+3 声望<br>2015-11-24"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.52040816326531"
                        title=""
                        data-original-title="+2 声望<br>2015-11-25"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-11-26"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-27"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-28"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.6530612244898"
                        title=""
                        data-original-title="+15 声望<br>2015-11-29"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-11-30"></li>
                    <li class="rect bg-green"
                        style="opacity: 0.51020408163265"
                        title=""
                        data-original-title="+1 声望<br>2015-12-01"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-12-02"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-12-03"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-12-04"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-12-05"></li>
                    <li class="rect bg-gray" title="" data-original-title="没有获得声望<br>2015-12-06"></li>
                </ul>
                <div class="profile-bio mono">
                    <?php if (isset($user['description'])) : ?>
                        <?= $user['description'] ?>
                    <?php else: ?>
                        <p>个人简介都不给 &lt;(￣ ﹌ ￣)&gt;</p>
                    <?php endif; ?>
                </div>
                <div class="profile-goodjob" id="goodJob" data-id="1030000002610133">
                    <strong>擅长标签</strong>

                    <div id="piechart" class="clearfix">
                        <svg height="200"
                             version="1.1"
                             width="319"
                             xmlns="http://www.w3.org/2000/svg"
                             style="overflow: hidden; position: relative; left: -0.5px; top: -0.5px;">
                            <desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Created with Raphaël 2.1.0
                            </desc>
                            <defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs>
                            <path fill="#59bb0c"
                                  stroke="#ffffff"
                                  d="M90,100L27.143785657290124,50.511654720496935A80,80,0,0,1,152.8562143427099,50.51165472049696Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#7edb35"
                                  stroke="#ffffff"
                                  d="M90,100L152.8562143427099,50.51165472049696A80,80,0,0,1,169.55126485821438,108.46145728927503Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#62e65d"
                                  stroke="#ffffff"
                                  d="M90,100L169.55126485821438,108.46145728927503A80,80,0,0,1,159.5300174916517,139.5673687223526Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#c4ed68"
                                  stroke="#ffffff"
                                  d="M90,100L159.5300174916517,139.5673687223526A80,80,0,0,1,138.70091432069765,163.46826722329882Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#e2ff9e"
                                  stroke="#ffffff"
                                  d="M90,100L138.70091432069765,163.46826722329882A80,80,0,0,1,111.18679212708216,177.1435016016502Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#f0f2dd"
                                  stroke="#ffffff"
                                  d="M90,100L111.18679212708216,177.1435016016502A80,80,0,0,1,80.54748913539257,179.43959993828315Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#d7f6a0"
                                  stroke="#ffffff"
                                  d="M90,100L80.54748913539257,179.43959993828315A80,80,0,0,1,51.302489031580826,170.0178737527003Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#5de6a9"
                                  stroke="#ffffff"
                                  d="M90,100L51.302489031580826,170.0178737527003A80,80,0,0,1,29.03816903975914,151.80400723858165Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#a0d2f6"
                                  stroke="#ffffff"
                                  d="M90,100L29.03816903975914,151.80400723858165A80,80,0,0,1,19.039133345742258,136.93988905880272Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#80def0"
                                  stroke="#ffffff"
                                  d="M90,100L19.039133345742258,136.93988905880272A80,80,0,0,1,12.856498398349785,121.18679212708219Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#666666"
                                  stroke="#ffffff"
                                  d="M90,100L12.856498398349785,121.18679212708219A80,80,0,0,1,27.14378565729011,50.511654720496956Z"
                                  stroke-width="1"
                                  stroke-linejoin="round"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); stroke-linejoin: round;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L27.143785657290124,50.511654720496935A80,80,0,0,1,152.8562143427099,50.51165472049696Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L152.8562143427099,50.51165472049696A80,80,0,0,1,169.55126485821438,108.46145728927503Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L169.55126485821438,108.46145728927503A80,80,0,0,1,159.5300174916517,139.5673687223526Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L159.5300174916517,139.5673687223526A80,80,0,0,1,138.70091432069765,163.46826722329882Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L138.70091432069765,163.46826722329882A80,80,0,0,1,111.18679212708216,177.1435016016502Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L111.18679212708216,177.1435016016502A80,80,0,0,1,80.54748913539257,179.43959993828315Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L80.54748913539257,179.43959993828315A80,80,0,0,1,51.302489031580826,170.0178737527003Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L51.302489031580826,170.0178737527003A80,80,0,0,1,29.03816903975914,151.80400723858165Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L29.03816903975914,151.80400723858165A80,80,0,0,1,19.039133345742258,136.93988905880272Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L19.039133345742258,136.93988905880272A80,80,0,0,1,12.856498398349785,121.18679212708219Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <path fill="#000000"
                                  stroke="none"
                                  d="M90,100L12.856498398349785,121.18679212708219A80,80,0,0,1,27.14378565729011,50.511654720496956Z"
                                  fill-opacity="0"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 0;"></path>
                            <circle cx="191"
                                    cy="110"
                                    r="5"
                                    fill="#59bb0c"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="110"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">28.8% - php</tspan>
                            </text>
                            <circle cx="191"
                                    cy="126.8"
                                    r="5"
                                    fill="#7edb35"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="126.8"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.003124999999997" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    12.3% - thinkphp
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="143.6"
                                    r="5"
                                    fill="#62e65d"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="143.6"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.006249999999994" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    6.5% - yii2
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="160.4"
                                    r="5"
                                    fill="#c4ed68"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="160.4"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.009375000000006" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    6.3% - rest
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="177.20000000000002"
                                    r="5"
                                    fill="#e2ff9e"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="177.20000000000002"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.012500000000017" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    6.2% - oauth2.0
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="194.00000000000003"
                                    r="5"
                                    fill="#f0f2dd"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="194.00000000000003"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.000000000000028" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    6.2% - 日志
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="210.80000000000004"
                                    r="5"
                                    fill="#d7f6a0"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="210.80000000000004"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.00312500000004" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">6.2%
                                                                                                                    -
                                                                                                                    api
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="227.60000000000005"
                                    r="5"
                                    fill="#5de6a9"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="227.60000000000005"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.006250000000051" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    5.8% - 邮件接收
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="244.40000000000006"
                                    r="5"
                                    fill="#a0d2f6"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="244.40000000000006"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.0093750000000625" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    3.6% - redis
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="261.20000000000005"
                                    r="5"
                                    fill="#80def0"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="261.20000000000005"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.0125000000000455" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    3.4% - hincrby
                                </tspan>
                            </text>
                            <circle cx="191"
                                    cy="278.00000000000006"
                                    r="5"
                                    fill="#666666"
                                    stroke="none"
                                    style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"
                                    transform="matrix(1,0,0,1,0,-91)"></circle>
                            <text x="206"
                                  y="278.00000000000006"
                                  text-anchor="start"
                                  font="12px Arial, sans-serif"
                                  stroke="none"
                                  fill="#000000"
                                  style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: start; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial, sans-serif;"
                                  transform="matrix(1,0,0,1,0,-91)">
                                <tspan dy="4.000000000000057" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                    14.7% - 其他
                                </tspan>
                            </text>
                        </svg>
                    </div>
                    <div class="joindate">
                        始于 3月20日
                    </div>
                </div>
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


