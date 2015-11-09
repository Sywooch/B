<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $answer_model common\entities\AnswerEntity */

$this->title = '更新回答: ' . ' ' . $question_model->subject;
$this->params['breadcrumbs'][] = ['label' => $question_model->subject, 'url' => [
        'question/view', 'id' => $question_model->id]
];
$this->params['breadcrumbs'][] = '更新回答';
?>
<div class="answer-entity-update">
    <?= $this->render(
            '_form',
            [
                    'model' => $answer_model,
            ]
    ) ?>

</div>
