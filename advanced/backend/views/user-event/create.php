<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\UserEventEntity */

$this->title = 'Create User Event Entity';
$this->params['breadcrumbs'][] = ['label' => 'User Event Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-event-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
