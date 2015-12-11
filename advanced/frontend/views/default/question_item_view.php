<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/31
 * Time: 11:44
 */
use common\helpers\TemplateHelper;
use yii\helpers\Html;

?>
<?php foreach ($data as $item): ?>
    <section class="stream-list__item">
        <div class="qa-rank">
            <div class="votes hidden-xs">
                <?= $item['count_follow'] ?>
                <small>关注</small>
            </div>
            <div class="answers<?= $item['count_answer'] > 0 ? ' answered' : ''; ?>">
                <?= $item['count_answer'] ?>
                <small>回答</small>
            </div>
            <div class="views hidden-xs">
                <?= $item['count_views'] ?>
                <small>浏览</small>
            </div>
        </div>
        <div class="summary">
            <ul class="author list-inline">
                <li>
                    <?= TemplateHelper::showUsername($item['created_by']) ?>
                    <span class="split"></span>
                    <?= Html::a(
                            TemplateHelper::showHumanTime(
                                    $item['created_at']
                            ),
                            ['question/view', 'id' => $item['id']],
                            [
                                    'class' => 'askDate',

                            ]
                    ) ?>
                </li>
            </ul>
            <h2 class="title">
                <?= Html::a(
                        $item['subject'],
                        [
                                'question/view',
                                'id' => $item['id'],

                        ],
                        [
                                'target' => '_blank',
                        ]
                ) ?>
            </h2>
            <ul class="taglist--inline ib">
                <?= TemplateHelper::showTagLiLabelByName($item['tags']) ?>
            </ul>
        </div>
    </section>

<?php endforeach; ?>