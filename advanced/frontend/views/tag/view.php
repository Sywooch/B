<?php

use yii\helpers\Html;
use common\helpers\TemplateHelper;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $tag \common\entities\TagEntity */

$this->title = $tag['name'];
?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-9 main">

            <section class="tag-info mt30">
                <?= $tag['icon'] ? Html::img(
                    $tag['icon'],
                    [
                        'class' => 'pull-left avatar-32 mr10',
                    ]
                ) : ''; ?>
                <h1 class="h3"><?= $tag['name'] ?></h1>
                <ul class="list-inline">
                </ul>

                <div class="mb20">
                    <p><?= $tag['description'] ?></p>
                    <ul class="list-inline">
                        <li><?= Html::a('修改', ['tag/edit']) ?></li>
                        <li><?= Html::a('记录', ['tag/version']) ?></li>
                    </ul>
                </div>
            </section>

            <ul class="nav nav-tabs nav-tabs-zen mb20">
                <li class="active"><?= Html::a('问答', ['question/index']) ?></li>
            </ul>
            <div class="tab-content">
                <div id="qa" class="stream-list question-stream">
                    <?php foreach ($questions as $item): ?>
                        <section class="stream-list__item">
                            <div class="qa-rank">
                                <div class="answers answered">
                                    9
                                    <small>回答</small>
                                </div>
                                <div class="views hidden-xs">
                                    540
                                    <small>浏览</small>
                                </div>
                            </div>
                            <div class="summary">
                                <ul class="author list-inline">
                                    <li class="pull-right" title="1 收藏">
                                        <small class="glyphicon glyphicon-bookmark"></small>
                                        1
                                    </li>
                                    <li>
                                        <a href="/u/f2e">52lidan</a>
                                        <span class="split"></span>
                                        <a href="/q/1010000003981953/a-1020000003989542">11月13日回答</a>
                                    </li>
                                </ul>
                                <h2 class="title">
                                    <?= Html::a($item['subject'], ['question/view', 'id' => $item['id']]); ?>
                                </h2>
                                <ul class="taglist--inline ib">
                                    <?= TemplateHelper::showTagLiLabelByName($item['tags']) ?>

                                </ul>
                            </div>
                        </section>

                    <?php endforeach; ?>

                </div>

                <div class="text-center">
                    <?= $pages ? LinkPager::widget(
                        [
                            'pagination'  => $pages,
                            'options'     => [
                                'id'    => 'tag-question-page',
                                'class' => 'pagination',

                            ],
                            'linkOptions' => [
                                //'rel' => 'nofollow',
                            ],
                        ]
                    ) : ''; ?>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.main -->

        <div class="col-xs-12 col-md-3 side">

            <ul class="widget-action--ver list-unstyled mt30">
                <li>
                    <!--问题关注-->
                    <?= $this->render(
                        '_tag_follow',
                        [
                            'id'           => $tag['id'],
                            'count_follow' => $tag['count_follow'],
                            'is_followed'  => $is_followed,
                        ]
                    ) ?>
                </li>
            </ul>

            <?php if ($tag_relation): ?>
                <div class="widget-box">
                    <?php if (isset($tag_relation['relate'])): ?>
                        <h2 class="h4 widget-box__title">相关标签</h2>
                        <ul class="taglist--inline multi">
                            <?php foreach ($tag_relation['relate'] as $tag): ?>
                                <li class="tagPopup">
                                    <?= Html::a($tag['name'], ['tag/view', 'id' => $tag['id']], ['class' => 'tag']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="widget-box widget-taguser">
                <h2 class="h4 widget-box__title">本月新人榜</h2>
                <ol class="widget-top10">
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/157/842/1578424531-1030000000455562_small"
                             class="avatar-24">
                        <a href="/u/mcfog" class="ellipsis">mcfog</a>
                        <span class="text-muted pull-right">+45</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/371/448/3714482234-1030000000203625_small"
                             class="avatar-24">
                        <a href="/u/douglarek" class="ellipsis">douglarek</a>
                        <span class="text-muted pull-right">+17</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/343/761/3437616413-565e77bda71f1_small"
                             class="avatar-24">
                        <a href="/u/houshuu" class="ellipsis">方舟</a>
                        <span class="text-muted pull-right">+16</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/344/431/3444319505-54b4d16166774_small"
                             class="avatar-24">
                        <a href="/u/shibar" class="ellipsis">JingDing</a>
                        <span class="text-muted pull-right">+8</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/184/012/1840129184-1030000000256515_small"
                             class="avatar-24">
                        <a href="/u/zaidisu" class="ellipsis">在低诉</a>
                        <span class="text-muted pull-right">+7</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/266/538/2665388308-1030000000323597_small"
                             class="avatar-24">
                        <a href="/u/weakish" class="ellipsis">weakish</a>
                        <span class="text-muted pull-right">+3</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/893/304/893304847-5508f4f9a6679_small"
                             class="avatar-24">
                        <a href="/u/util" class="ellipsis">util</a>
                        <span class="text-muted pull-right">+2</span>
                    </li>
                    <li>
                        <img src="http://static.segmentfault.com/v-56666dcd/global/img/user-32.png" class="avatar-24">
                        <a href="/u/jiayi797" class="ellipsis">jiayi797</a>
                        <span class="text-muted pull-right">+1</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/371/782/371782932-5620bef587fde_small"
                             class="avatar-24">
                        <a href="/u/kphcdr" class="ellipsis">kphcdr</a>
                        <span class="text-muted pull-right">+1</span>
                    </li>
                    <li>
                        <img src="http://static.segmentfault.com/v-56666dcd/global/img/user-32.png" class="avatar-24">
                        <a href="/u/web_cc" class="ellipsis">前端小c</a>
                        <span class="text-muted pull-right">+1</span>
                    </li>
                </ol>
            </div>

            <div class="widget-box widget-taguser">
                <h2 class="h4 widget-box__title">标签名人榜</h2>
                <ol class="widget-top10">
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/812/504/812504259-54509cf40aa63_small"
                             class="avatar-24">
                        <a href="/u/justjavac" class="ellipsis">justjavac</a>
                        <span class="text-muted pull-right">+1883</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/263/018/2630188223-1030000000125916_small"
                             class="avatar-24">
                        <a href="/u/nightire" class="ellipsis">nightire</a>
                        <span class="text-muted pull-right">+1057</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/190/789/1907890660-1030000000256749_small"
                             class="avatar-24">
                        <a href="/u/yinchuan" class="ellipsis">尹川</a>
                        <span class="text-muted pull-right">+756</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/157/842/1578424531-1030000000455562_small"
                             class="avatar-24">
                        <a href="/u/mcfog" class="ellipsis">mcfog</a>
                        <span class="text-muted pull-right">+647</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/261/898/2618981758-1030000000091293_small"
                             class="avatar-24">
                        <a href="/u/joyqi" class="ellipsis">joyqi</a>
                        <span class="text-muted pull-right">+643</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/266/538/2665388308-1030000000323597_small"
                             class="avatar-24">
                        <a href="/u/weakish" class="ellipsis">weakish</a>
                        <span class="text-muted pull-right">+625</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/373/745/3737454955-5493d7d24ab19_small"
                             class="avatar-24">
                        <a href="/u/sunny" class="ellipsis">高阳Sunny</a>
                        <span class="text-muted pull-right">+606</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/270/943/2709439599-1030000000321731_small"
                             class="avatar-24">
                        <a href="/u/shamiao" class="ellipsis">沙渺</a>
                        <span class="text-muted pull-right">+531</span>
                    </li>
                    <li>
                        <img src="http://sfault-avatar.b0.upaiyun.com/365/581/3655811061-1030000000094193_small"
                             class="avatar-24">
                        <a href="/u/jysperm" class="ellipsis">王子亭</a>
                        <span class="text-muted pull-right">+517</span>
                    </li>
                    <li>
                        <img src="http://static.segmentfault.com/v-56666dcd/global/img/user-32.png" class="avatar-24">
                        <a href="/u/syeerzy" class="ellipsis">Syeerzy</a>
                        <span class="text-muted pull-right">+516</span>
                    </li>
                </ol>
            </div>

        </div>
        <!-- /.side -->
    </div>
</div>