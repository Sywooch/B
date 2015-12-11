<?php

use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\helpers\TemplateHelper;
use common\widgets\UEditor\UEditor;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;
use yii\widgets\LinkPager;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

$this->title = $question_data['subject'];
$this->params['breadcrumbs'][] = ['label' => '问答'];

/* $var $user_entity UserEntity */
//$user_entity = Yii::createObject(UserEntity::className());
?>
<?php
$this->beginBlock('meta-header');
$meta = [];
$this->endBlock();
?>

<?php
$this->beginBlock('top-header');
?>
<div class="post-topheader bg-gray pt20 pb20">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <?= Breadcrumbs::widget(
                        [
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]
                ) ?>

                <h1 class="title">
                    <?= $question_data['subject'] ?>
                </h1>

                <div class="author">
                    <a href="/u/smallkiss" class="mr5">
                        <?= TemplateHelper::showUserAvatar($question_data['created_by'], 24, false) ?>
                        <strong><?= TemplateHelper::showUsername($question_data['created_by'], false) ?></strong>
                    </a>
                    <?= TemplateHelper::showHumanTime($question_data['created_at']) ?>
                    <?php if ($question_data['updated_at'] > 0): ?>
                        <?= Html::a(
                                '更新问题',
                                [
                                        'question-version/index',
                                        'question_id' => $question_data['id'],
                                ],
                                [
                                        'rel' => 'nofollow',
                                ]
                        ); ?>
                    <?php elseif ($question_data['is_anonymous'] == QuestionEntity::STATUS_ANONYMOUS): ?>
                        匿名提问
                    <?php else: ?>
                        提问
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <ul class="widget-action--ver list-unstyled">
                    <li>
                        <button type="button"
                                id="sideFollow"
                                class="btn btn-success btn-sm"
                                data-id="1010000003903942"
                                data-do="follow"
                                data-type="question"
                                data-toggle="tooltip"
                                data-placement="right"
                                title=""
                                data-original-title="关注后将获得更新提醒">关注
                        </button>
                        <strong><?= $question_data['count_follow'] ?></strong> 关注
                    </li>
                    <li>
                        <button type="button"
                                id="sideBookmark"
                                class="btn btn-default btn-sm"
                                data-id="1010000003903942"
                                data-type="question">收藏
                        </button>
                        <strong id="sideBookmarked"><?= $question_data['count_favorite'] ?></strong> 收藏，
                        <strong class="no-stress"><?= $question_data['count_views'] ?></strong> 浏览
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$this->endBlock();
?>

