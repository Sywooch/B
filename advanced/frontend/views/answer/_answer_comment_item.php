<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/14
 * Time: 22:10
 */
use common\entities\CommentEntity;
use common\helpers\TemplateHelper;
use common\models\CacheCommentModel;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php foreach ($data as $index => $item) : /* @var $item CacheCommentModel */ ?>
    <div class="widget-comments__item hover-show">
        <div class="votes widget-vote">
            <!--评论投票-->
            <?= $this->render(
                '_answer_comment_vote',
                [
                    'id'          => $item->id,
                    'count_vote'  => $item->count_like - $item->count_hate,
                    'vote_status' => $item->vote_status,
                ]
            ) ?></div>
        <div class="comment-content wordbreak">
            <div class="content fmt mt5">
                <?= TemplateHelper::dealWithComment($item->content); ?>
            </div>
        </div>
        <ul class="list-inline pb10 pt10 ml25">
            <li>#&nbsp;
                <?= TemplateHelper::showUsername(
                    $item->created_by,
                    true,
                    $item->is_anonymous == CommentEntity::STATUS_ANONYMOUS
                ) ?></li>
            <li><?php if ($item->is_anonymous != CommentEntity::STATUS_ANONYMOUS): ?>
                    <?php if ($answer_create_user_id == $item->created_by) : ?>
                        [答主]
                    <?php elseif ($question_create_user_id == $item->created_by) : ?>
                        [题主]
                    <?php elseif (!Yii::$app->user->isGuest && $item->created_by == Yii::$app->user->id) : ?>
                        [我]
                    <?php endif; ?>
                <?php endif; ?></li>
            <li><span class="createdDate">
                        <?= TemplateHelper::showHumanTime($item->created_at); ?></span>
                <?php if ($item->is_anonymous == CommentEntity::STATUS_ANONYMOUS): ?>
                    匿名评论
                <?php else: ?>
                    评论
                <?php endif; ?></li>
            <?php if ($item->created_by != Yii::$app->user->id) : ?>
        <li>
        <?php if ($item->is_anonymous != CommentEntity::STATUS_ANONYMOUS): ?>
            <?= Html::a(
                '回复',
                'javascritp:;',
                [
                    'title'           => '回复Ta',
                    'data-do-comment' => true,
                    'data-answer-id'  => $item->associate_id,
                    'data-username'   => TemplateHelper::showUsername(
                        $item->created_by,
                        false
                    ),
                ]
            ) ?>
            </li>
        <?php endif; ?>
            <li><span class="pull-right commentTools hover-show-obj">
                        <?= Html::a(
                            '举报',
                            'javascritp:;',
                            [
                                'title'             => '举报',
                                'data-do-report'    => true,
                                'data-object'       => 'comment',
                                'data-associate_id' => $item->id,
                            ]
                        ) ?>
                </span>
                <?php endif; ?></li>


            <? if ($item->created_by == Yii::$app->user->id): ?>
                <li><?= Html::a(
                        '编辑',
                        [
                            'comment/update',
                            'id'          => $item->id,
                            'question_id' => $question_id,
                            'answer_id'   => $item->associate_id,
                        ]
                    ) ?>
                </li>
            <? endif; ?>
            <li class="dropdown">
                <a href="javascript:void(0);"
                   class="dropdown-toggle"
                   data-toggle="dropdown">更多<b
                        class="caret"></b></a>
                <?= Dropdown::widget(
                    [
                        'items' => [
                            [
                                'label'       => $item->is_anonymous == CommentEntity::STATUS_ANONYMOUS ? '取消匿名' : '置为匿名',
                                'url'         => '/',
                                'visible'     => !Yii::$app->user->isGuest && $item->created_by == Yii::$app->user->id,
                                'linkOptions' => [
                                    'data-do-confirm' => true,
                                    'data-title'      => $item->is_anonymous == CommentEntity::STATUS_ANONYMOUS ? '取消匿名' : '设置匿名',
                                    'data-message'    => sprintf(
                                        '您确定要对此评论%s?',
                                        $item->is_anonymous == CommentEntity::STATUS_ANONYMOUS ? '取消匿名' : '设置匿名'
                                    ),
                                    'data-redirect'   => Url::to(
                                        [
                                            'comment/anonymous',
                                            'id'          => $item->id,
                                            'question_id' => $question_id,
                                            'answer_id'   => $item->associate_id,
                                        ]
                                    ),
                                ],
                            ],
                            [
                                'label'       => '删除',
                                'url'         => ['answer/delete', 'id' => $item->id],
                                'visible'     => !Yii::$app->user->isGuest && $item->created_by == Yii::$app->user->id,
                                'linkOptions' => [
                                    'data-do-confirm' => true,
                                    'data-title'      => '删除确认',
                                    'data-message'    => '您确定要删除此回答？',
                                    'data-redirect'   => Url::to(
                                        ['answer/delete', 'id' => $item->id]
                                    ),
                                ],
                            ],
                            [
                                'label'       => '举报',
                                'url'         => 'javascritp:;',
                                'visible'     => $item->created_by != Yii::$app->user->id,
                                'linkOptions' => [
                                    'title'             => '举报回答',
                                    'data-do-report'    => true,
                                    'data-object'       => 'answer',
                                    'data-associate_id' => $item->id,
                                ],
                            ],
                        ],
                    ]
                ); ?>
            </li>
        </ul>
    </div>
<?php endforeach; ?>
