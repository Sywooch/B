<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/12/6
 * Time: 1:08
 */
use common\helpers\TemplateHelper;
use common\entities\CommentEntity;
use common\widgets\UEditor\UEditor;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $answer_data */
/** @var $comment_form */
?>

<?php if (Yii::$app->user->isGuest): ?>

    <div class="widget-comments__form">
        <div class="col-md-12">
            请先 <?= TemplateHelper::showLoginAndRegisterBtn() ?> 后评论！
        </div>
    </div>

<?php else: ?>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'comment-form-' . $answer_data['id'],
        ]
    ); ?>

    <h4>评论</h4>
    <? /*= $form->field(
            $comment_form,
            'content'
    )->textarea(
            [
                    'id'    => 'comment-content-' . $answer_data['id'],
                    'class' => 'textarea-comment',
            ]
    )->label(false); */ ?>

    <?= $form->field($comment_form, 'content')->textarea(
        [
            'id'    => 'comment-content-' . $answer_data['id'],
            'class' => 'textarea-comment',
        ]
    )->label(false)->widget(
        UEditor::className(),
        [
            'no'           => $answer_data['id'],
            'style'        => 'comment',
            'associate_id' => $answer_data['question_id'],
        ]
    ); ?>

    <div class="form-group">
        <div class="pull-right">
            <?= Html::submitButton(
                '评论',
                [
                    'class'               => 'btn btn-primary',
                    'id'                  => 'btn_ajax_answer',
                    'data-do-ajax-submit' => true,
                    'data-href'           => Url::to(
                        ['comment/create', 'answer_id' => $answer_data['id']]
                    ),
                    'data-on-done'        => 'afterCommentCreateSuccess',
                    'data-form-id'        => 'comment-form-' . $answer_data['id'],
                    'data-id'             => $answer_data['id'],
                ]
            ) ?><br>
        </div>

        <div class="checkbox" class="pull-left" style="width:200px;">
            <?= $form->field(
                $comment_form,
                'is_anonymous'
            )->checkbox(
                [
                    'value'   => CommentEntity::STATUS_ANONYMOUS,
                    'uncheck' => CommentEntity::STATUS_UNANONYMOUS,
                ]
            ) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>