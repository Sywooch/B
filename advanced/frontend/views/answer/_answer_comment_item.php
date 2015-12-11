<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/14
 * Time: 22:10
 */
use common\entities\AnswerCommentEntity;
use common\helpers\TemplateHelper;

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
                        · <a href="#"
                             data-answer-id="<?= $item['id'] ?>"
                             data-comment-at-username="<?= TemplateHelper::showUsername(
                                     $item['created_by'],
                                     false
                             ); ?>">
                            回复
                        </a>
                    <?php endif; ?>
                    <span class="pull-right commentTools hover-show-obj">
                            <a href="#911" class="ml10" title="举报">举报</a>
                </span>
                <?php endif; ?>
            </p>

            <div class="content fmt mb-10">
                <?= $item['content']; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>