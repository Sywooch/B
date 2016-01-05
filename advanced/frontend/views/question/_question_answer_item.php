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

?>

<div id="answer_item_area">
<?php foreach ($data as $item): ?>
    <?php if ($item['type'] == AnswerEntity::TYPE_ANSWER): ?>
        <article class="clearfix widget-answers__item" id="answer-<?= $item['id'] ?>">
            <div class="post-col">
                <div class="widget-vote">
                    <!--答案投票-->
                    <?= $this->render(
                        '_question_answer_vote',
                        [
                            'id'          => $item['id'],
                            'count_vote'  => $item['count_like'] - $item['count_hate'],
                            'vote_status' => $item['vote_status'],
                        ]
                    ) ?>
                </div>
            </div>

            <div class="post-offset">
                <?= TemplateHelper::showUserAvatar(
                    $item['created_by'],
                    24,
                    true,
                    $item['is_anonymous']
                ) ?>
                <strong><?= TemplateHelper::showUsername(
                        $item['created_by'],
                        true,
                        $item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS
                    ) ?></strong>

        <span class="ml10 text-muted">
            <?= TemplateHelper::showHumanTime(
                $item['updated_at'] ? $item['updated_at'] : $item['created_at']
            ) ?>
            <?php if ($item['updated_at'] > 0): ?>
                <?= Html::a(
                    '更新回答',
                    [
                        'answer-version/index',
                        'answer_id' => $item['id'],
                    ],
                    ['rel' => 'nofollow',]
                ); ?>
            <?php else: ?>
                回答
            <?php endif; ?>
            <?= $item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS ? '[匿名回答]' : '' ?>
        </span>

                <div class="answer fmt mt10 mb10">
                    <?= $item['content']; ?>
                </div>


                <div class="post-opt"
                ">
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
                    <? if ($item['created_by'] == Yii::$app->user->id): ?>
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
                                '(%s)',
                                $item['count_comment']
                            ) : ''),
                            'javascript:void(0);',
                            [
                                'data-do-ajax-submit' => true,
                                'data-href'           => Url::to(
                                    ['answer/get-comment-list', 'id' => $item['id']]
                                ),
                                'data-on-done'        => 'afterShowCommentList',
                                'data-id'             => $item['id'],
                                'data-do'             => 'comment',
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
                                        'label'       => $item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS ? '取消匿名' : '置为匿名',
                                        'url'         => '/',
                                        'visible'     => !Yii::$app->user->isGuest && $item['created_by'] == Yii::$app->user->id,
                                        'linkOptions' => [
                                            'data-do-confirm' => true,
                                            'data-title'      => $item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS ? '取消匿名' : '设置匿名',
                                            'data-message'    => sprintf(
                                                '您确定要对此回答%s?',
                                                $item['is_anonymous'] == AnswerEntity::STATUS_ANONYMOUS ? '取消匿名' : '设置匿名'
                                            ),
                                            'data-redirect'   => Url::to(
                                                ['answer/anonymous', 'id' => $item['id']]
                                            ),
                                        ],
                                    ],
                                    [
                                        'label'       => '删除',
                                        'url'         => ['answer/delete', 'id' => $item['id']],
                                        'visible'     => !Yii::$app->user->isGuest && $item['created_by'] == Yii::$app->user->id,
                                        'linkOptions' => [
                                            'data-do-confirm' => true,
                                            'data-title'      => '删除确认',
                                            'data-message'    => '您确定要删除此回答？',
                                            'data-redirect'   => Url::to(
                                                ['answer/delete', 'id' => $item['id']]
                                            ),
                                        ],
                                    ],
                                    [
                                        'label'   => $item['is_fold'] == AnswerEntity::STATUS_FOLD ? '取消折叠' : '折叠',
                                        'url'     => '/',
                                        'visible' => !Yii::$app->user->isGuest && $item['created_by'] != Yii::$app->user->id,
                                    ],
                                    [
                                        'label'   => '公众编辑',
                                        'url'     => [
                                            'answer/common-edit',
                                            'id'          => $item['id'],
                                            'question_id' => $question_id,
                                        ],
                                        'visible' => !Yii::$app->user->isGuest && $item['created_by'] != Yii::$app->user->id,
                                    ],
                                    [
                                        'label'       => '举报',
                                        'url'         => 'javascritp:;',
                                        'visible'     => $item['created_by'] != Yii::$app->user->id,
                                        'linkOptions' => [
                                            'title'             => '举报回答',
                                            'data-do-report'    => true,
                                            'data-object'       => 'answer',
                                            'data-associate_id' => $item['id'],
                                        ],
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
    <?php else: ?>
        <?= $item['type']; ?>
    <?php endif; ?>
<?php endforeach; ?>
</div>