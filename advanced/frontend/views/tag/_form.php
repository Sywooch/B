<?php

use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Tag */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tag-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->label('简短介绍')->textarea(['rows' => 2]) ?>

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
        <?= Html::submitButton(
            $model->isNewRecord ? '创建' : '更新',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
