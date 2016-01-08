<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\UserScoreRuleEntity */

$this->title = 'Update User Score Rule Entity: ' . ' ' . $model->user_event_id;
$this->params['breadcrumbs'][] = ['label' => 'User Score Rule Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_event_id, 'url' => ['view', 'user_event_id' => $model->user_event_id, 'type' => $model->type]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-score-rule-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
