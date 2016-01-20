<?php

use common\entities\CommentEntity;
use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\CommentEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'associate_id')->label(false)->hiddenInput() ?>

    <div class="panel panel-default question-view">
        <div class="panel-heading" role="tab" id="headingOne">
            <?= Html::a(
                '<span class="glyphicon glyphicon-new-window small"></span>',
                [
                    'question/view',
                    'id' => $question_data['id'],
                    'answer_id' => $answer_data['id'],
                    'comment_id' => $model['id'],
                ],
                [
                    'target' => '_blank',
                    'class'  => 'pull-right',
                ]
            ) ?>
            <h4 class="panel-title">
                <?= Html::a(
                    $question_data['subject'],
                    '#collapseOne',
                    [
                        'class'         => '',
                        'ria-expanded'  => 'true',
                        'aria-controls' => 'collapseOne',
                        'data'          => [
                            'toggle' => 'collapse',
                            'parent' => '#accordion',
                        ],
                    ]
                ) ?>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
                <div class="fmt">
                    <?= $answer_data['content'] ?>
                </div>
            </div>
        </div>
    </div>


    <?= $form->field($model, 'content')->label(false)->widget(
        UEditor::className(),
        [
            'style'        => 'comment_update',
            'associate_id' => $model->id,
        ]
    ); ?>

    <div class="form-group">
        <div class="checkbox pull-left">
            <?php if ($model->scenario != 'common_edit'): ?>
                <?= $form->field($model, 'is_anonymous')->checkbox(
                    [
                        'value'   => CommentEntity::STATUS_ANONYMOUS,
                        'uncheck' => CommentEntity::STATUS_UNANONYMOUS,
                    ]
                ) ?>
            <?php endif; ?>
        </div>

        <div class="pull-right">
            <?= Html::submitButton(
                $model->isNewRecord ? '提交评论' : '更新评论',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
