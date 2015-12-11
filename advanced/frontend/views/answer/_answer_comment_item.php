<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/14
 * Time: 22:10
 */
use common\entities\AnswerCommentEntity;
use common\helpers\TemplateHelper;
use yii\helpers\Html;

?>
<?php foreach ($data as $index => $item) : ?>
    <div class="widget-comments__item hover-show">
        <div class="comment-content wordbreak">
            <p class="comment-meta">
                #&nbsp;
                <?= TemplateHelper::showUsername(
                        $item['created_by'],
                        true,
                        $item['is_anonymous'] == AnswerCommentEntity::STATUS_ANONYMOUS
                ) ?>
                <?php if ($item['is_anonymous'] != AnswerCommentEntity::STATUS_ANONYMOUS): ?>
                    <?php if ($answer_create_user_id == $item['created_by']) : ?>
                        [答主]
                    <?php elseif ($question_create_user_id == $item['created_by']) : ?>
                        [题主]
                    <?php elseif (!Yii::$app->user->isGuest && $item['created_by'] == Yii::$app->user->id) : ?>
                        [我]
                    <?php endif; ?>
                <?php endif; ?>
                · <span class="createdDate">
                        <?= TemplateHelper::showHumanTime($item['created_at']); ?></span>
                <?php if ($item['is_anonymous'] == AnswerCommentEntity::STATUS_ANONYMOUS): ?>
                    匿名评论
                <?php else: ?>
                    评论
                <?php endif; ?>


                <?php if ($item['created_by'] != Yii::$app->user->id) : ?>
                    <?php if ($item['is_anonymous'] != AnswerCommentEntity::STATUS_ANONYMOUS): ?>
                        · <?= Html::a(
                                '回复',
                                'javascritp:;',
                                [
                                        'title'           => '回复Ta',
                                        'data-do-comment' => true,
                                        'data-answer-id'       => $item['answer_id'],
                                        'data-username'   => TemplateHelper::showUsername(
                                                $item['created_by'],
                                                false
                                        ),
                                ]
                        ) ?>
                    <?php endif; ?>
                    <span class="pull-right commentTools hover-show-obj">
                        <?= Html::a(
                                '举报',
                                'javascritp:;',
                                [
                                        'title'             => '举报',
                                        'data-do-report'    => true,
                                        'data-object'       => 'comment',
                                        'data-associate_id' => $item['id'],
                                ]
                        ) ?>
                </span>
                <?php endif; ?>
            </p>

            <div class="content fmt mb-10">
                <?= $item['content']; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>