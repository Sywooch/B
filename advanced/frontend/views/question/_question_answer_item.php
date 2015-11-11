<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/31
 * Time: 13:09
 */
use common\entities\AnswerEntity;
use common\helpers\TemplateHelper;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

//print_r($data);exit;
?>
<?php \yii\widgets\Pjax::begin(
        [
                'timeout'       => 10000,
                'clientOptions' => [
                        'container' => 'pjax-container',
                ],
                'options'       => [
                        'id' => 'answer_item_area',
                ],
        ]
); ?>
<?php foreach ($data as $item): ?>
    <article class="clearfix widget-answers__item">
        <div class="post-col">
            <div class="widget-vote">
                <button type="button"
                        class="like"
                        data-id="1020000003903993"
                        data-type="answer"
                        data-do="like"
                        data-toggle="tooltip"
                        data-placement="top"
                        title=""
                        data-original-title="答案对人有帮助，有参考价值">
                    <span class="sr-only">答案对人有帮助，有参考价值</span>
                </button>
                <span class="count">0</span>
                <button type="button"
                        class="hate"
                        data-id="1020000003903993"
                        data-type="answer"
                        data-do="hate"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title=""
                        data-original-title="答案没帮助，是错误的答案，答非所问">
                    <span class="sr-only">答案没帮助，是错误的答案，答非所问</span>
                </button>

            </div>
        </div>

        <div class="post-offset">
            <?= TemplateHelper::showUserAvatar(
                    $item['create_by'],
                    24,
                    true,
                    $item['is_anonymous']
            ) ?>
            <strong><?= TemplateHelper::showUsername(
                        $item['create_by'],
                        true,
                        $item['is_anonymous']
                ) ?></strong>

        <span class="ml10 text-muted">
            <?= TemplateHelper::showhumanTime(
                    $item['modify_at'] ? $item['modify_at'] : $item['create_at']
            ) ?>
            <?php if ($item['modify_at'] > 0): ?>
                <?= Html::a(
                        '更新回答',
                        [
                                'answer-version/index',
                                'answer_id' => $item['id'],
                        ]
                ); ?>
            <?php elseif ($item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS): ?>
                匿名回答
            <?php else: ?>
                回答
            <?php endif; ?>
        </span>

            <div class="answer fmt mt10 mb10">
                <?= $item['content']; ?>
            </div>


            <div class="post-opt">
                <ul class="list-inline mb0">
                    <li><?= Html::a(
                                '链接',
                                [
                                        'question/view',
                                        'id'        => $question_id,
                                        'answer_id' => $item['id'],
                                ]
                        ) ?>
                    </li>
                    <? if ($item['create_by'] == Yii::$app->user->id): ?>
                        <li><?= Html::a(
                                    '编辑',
                                    [
                                            'answer/update',
                                            'id'          => $item['id'],
                                            'question_id' => $question_id,
                                    ]
                            ) ?>
                        </li>
                    <? endif; ?>
                    <li>
                        <?= Html::a(
                                '评论' . ($item['count_comment'] > 0 ? sprintf(
                                        '<data>(%s)</data>)',
                                        $item['count_comment']
                                ) : ''),
                                'javascript:void(0);',
                                [
                                        'data-href'    => Url::to(
                                                ['answer/get-comment-list', 'id' => $item['id']]
                                        ),
                                        'data-on-done' => 'afterShowCommentList',
                                        'data-id'      => $item['id'],
                                ]
                        ) ?>
                    </li>
                    <li class="dropdown">
                        <a href="javascript:void(0);"
                           class="dropdown-toggle"
                           data-toggle="dropdown">更多<b
                                    class="caret"></b></a>
                        <?= Dropdown::widget(
                                [
                                        'items' => [
                                                [
                                                        'label'   => $item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS ? '取消匿名' : '匿名提问',
                                                        'url'     => '/',
                                                        'visible' => $item['create_by'] == Yii::$app->user->id,
                                                ],
                                                [
                                                        'label'   => '删除',
                                                        'url'     => '/',
                                                        'visible' => $item['create_by'] == Yii::$app->user->id,
                                                ],
                                                [
                                                        'label'   => $item['is_fold'] == AnswerEntity::STATUS_FOLD ? '取消折叠' : '折叠',
                                                        'url'     => '/',
                                                        'visible' => $item['create_by'] != Yii::$app->user->id,
                                                ],
                                                [
                                                        'label'   => '公众编辑',
                                                        'url'     => '/',
                                                        'visible' => $item['create_by'] != Yii::$app->user->id,
                                                ],
                                                [
                                                        'label'   => '举报',
                                                        'url'     => '#',
                                                        'visible' => $item['create_by'] != Yii::$app->user->id,
                                                ],
                                        ],
                                ]
                        ); ?>
                    </li>
                </ul>
            </div>

            <div class="widget-comments hidden" id="comment-<?= $item['id'] ?>"></div>
        </div>
    </article>
<?php endforeach; ?>

<?= $pages ? LinkPager::widget(
        ['pagination' => $pages]
) : ''; ?>
<?php \yii\widgets\Pjax::end(); ?>