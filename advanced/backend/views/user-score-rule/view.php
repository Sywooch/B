<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\entities\UserScoreRuleEntity */

$this->title = $model->user_event_id;
$this->params['breadcrumbs'][] = ['label' => 'User Score Rule Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-score-rule-entity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'user_event_id' => $model->user_event_id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'user_event_id' => $model->user_event_id, 'type' => $model->type], [
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
            'user_event_id',
            'type',
            'score',
            'limit_interval',
            'limit_times:datetime',
            'status',
        ],
    ]) ?>

</div>
