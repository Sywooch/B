<?php

use common\entities\AnswerEntity;
use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\AnswerEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="answer-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question_id')->label(false)->hiddenInput() ?>

    <?= $form->field($model, 'content')->label(false)->widget(UEditor::className()); ?>

    <div class="form-group">
        <div class="checkbox pull-left">
            <?= $form->field($model, 'is_anonymous')->checkbox(
                    ['value' => AnswerEntity::STATUS_ANONYMOUS]
            ) ?>
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
