<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\ReportEntity */

$this->title = 'Update Report Entity: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Report Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="report-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
