<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/8
 * Time: 13:17
 */

use yii\widgets\LinkPager;

?>
<?php \yii\widgets\Pjax::begin(
    [
        'id'              => 'comment-pajax-' . $answer_data['id'],
        'linkSelector'    => sprintf('#comment-page-%d a', $answer_data['id']),
        'enablePushState' => false,
        'timeout'         => 10000,
    ]
); ?>
<?= $comment_item_html ?>

<?= $pages ? LinkPager::widget(
    [
        'pagination' => $pages,
        'options'    => [
            'id'    => 'comment-page-' . $answer_data['id'],
            'class' => 'pagination',
        ],
    ]
) : ''; ?>

<?php \yii\widgets\Pjax::end(); ?>

<!--评论表单-->
<?= $this->render('_answer_comment_form', ['answer_data' => $answer_data, 'comment_form' => $comment_form,]) ?>

