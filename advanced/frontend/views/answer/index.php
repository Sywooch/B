<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Answer Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="answer-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Answer Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'question_id',
            'content:ntext',
            'count_usefull',
            'count_comment',
            // 'create_at',
            // 'create_by',
            // 'modify_at',
            // 'modify_by',
            // 'reproduce_url:url',
            // 'reproduce_username',
            // 'is_anonymous',
            // 'is_fold',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
