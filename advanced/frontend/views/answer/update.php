<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $answer_model common\entities\AnswerEntity */

$this->title = '更新回答: ' . ' ' . $question_data['subject'];
//$this->params['breadcrumbs'][] = [
//        'label' => $question_data['subject'],
//        'url'   => [
//                'question/view',
//                'id' => $question_data['id'],
//        ],
//];
//$this->params['breadcrumbs'][] = '更新回答';
?>
<div class="container">
    <h1 class="h4 mt20">修改答案</h1>

    <div class="answer-entity-update">
        <?= $this->render(
                '_form',
                [
                        'model' => $answer_model,
                ]
        ) ?>

    </div>
</div>
