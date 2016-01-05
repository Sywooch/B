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
                        <li><?= Html::a('修改', ['tag/update', 'id' => $tag['id']]) ?></li>
                        <li><?= Html::a('记录', ['tag/version']) ?></li>
                    </ul>
                </div>
            </section>

            <ul class="nav nav-tabs nav-tabs-zen mb20">
                <li class="active"><?= Html::a('问答', ['tag/view', 'id' => $tag['id']]) ?></li>
            </ul>
            <div class="tab-content">
                <div id="qa" class="stream-list question-stream">
                    <?php foreach ($questions as $item): ?>
                        <section class="stream-list__item">
                            <div class="qa-rank">
                                <div class="votes hidden-xs">
                                    <?= $item['count_follow'] ?>
                                    <small>关注</small>
                                </div>
                                <div class="answers<?= $item['count_answer'] > 0 ? ' answered' : ''; ?>">
                                    <?= $item['count_answer'] ?>
                                    <small>回答</small>
                                </div>
                                <div class="views hidden-xs">
                                    <?= $item['count_views'] ?>
                                    <small>浏览</small>
                                </div>
                            </div>
                            <div class="summary">
                                <ul class="author list-inline">
                                    <? if ($item['count_favorite'] > 0): ?>
                                        <li class="pull-right" title="<?= $item['count_favorite'] ?> 收藏">
                                            <small class="glyphicon glyphicon-bookmark"></small>
                                            <?= $item['count_favorite'] ?>
                                        </li>
                                    <? endif; ?>
                                    <li>
                                        <?= TemplateHelper::showUsername($item['created_by'], true) ?>
                                        <span class="split"></span>
                                        <?= Html::a(
                                            TemplateHelper::showHumanTime(
                                                $item['created_at']
                                            ),
                                            ['question/view', 'id' => $item['id']],
                                            [
                                                'class' => 'askDate',

                                            ]
                                        ) ?>
                                    </li>
                                </ul>
                                <h2 class="title">
                                    <?= Html::a(
                                        $item['subject'],
                                        ['question/view', 'id' => $item['id']],
                                        [
                                            'target' => '_blank',
                                        ]
                                    ); ?>
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

            <?php if ($tag_who_good_at_in_30_days): ?>
                <div class="widget-box widget-taguser">
                    <h2 class="h4 widget-box__title">本月新人榜</h2>
                    <ol class="widget-top10">
                        <?php foreach ($tag_who_good_at_in_30_days as $user_id => $count_follow): ?>
                            <li>
                                <?= TemplateHelper::showUserAvatar($user_id, 24, false) ?>
                                <?= TemplateHelper::showUsername($user_id) ?>
                                <span class="text-muted pull-right">+<?= $count_follow ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>

            <?php if ($tag_who_good_at_in_365_days): ?>
                <div class="widget-box widget-taguser">
                    <h2 class="h4 widget-box__title">标签名人榜</h2>
                    <ol class="widget-top10">
                        <?php foreach ($tag_who_good_at_in_365_days as $user_id => $count_follow): ?>
                            <li>
                                <?= TemplateHelper::showUserAvatar($user_id, 24, false) ?>
                                <?= TemplateHelper::showUsername($user_id) ?>
                                <span class="text-muted pull-right">+<?= $count_follow ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.side -->
    </div>
</div>