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

/* @var $this yii\web\View */

$this->title = $question_data['subject'];
$this->params['breadcrumbs'][] = ['label' => '问答'];

/* $var $user_entity UserEntity */
$user_entity = Yii::createObject(UserEntity::className());

?>
<?php
$this->beginBlock('meta-header');
$meta = [];
?>

<?php
$this->endBlock();
?>
<?php
$this->beginBlock('top-header');
?>
<div class="post-topheader">
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
                        <?= TemplateHelper::showUserAvatar($question_data['create_by'], 24, false) ?>
                        <strong><?= TemplateHelper::showUsername($question_data['create_by'], false) ?></strong>
                    </a>
                    <?= TemplateHelper::showhumanTime($question_data['create_at']) ?>
                    <?php if ($question_data['active_at'] > 0): ?>
                        <?= Html::a(
                                '更新问题',
                                [
                                        'question-version/index',
                                        'question_id' => $question_data['id'],
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
                        <button type="button"
                                class="like"
                                data-id="1010000003931811"
                                data-type="question"
                                data-do="like"
                                data-toggle="tooltip"
                                data-placement="top"
                                title="问题对人有帮助，内容完整，我也想知道答案">
                            <span class="sr-only">问题对人有帮助，内容完整，我也想知道答案</span>
                        </button>
                        <span class="count">1</span>
                        <button
                                type="button"
                                class="hate"
                                data-id="1010000003931811"
                                data-type="question"
                                data-do="hate"
                                data-toggle="tooltip"
                                data-placement="bottom"
                                title="问题没有实际价值，缺少关键内容，没有改进余地">
                            <span class="sr-only">问题没有实际价值，缺少关键内容，没有改进余地</span>
                        </button>
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
                            <? if ($question_data['create_by'] == Yii::$app->user->id): ?>
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
                                                                'visible' => $question_data['create_by'] == Yii::$app->user->id,
                                                        ],
                                                        [
                                                                'label'   => '删除',
                                                                'url'     => '/',
                                                                'visible' => $question_data['create_by'] == Yii::$app->user->id,
                                                        ],
                                                        [
                                                                'label'   => '举报',
                                                                'url'     => '#',
                                                                'visible' => $question_data['create_by'] != Yii::$app->user->id,
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

                <h2 class="title h4 mt30 mb20 post-title" id="answers-title"><?= $question_data['count_answer'] ?>
                    个回答</h2>
                <?= $answer_item_html ?>
                <div class="text-center"></div>
            </div>
            <!-- /.widget-answers -->

            <?= $this->render(
                    '_question_answer_form',
                    [
                            'question_data' => $question_data,
                            'answer_model'   => $answer_model,
                    ]
            ); ?>
        </div>
        <!-- /.main -->


        <div class="col-xs-12 col-md-3 side">
            <div class="sfad-sidebar">
                <div class="sfad-item" data-adn="ad-981179" id="adid-981179">
                    <button class="close" type="button" aria-hidden="true">×</button>
                </div>

            </div>


            <div class="widget-box no-border">
                <h2 class="h4 widget-box__title">相似问题</h2>
                <ul class="widget-links list-unstyled">
                    <li class="widget-links__item"><a title="如何使php的MD5与C#的MD5一致？" href="/q/1010000000492161">如何使php的MD5与C#的MD5一致？</a>
                        <small class="text-muted">
                            1 回答
                            | 已解决
                        </small>
                    </li>
                </ul>
            </div>
            <div class="widget-share sharer-0" data-text="md5加密问题" style="display: block;">分享
                <ul id="share" data-title="" class="sn-inline">
                    <li data-network="weibo">
                        <a href="javascript:void(0);"
                           class="entypo-weibo icon-sn-weibo share-1"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=""
                           data-original-title="分享至新浪微博">新浪微博</a></li>
                </ul>
            </div>
        </div>
        <!-- /.side -->
    </div>
</div>