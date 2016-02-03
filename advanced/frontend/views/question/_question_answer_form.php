<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/31
 * Time: 13:09
 */
use common\helpers\TemplateHelper;
use common\services\AnswerService;
use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\entities\AnswerEntity;


/* @var $answer_model AnswerEntity */
?>

<div id="answer-form-area">

    <?php if (Yii::$app->request->get('answer_id') && $question_data['count_answer'] > 1): ?>
        <div class="mt20">
            <?= Html::a(
                    sprintf(
                            '<strong>查看全部 %d 个回答</strong>',
                            $question_data['count_answer']

                    ),
                    [
                            'question/view',
                            'id' => $question_data['id'],
                    ]
            ); ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->user->isGuest): ?>
        <div class="widget-comments">
            <div class="widget-comments__form row">
                <div class="col-md-12">
                    请先 <?= TemplateHelper::showLoginAndRegisterBtn() ?> 后回答！
                </div>
            </div>
        </div>

    <?php elseif ($answer_id = AnswerService::getUserAnswerId($question_data['id'], Yii::$app->user->id)): ?>
        <?= $this->render(
                '_question_has_answered',
                [
                        'question_id' => $question_data['id'],
                        'answer_id'   => $answer_id,
                ]
        ); ?>
    <?php else: ?>
        <h4>撰写答案</h4>
        <?php $form = ActiveForm::begin(['id' => 'answer_form',]); ?>

        <?= $form->field($answer_model, 'content')->label(false)->widget(
                UEditor::className(),
                ['style' => 'answer', 'associate_id' => $question_data['id']]
        ); ?>


        <div class="form-group">
            <div class="checkbox pull-left">
                <?= $form->field($answer_model, 'is_anonymous')->checkbox(
                    [
                        'value'   => AnswerEntity::STATUS_ANONYMOUS,
                        'uncheck' => AnswerEntity::STATUS_UNANONYMOUS,
                    ]
                ) ?>
            </div>

            <div class="pull-right">
                <?= Html::submitButton(
                        '提交答案',
                        [
                                'class'               => 'btn btn-primary',
                                'id'                  => 'btn_ajax_answer',
                                'data-do-ajax-submit' => true,
                                'data-href'           => Url::to(
                                        ['answer/create', 'question_id' => $question_data['id']]
                                ),
                                'data-on-done'        => 'afterAnswerCreateSuccess',
                                'data-form-id'        => 'answer_form',
                        ]
                ) ?>
                <br>
            </div>
        </div>

        <?php
        //$this->registerJs("$('#btn_ajax_answer').click(ajaxHandle);", \yii\web\View::POS_READY);
        ?>

        <?php ActiveForm::end(); ?>

    <?php endif; ?>
</div>