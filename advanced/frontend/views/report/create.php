<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\ReportEntity */

$this->title = 'Create Report Entity';
$this->params['breadcrumbs'][] = ['label' => 'Report Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
