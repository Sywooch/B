<?php

use kartik\tabs\TabsX;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel common\models\QuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '最新问题';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-9 main">
            <p class="main-title hidden-xs mt10">
                今天，你在深圳遇到了什么问题呢？
                <a id="goAsk" href="<?= Url::to(['question/create']) ?>" class="btn btn-primary">我要提问</a>
            </p>

            <?php
            $items = [
                [
                    'label'   => '<i class="glyphicon glyphicon-home"></i> 最新的',
                    'content' => $active == 'latest' ? $question_data : '',
                    'active'  => $active == 'latest' ? true : false,
                    'url'     => Url::to(['question/latest']),
                ],
                [
                    'label'   => '<i class="glyphicon glyphicon-user"></i> 热门的',
                    'content' => $active == 'hottest' ? $question_data : '',
                    'active'  => $active == 'hottest' ? true : false,
                    'url'     => Url::to(['question/hottest']),
                ],
                [
                    'label'   => '<i class="glyphicon glyphicon-user"></i> 未回答',
                    'content' => $active == 'un-answer' ? $question_data : '',
                    'active'  => $active == 'un-answer' ? true : false,
                    'url'     => Url::to(['question/un-answer']),
                ],

            ];
            // Ajax Tabs Above
            echo Tabs::widget(
                [
                    'items'        => $items,
                    'encodeLabels' => false,
                ]
            );
            ?>

            <!-- /.stream-list -->

            <div class="text-left">
                <?= $pages ? LinkPager::widget(['pagination' => $pages]) : ''; ?>
            </div>
        </div>
        <!-- /.main -->
        <div class="col-xs-12 col-md-3 side mt30">
            <?= $this->render('/default/_right') ?>
        </div>
        <!-- /.side -->
    </div>
</div>
