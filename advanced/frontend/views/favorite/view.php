<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\entities\FavoriteEntity */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Favorite Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="favorite-entity-view">

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
            'favorite_category_id',
            'type',
            'associate_id',
            'created_at',
            'created_by',
            'note',
        ],
    ]) ?>

</div>
