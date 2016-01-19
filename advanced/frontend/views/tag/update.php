<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Tag */

$this->title = '完善标签 ';
?>
<div class="container">
    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>
</div>
