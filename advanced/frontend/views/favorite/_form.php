<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\FavoriteEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="favorite-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'favorite_category_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList([ 'question' => 'Question', 'article' => 'Article', 'answer' => 'Answer', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'associate_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
