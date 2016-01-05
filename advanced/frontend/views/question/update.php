<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Question */

$this->title = '编辑问题: ' . ' ' . $model->subject;
?>
<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>
        <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
