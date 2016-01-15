<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/9/27
 * Time: 21:52
 * Version:
 * Created by PhpStorm.
 */
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\tabs\TabsX;


?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-9 main">
            <p class="main-title hidden-xs mt10">
                今天，你在深圳遇到了什么问题呢？
                <?= Html::a(
                    '我要提问',
                    ['/question/create'],
                    [
                        'id'              => 'goAsk',
                        'class'           => 'btn btn-primary',
                        'data-need-login' => true,
                    ]
                ) ?>
            </p>

            <?php
            $items = [
                [
                    'label' => '<i class="glyphicon glyphicon-time"></i> 最新的',
                    'content' => $question_latest,
                    'active'  => true,
                    //'linkOptions' => ['data-url' => Url::to(['/site/fetch-tab?tab=1'])],
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-fire"></i> 热门的',
                    'content'     => '$content2',
                    'linkOptions' => ['data-url' => Url::to(['/default/fetch-hottest'])],
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-magnet"></i> 未回答',
                    'content'     => '$content2',
                    'linkOptions' => ['data-url' => Url::to(['/default/fetch-un-answer'])],
                ],

            ];
            // Ajax Tabs Above
            echo TabsX::widget(
                [
                    'items'        => $items,
                    'position'     => TabsX::POS_ABOVE,
                    'encodeLabels' => false,
                    'options'      => [

                    ],
                ]
            );
            ?>

            <!-- /.stream-list -->

            <div class="text-center">
                <ul class="pager">
                    <li>以上是部分更新，你还可以查看</li>
                    <li><?= Html::a('全部问题', ['question/latest']) ?></li>
                    <li>或者</li>
                    <li><?= Html::a('热门问题', ['question/hottest']) ?></li>
                    <li>列表</li>
                </ul>
            </div>
        </div>
        <!-- /.main -->
        <div class="col-xs-12 col-md-3 side mt30">
            <?= $this->render('_right') ?>
        </div>
        <!-- /.side -->
    </div>
</div>

