<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\CommentEntity */

$this->title = 'Update Answer Comment Entity: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Answer Comment Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container">
    <h1 class="h4 mt20">修改评论</h1>

    <?= $this->render(
        '_form',
        [
            'model'         => $model,
            'question_data' => $question_data,
            'answer_data'   => $answer_data,
        ]
    ) ?>

</div>
