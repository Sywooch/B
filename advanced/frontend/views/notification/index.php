<?php
/* @var $this yii\web\View */
use common\entities\NotificationEntity;
use common\helpers\TemplateHelper;
use yii\widgets\LinkPager;

?>
<div class="wrap">
    <div class="container pt30">
        <div class="row">
            <div class="col-xs-12 col-md-9 main">
                <h2 class="h4 mt0 mb20">
                    消息通知
                    <button type="button" class="btn btn-default btn-xs ingore-all ml10">全部标记为已读</button>
                </h2>
                <div class="stream-list notify-stream border-top">

                    <? foreach ($data as $key => $item): ?>
                        <h3 class="time"><?= TemplateHelper::showHumanTime(strtotime($key)) ?></h3>

                        <? foreach ($item as $section): ?>
                            <section class="stream-list__item<?= $section['status'] == NotificationEntity::STATUS_READ ? ' viewed' : '' ?> "><?= $section['template'] ?></section>
                        <? endforeach; ?>
                    <? endforeach; ?>

                </div>
                <!-- /.stream-list -->

                <div class="text-left">
                    <?php \yii\widgets\Pjax::begin(
                            [
                                    'timeout'       => 10000,
                                    'clientOptions' => [
                                            'container' => 'pjax-container',
                                    ],
                            ]
                    ); ?>

                    <?= $pages ? LinkPager::widget(
                            [
                                    'pagination'  => $pages,
                                    'options'     => [
                                            'id'    => 'answer-page',
                                            'class' => 'pagination',

                                    ],
                                    'linkOptions' => [
                                            'rel' => 'nofollow',
                                    ],
                            ]
                    ) : ''; ?>
                    <?php \yii\widgets\Pjax::end(); ?>
                </div>
            </div>
            <!-- /.main -->

            <div class="col-xs-12 col-md-3 side">
                <div class="widget-messages">
                    <a id="followingQuestionCount" class="widget-messages__item" href="/user/questions">
                        我的问题
                    </a>
                    <a id="noteCount" class="widget-messages__item" href="/user/note">我的笔记</a>
                    <a id="draftCount" class="widget-messages__item" href="/user/draft">我的草稿</a>
                    <a class="widget-messages__item" href="/user/bookmarks">我的收藏</a>
                    <a id="inviteCount" class="widget-messages__item" href="/user/invited">
                        邀请我回答的
                    </a>
                    <a class="widget-messages__item" href="/user/invitation">邀请朋友加入</a>
                </div>

            </div>
            <!-- /.side -->
        </div>
    </div>
</div>