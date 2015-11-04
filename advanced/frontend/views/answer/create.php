<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\AnswerEntity */

$this->title = 'Create Answer Entity';
$this->params['breadcrumbs'][] = ['label' => 'Answer Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="answer-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
