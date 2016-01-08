<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\UserScoreRuleEntity */

$this->title = 'Create User Score Rule Entity';
$this->params['breadcrumbs'][] = ['label' => 'User Score Rule Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-score-rule-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
