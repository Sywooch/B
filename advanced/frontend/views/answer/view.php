<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\entities\AnswerEntity */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Answer Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="answer-entity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'question_id',
            'content:ntext',
            'count_usefull',
            'count_comment',
            'create_at',
            'create_by',
            'modify_at',
            'modify_by',
            'reproduce_url:url',
            'reproduce_username',
            'is_anonymous',
            'is_fold',
        ],
    ]) ?>

</div>
