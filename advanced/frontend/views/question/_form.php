<?php

use common\entities\QuestionEntity;
use common\widgets\UEditor\UEditor;
use dosamigos\selectize\SelectizeTextInput;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Question */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="question-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if ($model->hasErrors()) {
        $errors = $model->getErrors();
        $error_html = [];
        foreach ($errors as $key => $error) {
            $error_html[] = sprintf('<h4>%s</h4>', $key);
            foreach ($error as $err) {
                $error_html[] = sprintf('<p>%s</p>', $err);
            }
        }

        echo sprintf(
            '<div class="bs-callout bs-callout-danger">%s</div>',
            implode('', $error_html)
        );
    }
    ?>


    <div class="form-group">
        <label for="title" class="sr-only">标题</label>
        <?= $form->field($model, 'subject')->textInput(
            [
                'placeholder'  => '标题：一句话说清问题，用问号结尾',
                'autocomplete' => 'off',
                'required'     => 'required',
            ]
        )->label(false) ?>
    </div>

    <?= SelectizeTextInput::widget(
        [
            'name'          => 'QuestionEntity[tags]',
            'value'         => $model->tags,
            'loadUrl'       => ['/tag/search'],
            'clientOptions' => [
                'placeholder'      => sprintf(
                    '标签：至少%d个，最多%d个',
                    $model->getMinTagsNumber(),
                    $model->getMaxTagsNumber()
                ),
                'allowEmptyOption' => false,
                'delimiter'        => ',',
                'valueField'       => 'name',
                'labelField'       => 'name',
                'searchField'      => 'name',
                'maxItems'         => $model->getMaxTagsNumber(),
                'plugins'          => ['remove_button'],
                'persist'          => false,
                'create'           => true,
            ],
        ]
    ) ?>

    <?= $form->field($model, 'content')->label(false)->widget(UEditor::className()); ?>


    <div class="form-group">
        <div class="checkbox pull-left">
            <?= $form->field($model, 'is_anonymous')->checkbox(
                ['value' => QuestionEntity::STATUS_ANONYMOUS]
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::submitButton($model->isNewRecord ? '发布问题' : '更新问题', ['class' => 'btn btn-primary btn-lg']) ?><br>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
</div>

<script>

</script>