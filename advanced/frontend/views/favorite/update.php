<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\FavoriteEntity */

$this->title = 'Update Favorite Entity: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Favorite Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="favorite-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
