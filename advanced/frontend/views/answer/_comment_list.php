<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/8
 * Time: 13:17
 */
use common\helpers\TemplateHelper;
use common\widgets\UEditor\UEditor;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>


<? foreach ($comments_data as $index => $item): ?>
    <div class="widget-comments__item hover-show" id="1050000003912333">
        <div class="votes widget-vote">
            <button class="like "
                    data-id="1050000003912333"
                    type="button"
                    data-do="like"
                    data-type="comment"></button>
            <span class="count">&nbsp;</span></div>
        <div class="comment-content wordbreak">
            <div class="content fmt">
                <?= $item['content']; ?>
            </div>

            <p class="comment-meta">
                <a href="/c/1050000003912333"
                   class="text-muted">#<?= $index ?></a>&nbsp;
                <?= TemplateHelper::showUsername($item['create_by']) ?>· <span class="createdDate">
                        <?= TemplateHelper::showhumanTime(
                                $item['create_at']
                        ) ?></span> ·
                <a href="#"
                   class="commentReply"
                   data-userid="1030000002644202"
                   data-id="1050000003912333"
                   data-username="青龙道人">回复</a>
                <span class="pull-right commentTools hover-show-obj">                                                <a
                            href="#911"
                            class="ml10"
                            data-toggle="modal"
                            data-target="#911"
                            data-type="comment"
                            data-id="1050000003912333"
                            data-typetext="评论"
                            data-placement="top"
                            title="举报">举报</a>            </span></p></div>
    </div>
<? endforeach; ?>

<?php if (Yii::$app->user->isGuest): ?>

    <div class="widget-comments__form row">
        <div class="col-md-12">
            请先 <?= \common\helpers\TemplateHelper::showLoginAndRegisterBtn() ?> 后评论！
        </div>
    </div>

<?php else: ?>
    <?php $form = ActiveForm::begin(
            [
                    'id' => 'comment_form',
            ]
    ); ?>


    <?= $form->field($comment_form, 'content', [
        //'class' => 'form-control',
    ])->label(false)->widget(
            UEditor::className(),
            ['style' => 'comment']
    ); ?>


    <div class="form-group">
        <div class="checkbox pull-left">
            <?= $form->field($comment_form, 'is_anonymous')->checkbox() ?>
        </div>

        <div class="pull-right">
            <?= Html::submitButton(
                    '评论',
                    [
                            'class'        => 'btn btn-primary',
                            'id'           => 'btn_ajax_answer',
                            'data-href'    => Url::to(
                                    ['answer-comment/create', 'answer_id' => $answer_model->id]
                            ),
                            'data-on-done' => 'afterCommentCreateSuccess',
                            'data-form-id' => 'comment_form',
                    ]
            ) ?><br>
        </div>
    </div>

    <?php
    //$this->registerJs("$('#btn_ajax_answer').click(ajaxHandle);", \yii\web\View::POS_READY);
    ?>

    <?php ActiveForm::end(); ?>

<?php endif; ?>

