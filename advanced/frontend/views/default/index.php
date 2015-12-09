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
                            'label'   => '<i class="glyphicon glyphicon-home"></i> 最新的',
                            'content' => $question_latest,
                            'active'  => true,
                        //'linkOptions' => ['data-url' => Url::to(['/site/fetch-tab?tab=1'])],
                    ],
                    [
                            'label'       => '<i class="glyphicon glyphicon-user"></i> 热门的',
                            'content'     => '$content2',
                            'linkOptions' => ['data-url' => Url::to(['/default/fetch-hottest'])],
                    ],
                    [
                            'label'       => '<i class="glyphicon glyphicon-user"></i> 未回答',
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
            <aside class="widget-welcome">
                <h2 class="h4 title">深圳本地社区</h2>

                <p>最前沿的技术问答，最纯粹的技术切磋。让你不知不觉中开拓眼界，提高技能，认识更多朋友。</p>
            </aside>

            <?php if ($follow_tags): ?>
                <div class="widget-box">
                    <h2 class="h4 widget-box__title">关注 <?= Html::a($follow_tag_count, ['user']) ?> 个标签</h2>
                    <ul class="taglist--inline multi">
                        <?php foreach ($follow_tags as $follow_tag) : ?>
                            <li class="tagPopup">
                                <?= Html::a(
                                        $follow_tag['name'],
                                        ['tag/view', 'id' => $follow_tag['id']],
                                        [
                                                'class' => 'tag',
                                        ]
                                ); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($tags): ?>
                <div class="widget-box">
                    <h2 class="h4 widget-box__title">热门标签 <a href="/tags" title="更多">&raquo;</a></h2>
                    <ul class="taglist--inline multi">
                        <?php foreach ($tags as $tag): ?>
                            <li class="tagPopup">
                                <?= Html::a(
                                        $tag['name'],
                                        ['tag/view', 'id' => $tag['id']],
                                        [
                                                'class' => 'tag',
                                        ]
                                ); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($question_hottest): ?>
                <div class="widget-box">
                    <h2 class="h4 widget-box__title">最近热门的</h2>
                    <ul class="widget-links list-unstyled">
                        <?php foreach ($question_hottest as $question): ?>
                            <li class="widget-links__item">
                                <?= Html::a(
                                        $question['subject'],
                                        ['question/view', 'id' => $question['id']]
                                ) ?>
                                <small class="text-muted">
                                    <?= $question['count_answer']; ?> 回答
                                </small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.side -->
    </div>
</div>