<div class="container mt30">
    <div class="row">
        <div class="col-xs-12 col-md-9 main">
            <article class="widget-question__item">

                <div class="post-col">
                    <div class="widget-vote">
                        <!--问题投票-->
                        <?= $this->render(
                                '_question_vote',
                                [
                                        'id'         => $question_data['id'],
                                        'count_like' => $question_data['count_like'],
                                ]
                        ) ?>
                    </div>
                    <!-- end .widget-vote -->
                </div>

                <div class="post-offset">
                    <div class="question fmt">
                        <?= $question_data['content'] ?>

                    </div>
                    <ul class="taglist--inline mb20">
                        <?= TemplateHelper::showTagLiLabelByName($question_data['tags']) ?>
                    </ul>

                    <div class="post-opt">
                        <ul class="list-inline mb0">
                            <li><?= Html::a('链接', ['question/view', 'id' => $question_data['id']]); ?></li>
                            <? if ($question_data['created_by'] == Yii::$app->user->id): ?>
                                <li><?= Html::a(
                                            '编辑',
                                            ['question/update', 'id' => $question_data['id']]
                                    ) ?></li>
                            <? endif; ?>
                            <li class="dropdown">
                                <a href="javascript:void(0);"
                                   class="dropdown-toggle"
                                   data-toggle="dropdown">更多<b class="caret"></b></a>
                                <?= Dropdown::widget(
                                        [
                                                'items' => [
                                                        [
                                                                'label'   => $question_data['is_anonymous'] == QuestionEntity::STATUS_ANONYMOUS ? '取消匿名' : '匿名提问',
                                                                'url'     => '/',
                                                                'visible' => $question_data['created_by'] ==
                                                                        Yii::$app->user->id,
                                                        ],
                                                        [
                                                                'label'   => '删除',
                                                                'url'     => '/',
                                                                'visible' => $question_data['created_by'] ==
                                                                        Yii::$app->user->id,
                                                        ],
                                                        [
                                                                'label'   => '公众编辑',
                                                                'url'     => [
                                                                        'question/common-edit',
                                                                        'id' => $question_data['id'],
                                                                ],
                                                                'visible' => $question_data['created_by'] !=
                                                                        Yii::$app->user->id,
                                                        ],
                                                        [
                                                                'label'   => '举报',
                                                                'url'     => '#',
                                                                'visible' => $question_data['created_by'] !=
                                                                        Yii::$app->user->id,
                                                        ],
                                                ],
                                        ]
                                ); ?>
                            </li>
                        </ul>
                    </div>
                    <div class="widget-comments hidden" id="comment-1010000003903942" data-id="1010000003903942">
                        <div class="widget-comments__form row">
                            <div class="col-md-12">
                                请先 <a class="commentLogin" href="javascript:void(0);">登录</a> 后评论
                            </div>

                        </div>
                        <!-- /.widget-comments__form -->
                    </div>
                    <!-- /.widget-comments -->


                </div>
                <!-- end .post-offset -->
            </article>

            <div class="widget-answers" id="answer-list">
                <div class="btn-group pull-right" role="group">
                    <?= Html::a(
                            '默认排序',
                            [
                                    'question/view',
                                    'id'   => $question_data['id'],
                                    'sort' => 'default',
                            ],
                            [
                                    'id'    => 'sortby-rank',
                                    'class' => 'btn btn-default btn-xs' . ($sort == 'default' ? ' active' : ''),
                                    'rel'   => 'nofollow',
                            ]
                    ); ?><?= Html::a(
                            '时间排序',
                            [
                                    'question/view',
                                    'id'   => $question_data['id'],
                                    'sort' => 'created',
                            ],
                            [
                                    'id'    => 'sortby-created',
                                    'class' => 'btn btn-default btn-xs' . ($sort != 'default' ? ' active' : ''),
                                    'rel'   => 'nofollow',
                            ]
                    ); ?>

                </div>

                <h2 class="title h4 mt30 mb20 post-title" id="answers-title">
                    <?= $question_data['count_answer'] ?>个回答
                </h2>
                <?php Pjax::begin(
                        [
                                'id'              => 'answer-pjax',
                                'enablePushState' => false,
                                'linkSelector'    => '#answer-page',
                                'timeout'         => 10000,
                                'clientOptions'   => [
                                        'container' => 'pjax-container-answer',
                                ],
                                'options'         => [
                                        'id' => 'answer_item_area',
                                ],
                        ]
                ); ?>
                <?= $answer_item_html ?>

                <?= $pages ? LinkPager::widget(
                        [
                                'pagination'  => $pages,
                                'options'     => [
                                        'id'    => 'answer-page',
                                        'class' => 'pagination',

                                ],
                                'linkOptions' => [
                                    //'rel' => 'nofollow',
                                ],
                        ]
                ) : ''; ?>
                <?php Pjax::end(); ?>

                <div class="text-center"></div>
            </div>
            <!-- /.widget-answers -->

            <?= $this->render(
                    '_question_answer_form',
                    [
                            'question_data' => $question_data,
                            'answer_model'  => $answer_model,
                    ]
            ); ?>
        </div>
        <!-- /.main -->


        <div class="col-xs-12 col-md-3 side">
            <!--<div class="sfad-sidebar"></div>-->
            <?php if ($similar_question): ?>
                <div class="widget-box no-border">
                    <h2 class="h4 widget-box__title">相似问题</h2>
                    <ul class="widget-links list-unstyled">
                        <?php foreach ($similar_question as $question): ?>
                            <?php if ($question['id'] != $question_data['id']): ?>
                                <li class="widget-links__item">
                                    <?= Html::a(
                                            $question['subject'],
                                            ['question/view', 'id' => $question['id']]
                                    ); ?>
                                    <?php if ($question['count_answer']): ?>
                                        <small class="text-muted">
                                            <?= $question['count_answer']; ?> 回答
                                        </small>
                                    <?php endif; ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.side -->
    </div>
</div>