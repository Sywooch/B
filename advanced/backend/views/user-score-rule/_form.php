<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\UserScoreRuleEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-score-rule-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_event_id')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'currency' => 'Currency', 'credit' => 'Credit', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'score')->textInput() ?>

    <?= $form->field($model, 'limit_interval')->dropDownList([ 'limitless' => 'Limitless', 'year' => 'Year', 'season' => 'Season', 'month' => 'Month', 'week' => 'Week', 'day' => 'Day', 'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'limit_times')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enable' => 'Enable', 'disable' => 'Disable', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
