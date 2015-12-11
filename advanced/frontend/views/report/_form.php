<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\ReportEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'associate_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'report_object')->dropDownList([ 'question' => 'Question', 'answer' => 'Answer', 'comment' => 'Comment', 'user' => 'User', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'report_reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'unprocessed' => 'Unprocessed', 'confirmed' => 'Confirmed', 'unconfirmed' => 'Unconfirmed', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
