<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\AnswerCommentEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="answer-comment-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'answer_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'create_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_by')->textInput() ?>

    <?= $form->field($model, 'modify_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'modify_by')->textInput() ?>

    <?= $form->field($model, 'is_anonymous')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enable' => 'Enable', 'disable' => 'Disable', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
