<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/14
 * Time: 22:10
 */
use common\helpers\TemplateHelper;

?>
<? foreach ($data as $index => $item): ?>
    <div class="widget-comments__item hover-show">
        <div class="comment-content wordbreak">
            <p class="comment-meta">
                #&nbsp;
                <?= TemplateHelper::showUsername($item['create_by']) ?>
                <? if ($answer_create_user_id == $item['create_by']): ?>
                    [答主]
                <? elseif ($question_create_user_id == $item['create_by']): ?>
                    [提问者]
                <? endif; ?>
                · <span class="createdDate">
                        <?= TemplateHelper::showhumanTime(
                                $item['create_at']
                        ) ?></span>
                <? if ($item['create_by'] != Yii::$app->user->id): ?>
                    · <a href="#"
                         class="commentReply"
                         data-userid="1030000002644202"
                         data-id="1050000003912333"
                         data-username="<?= $item['create_by'] ?>">回复</a>
                    <span class="pull-right commentTools hover-show-obj">                                                <a
                            <a href="#911"
                               class="ml10"
                               data-toggle="modal"
                               data-target="#911"
                               data-type="comment"
                               data-id="1050000003912333"
                               data-typetext="评论"
                               data-placement="top"
                               title="举报">举报</a>
                </span>
                <? endif; ?>

            </p>

            <div class="content fmt mb-10">
                <?= $item['content']; ?>
            </div>
        </div>
    </div>
<? endforeach; ?>