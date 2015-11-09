<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Question */

$this->title = '提问';
/*$this->params['breadcrumbs'][] = ['label' => 'Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;*/
?>

<div class="wrap publish">
    <div class="container">
        <h1 class="h4 mt20">提问题
            <input type="hidden" value="" id="draftId">
            <input type="hidden" value="0" name="site" id="siteId">
        </h1>

        <form id="question" method="POST" role="form">
            <?= $this->render(
                    '_form',
                    [
                            'model' => $model,
                    ]
            ) ?>

        </form>

    </div>
    <!-- /.container -->
</div><!-- /.wrap -->