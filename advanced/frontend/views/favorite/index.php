<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Favorite Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="favorite-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Favorite Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'favorite_category_id',
            'type',
            'associate_id',
            'created_at',
            // 'created_by',
            // 'note',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
