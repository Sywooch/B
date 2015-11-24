<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/8
 * Time: 13:17
 */
use common\helpers\TemplateHelper;
use common\widgets\UEditor\UEditor;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>
<?php \yii\widgets\Pjax::begin(
        [
                'id'              => 'comment-pajax',
                'linkSelector'    => '#comment-page a',
                'enablePushState' => false,
                'timeout'         => 10000,
                'clientOptions'   => [
                        'container' => 'pjax-container-comment',
                ],
                'options'         => [
                        'id' => 'comment_item_area_' . $answer_data['id'],
                ],
        ]
); ?>
<?= $comment_item_html ?>

<?= $pages ? LinkPager::widget(
        [
                'pagination' => $pages,
                'options'    => [
                        'id'    => 'comment-page',
                        'class' => 'pagination',
                ],
        ]
) : ''; ?>

<?php \yii\widgets\Pjax::end(); ?>



<?php if (Yii::$app->user->isGuest): ?>

    <div class="widget-comments__form row">
        <div class="col-md-12">
            请先 <?= TemplateHelper::showLoginAndRegisterBtn() ?> 后评论！
        </div>
    </div>

<?php else: ?>
    <?php $form = ActiveForm::begin(
            [
                    'id' => 'comment-form-'. $answer_data['id'],
            ]
    ); ?>

    <h4>评论</h4>
    <?= $form->field(
            $comment_form,
            'content'
    )->textarea(
            [
                    'id'    => 'comment-content-' . $answer_data['id'],
                    'class' => 'textarea-comment',
            ]
    )->label(false); ?>


    <div class="form-group">
        <div class="checkbox pull-left">
            <?= $form->field(
                    $comment_form,
                    'is_anonymous'
            )->checkbox() ?>
        </div>

        <div class="pull-right">
            <?= Html::submitButton(
                    '评论',
                    [
                            'class'        => 'btn btn-primary',
                            'id'           => 'btn_ajax_answer',
                            'data-href'    => Url::to(
                                    ['answer-comment/create', 'answer_id' => $answer_data['id']]
                            ),
                            'data-on-done' => 'afterCommentCreateSuccess',
                            'data-form-id' => 'comment-form-'.$answer_data['id'],
                            'data-id'      => $answer_data['id'],
                    ]
            ) ?><br>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
<?php endif; ?>

