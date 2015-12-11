<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Question */

$this->title = '提问';
/*$this->params['breadcrumbs'][] = ['label' => 'Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;*/
?>
<div class="container">
    <h1 class="h4 mt20">提问题</h1>

    <form id="question" method="POST" role="form">
        <?= $this->render(
                '_form',
                [
                        'model' => $model,
                ]
        ) ?>

    </form>

</div>