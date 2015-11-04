<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/31
 * Time: 13:09
 */
use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\entities\AnswerEntity;

/* @var $answer_model AnswerEntity */
?>

<div id="answer_form_area">
    <?php if (Yii::$app->user->isGuest): ?>
        <div class="widget-comments">
            <div class="widget-comments__form row">
                <div class="col-md-12">
                    请先 <?= \common\helpers\TemplateHelper::showLoginAndRegisterBtn() ?> 后回答！
                </div>
            </div>
        </div>

    <?php elseif ($answer_id = $answer_model->checkWhetherHasAnswered(
            $question_model->id,
            Yii::$app->user->id
    )
    ):
        echo $this->render(
                '_question_has_answered',
                [
                        'question_id' => $question_model->id,
                        'answer_id'  => $answer_id,
                ]
        );
        ?>

    <?php else: ?>
        <h4>撰写答案</h4>
        <?php $form = ActiveForm::begin(
                [
                        'id' => 'answer_form',
                ]
        ); ?>

        <?= $form->field($answer_model, 'content')->label(false)->widget(
                UEditor::className(),
                ['style' => 'answer']
        ); ?>


        <div class="form-group">
            <div class="checkbox pull-left">
                <?= $form->field($answer_model, 'is_anonymous')->checkbox() ?>
            </div>

            <div class="pull-right">
                <?= Html::submitButton(
                        '提交答案',
                        [
                                'class'        => 'btn btn-primary',
                                'id'           => 'btn_ajax_answer',
                                'data-href'    => Url::to(['answer/create', 'question_id' => $question_model->id]),
                                'data-on-done' => 'afterAnswerCreateSuccess',
                                'data-form-id' => 'answer_form',
                        ]
                ) ?><br>
            </div>
        </div>

        <?php
        $this->registerJs("$('#btn_ajax_answer').click(handleAjaxLink);", \yii\web\View::POS_READY);
        ?>

        <?php ActiveForm::end(); ?>

    <?php endif; ?>
</div>