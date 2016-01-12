<?php

use common\entities\AnswerEntity;
use common\models\CacheQuestionModel;
use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\AnswerEntity */
/* @var $form yii\widgets\ActiveForm */
/** @var CacheQuestionModel $question_data */

?>

<div class="answer-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question_id')->label(false)->hiddenInput() ?>

    <div class="panel panel-default question-view">
        <div class="panel-heading" role="tab" id="headingOne">
            <?= Html::a(
                '<span class="glyphicon glyphicon-new-window small"></span>',
                [
                    'question/view',
                    'id' => $question_data['id'],
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
                        'class'         => 'collapsed',
                        'ria-expanded'  => 'false',
                        'aria-controls' => 'collapseOne',
                        'data'          => [
                            'toggle' => 'collapse',
                            'parent' => '#accordion',
                        ],
                    ]
                ) ?>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
                <div class="fmt">
                    <?= $question_data['content'] ?>
                </div>
            </div>
        </div>
    </div>


    <?= $form->field($model, 'content')->label(false)->widget(UEditor::className()); ?>

    <?php if ($model->scenario == 'common_edit'): ?>
        <div class="form-group">
            <?= $form->field($model, 'update_reason')->textInput(
                [
                    'placeholder'  => '请描述您要修改的原因',
                    'autocomplete' => 'off',
                    'required'     => 'required',
                ]
            )->label('修改原因') ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="checkbox pull-left">
            <?php if ($model->scenario != 'common_edit'): ?>
                <?= $form->field($model, 'is_anonymous')->checkbox(
                    [
                        'value'   => AnswerEntity::STATUS_ANONYMOUS,
                        'uncheck' => AnswerEntity::STATUS_UNANONYMOUS,
                    ]
                ) ?>
            <?php endif; ?>
        </div>

        <div class="pull-right">
            <?= Html::submitButton(
                $model->isNewRecord ? '提交回答' : '更新回答',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
