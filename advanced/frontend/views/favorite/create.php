<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\FavoriteEntity */

$this->title = 'Create Favorite Entity';
$this->params['breadcrumbs'][] = ['label' => 'Favorite Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="favorite-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
