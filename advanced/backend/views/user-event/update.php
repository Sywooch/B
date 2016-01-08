<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\UserEventEntity */

$this->title = 'Update User Event Entity: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User Event Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-event-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
