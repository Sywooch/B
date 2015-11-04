<?php

use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\helpers\TemplateHelper;
use common\widgets\UEditor\UEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;
use yii\widgets\LinkPager;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $question_model common\models\Question */

$this->title = $question_model->id;
$this->params['breadcrumbs'][] = ['label' => '问答', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/* $var $user_entity UserEntity */
$user_entity = Yii::createObject(UserEntity::className());

?>
<?php
$this->beginBlock('top-header');
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
                    <?= $question_model->subject ?>
                </h1>

                <div class="author">
                    <a href="/u/smallkiss" class="mr5">
                        <?= TemplateHelper::showUserAvatar($question_model->create_by, 24, false) ?>
                        <strong><?= TemplateHelper::showUsername($question_model->create_by, false) ?></strong>
                    </a>
                    <?= TemplateHelper::showhumanTime($question_model->create_at) ?> 提问
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
                        <strong><?= $question_model->count_follow ?></strong> 关注
                    </li>
                    <li>
                        <button type="button"
                                id="sideBookmark"
                                class="btn btn-default btn-sm"
                                data-id="1010000003903942"
                                data-type="question">收藏
                        </button>
                        <strong id="sideBookmarked"><?= $question_model->count_favorite ?></strong> 收藏，
                        <strong class="no-stress"><?= $question_model->count_views ?></strong> 浏览
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
                <div class="post-offset">
                    <div class="question fmt">
                        <?= $question_model->content ?>

                    </div>
                    <ul class="taglist--inline mb20">
                        <?= TemplateHelper::showTagLiLabelByName($question_model->tags) ?>
                    </ul>


                    <div class="post-opt">
                        <ul class="list-inline mb0">
                            <li><?= Html::a('链接', ['question/view', 'id' => $question_model->id]); ?></li>

                            <li class="dropdown">
                                <a href="javascript:void(0);"
                                   class="dropdown-toggle"
                                   data-toggle="dropdown">更多<b class="caret"></b></a>
                                <ul class="dropdown-menu dropdown-menu-left">
                                    <li><a href="#911"
                                           data-id="1010000003903942"
                                           data-toggle="modal"
                                           data-target="#911"
                                           data-type="question"
                                           data-typetext="问题">举报</a>
                                    </li>


                                </ul>
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
                    <a href="/q/1010000003903942#answers-title" id="sortby-rank" class="btn btn-default btn-xs active">默认排序</a>
                    <a href="?sort=created#answers-title" id="sortby-created" class="btn btn-default btn-xs">时间排序</a>
                </div>

                <h2 class="title h4 mt30 mb20 post-title" id="answers-title"><?= $question_model->count_answer ?> 个回答</h2>
                <?= $answer_item_html ?>
                <div class="text-center"></div>
            </div>
            <!-- /.widget-answers -->

            <?= $this->render(
                    '_question_answer_form',
                    [
                            'question_model' => $question_model,
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
                    <li class="widget-links__item"><a title="游戏开发后端都是c++吗？" href="/q/1010000003496468">游戏开发后端都是c++吗？</a>
                        <small class="text-muted">
                            4 回答
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="Python有那些单元测试框架可以用？" href="/q/1010000000166176">Python有那些单元测试框架可以用？</a>
                        <small class="text-muted">
                            2 回答
                            | 已解决
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="哪位大哥帮忙翻译成PHP?" href="/q/1010000000263829">哪位大哥帮忙翻译成PHP?</a>
                        <small class="text-muted">
                            1 回答
                            | 已解决
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="求c++扩充数组的办法" href="/q/1010000002396657">求c++扩充数组的办法</a>
                        <small class="text-muted">
                            1 回答
                            | 已解决
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="php是c编写，为何win下依赖vc这类c++编译器？" href="/q/1010000002887763">php是c编写，为何win下依赖vc这类c++编译器？</a>
                        <small class="text-muted">
                            7 回答
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="C# 调用 C++ DLL，DllImport" href="/q/1010000002393201">C# 调用
                                                                                                                 C++
                                                                                                                 DLL，DllImport</a>
                        <small class="text-muted">
                            1 回答
                            | 已解决
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="一道PHP面试的编程题" href="/q/1010000003754168">一道PHP面试的编程题</a>
                        <small class="text-muted">
                            3 回答
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="一个字符转化为byte或sbyte类型，新的值可能是负数吗？C#或Java中"
                                                      href="/q/1010000003719323">一个字符转化为byte或sbyte类型，新的值可能是负数吗？C#或Java中</a>
                        <small class="text-muted">
                            1 回答
                            | 已解决
                        </small>
                    </li>
                    <li class="widget-links__item"><a title="Java or c/c++ 与PHP配合使用" href="/q/1010000003042988">Java or
                                                                                                                c/c++
                                                                                                                与PHP配合使用</a>
                        <small class="text-muted">
                            5 回答
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
                    <li data-network="wechart">
                        <a href="javascript:void(0);"
                           class="entypo-wechart icon-sn-wechat share-2"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=""
                           data-original-title="分享至微信">微信</a></li>
                    <li data-network="twitter">
                        <a href="javascript:void(0);"
                           class="entypo-twitter icon-sn-twitter share-3"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=""
                           data-original-title="分享至 Twitter">Twitter</a></li>
                    <li data-network="facebook">
                        <a href="javascript:void(0);"
                           class="entypo-facebook icon-sn-facebook share-4"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=""
                           data-original-title="分享至 Facebook">Facebook</a></li>
                    <li data-network="renren">
                        <a href="javascript:void(0);"
                           class="entypo-renren icon-sn-renren share-5"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=""
                           data-original-title="分享至人人网">人人网</a></li>
                    <li data-network="douban">
                        <a href="javascript:void(0);"
                           class="entypo-douban icon-sn-douban share-6"
                           data-toggle="tooltip"
                           data-placement="top"
                           title=""
                           data-original-title="分享至豆瓣">豆瓣</a></li>
                </ul>
            </div>


        </div>
        <!-- /.side -->

    </div>
</div>