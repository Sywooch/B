<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\AnswerEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="answer-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'count_usefull')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'count_comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_by')->textInput() ?>

    <?= $form->field($model, 'modify_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'modify_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reproduce_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reproduce_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_anonymous')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_fold')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
