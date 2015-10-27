<?php

use common\entities\UserEntity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Question */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '问答', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/* $var $user_entity UserEntity */
$user_entity = Yii::createObject(UserEntity::className());

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
                    <?= $model->subject ?>
                </h1>

                <div class="author">
                    <a href="/u/smallkiss" class="mr5">
                        <img class="avatar-24 mr10"
                             src="<?= Yii::$app->user->identity->getAvatar(24, true) ?>"
                             alt="smallkiss">


                        <strong><?= $user_entity->getUsernameByUserId($model->create_by) ?></strong>
                    </a>
                    <?= Yii::$app->formatter->asRelativeTime($model->create_at) ?> 提问
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
                        <strong><?= $model->count_follow ?></strong> 关注
                    </li>
                    <li>
                        <button type="button"
                                id="sideBookmark"
                                class="btn btn-default btn-sm"
                                data-id="1010000003903942"
                                data-type="question">收藏
                        </button>
                        <strong id="sideBookmarked"><?= $model->count_favorite ?></strong> 收藏，
                        <strong class="no-stress"><?= $model->count_views ?></strong> 浏览
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
                        <?/*= $model->content */?>

                    </div>
                    <ul class="taglist--inline mb20">
                        <li class="tagPopup"><a class="tag"
                                                href="/t/c"
                                                data-toggle="popover"
                                                data-placement="top"
                                                data-original-title="c"
                                                data-id="1040000000089457"
                                                data-img="">c</a></li>
                        <li class="tagPopup"><a class="tag"
                                                href="/t/c%2B%2B"
                                                data-toggle="popover"
                                                data-placement="top"
                                                data-original-title="c++"
                                                data-id="1040000000089741"
                                                data-img="">c++</a></li>
                        <li class="tagPopup"><a class="tag"
                                                href="/t/php"
                                                data-toggle="popover"
                                                data-placement="top"
                                                data-original-title="php"
                                                data-id="1040000000089387"
                                                data-img="">php</a></li>
                        <li class="tagPopup"><a class="tag"
                                                href="/t/c%23"
                                                data-toggle="popover"
                                                data-placement="top"
                                                data-original-title="c#"
                                                data-id="1040000000089581"
                                                data-img="">c#</a></li>
                        <li class="tagPopup"><a class="tag"
                                                href="/t/java"
                                                data-toggle="popover"
                                                data-placement="top"
                                                data-original-title="java"
                                                data-id="1040000000089449"
                                                data-img="">java</a></li>
                    </ul>


                    <div class="post-opt">
                        <ul class="list-inline mb0">
                            <li><a href="/q/1010000003903942">链接</a></li>
                            <li><a href="javascript:void(0);"
                                   class="comments"
                                   data-id="1010000003903942"
                                   data-target="#comment-1010000003903942">
                                    评论</a></li>


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

            <div class="widget-answers">
                <div class="btn-group pull-right" role="group">
                    <a href="/q/1010000003903942#answers-title" id="sortby-rank" class="btn btn-default btn-xs active">默认排序</a>
                    <a href="?sort=created#answers-title" id="sortby-created" class="btn btn-default btn-xs">时间排序</a>
                </div>

                <h2 class="title h4 mt30 mb20 post-title" id="answers-title">2 个回答</h2>


                <article class="clearfix widget-answers__item" id="a-1020000003903993">
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
                        <a href="/u/baiyi"><img class="avatar-24"
                                                src="http://sfault-avatar.b0.upaiyun.com/179/096/1790962493-1030000000512535_big64"
                                                alt=""></a>
                        <strong><a href="/u/baiyi" class="mr5">白一梓</a> 2.2k</strong>

        <span class="ml10 text-muted">
            1 小时前 回答
                    </span>

                        <div class="answer fmt mt10">

                            <p>md5函数都支持分段计算，只要你分割的文件不添加额外的文件内容，计算出来的就和未分割之前的是一致的。</p>

                            <div class="widget-codetool" style="display:none;">
                                <button class="selectCode btn btn-xs">全选</button>
                                <button href="javascript:void(0);" class="copyCode btn btn-xs" data-clipboard-text="#include<stdio.h>
#include<openssl/md5.h>
#include<string.h>

int main( int argc, char **argv )
{
MD5_CTX ctx;
unsigned char *data=&quot;123&quot;;
unsigned char md[16];
char buf[33]={'\0'};
char tmp[3]={'\0'};
int i;

MD5_Init(&amp;ctx);
MD5_Update(&amp;ctx,data,strlen(data));//多次调用这个函数就可以了，你可以每次update 1kB的数据
MD5_Final(md,&amp;ctx);//所有的文件都处理完了，调用这个函数就行了

for( i=0; i<16; i++ ){
sprintf(tmp,&quot;%02X&quot;,md[i]);
strcat(buf,tmp);
}
printf(&quot;%s\n&quot;,buf);
return 0;
}" data-toggle="tooltip" data-placement="top" title="">复制
                                </button>
                                <button href="javascript:void(0);" class="saveToNote btn btn-xs">放进笔记</button>
                            </div><pre class="hljs cpp"><code><span class="hljs-preprocessor">#<span class="hljs-keyword">include</span>&lt;stdio.h&gt;</span>
                                    <span class="hljs-preprocessor">#<span class="hljs-keyword">include</span>&lt;openssl/md5.h&gt;</span>
                                    <span class="hljs-preprocessor">#<span class="hljs-keyword">include</span>&lt;string.h&gt;</span>

<span class="hljs-function"><span class="hljs-keyword">int</span> <span class="hljs-title">main</span><span class="hljs-params">( <span
                class="hljs-keyword">int</span> argc, <span class="hljs-keyword">char</span> **argv )</span>
</span>{
       MD5_CTX ctx;
                                    <span class="hljs-keyword">unsigned</span> <span class="hljs-keyword">char</span>
       *data=<span class="hljs-string">"123"</span>;
                                    <span class="hljs-keyword">unsigned</span> <span class="hljs-keyword">char</span>
       md[<span class="hljs-number">16</span>];
                                    <span class="hljs-keyword">char</span>
       buf[<span class="hljs-number">33</span>]={<span class="hljs-string">'\0'</span>};
                                    <span class="hljs-keyword">char</span>
       tmp[<span class="hljs-number">3</span>]={<span class="hljs-string">'\0'</span>};
                                    <span class="hljs-keyword">int</span> i;

       MD5_Init(&amp;ctx);
       MD5_Update(&amp;ctx,data,<span class="hljs-built_in">strlen</span>(data));<span class="hljs-comment">//多次调用这个函数就可以了，你可以每次update 1kB的数据</span>
       MD5_Final(md,&amp;ctx);<span class="hljs-comment">//所有的文件都处理完了，调用这个函数就行了</span>

                                    <span class="hljs-keyword">for</span>( i=<span class="hljs-number">0</span>;
       i&lt;<span class="hljs-number">16</span>; i++ ){
                                    <span class="hljs-built_in">sprintf</span>(tmp,<span class="hljs-string">"%02X"</span>,md[i]);
                                    <span class="hljs-built_in">strcat</span>(buf,tmp);
       }
                                    <span class="hljs-built_in">printf</span>(<span class="hljs-string">"%s\n"</span>,buf);
                                    <span class="hljs-keyword">return</span> <span class="hljs-number">0</span>;
       }</code></pre>

                        </div>


                        <div class="post-opt">
                            <ul class="list-inline mb0">

                                <li><a href="/q/1010000003903942/a-1020000003903993">链接</a></li>
                                <li><a href="javascript:void(0);"
                                       class="comments"
                                       data-id="1020000003903993"
                                       data-target="#comment-1020000003903993"> 评论</a></li>
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">更多<b
                                                class="caret"></b></a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                        <li><a href="#911"
                                               data-id="1020000003903993"
                                               data-toggle="modal"
                                               data-target="#911"
                                               data-type="answer"
                                               data-typetext="答案">举报</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <div class="widget-comments hidden" id="comment-1020000003903993" data-id="1020000003903993">
                            <div class="widget-comments__form row">
                                <div class="col-md-12">
                                    请先 <a class="commentLogin" href="javascript:void(0);">登录</a> 后评论
                                </div>

                            </div>
                            <!-- /.widget-comments__form -->
                        </div>
                        <!-- /.widget-comments -->


                    </div>
                </article>
                <!-- /article -->


                <article class="clearfix widget-answers__item" id="a-1020000003904111">
                    <div class="post-col">
                        <div class="widget-vote">
                            <button type="button"
                                    class="like"
                                    data-id="1020000003904111"
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
                                    data-id="1020000003904111"
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
                        <a href="/u/HUST_Distance"><img class="avatar-24"
                                                        src="http://static.segmentfault.com/global/img/user-64.png"
                                                        alt=""></a>
                        <strong><a href="/u/HUST_Distance" class="mr5">HUST_Distance</a> 3</strong>

        <span class="ml10 text-muted">
            38 分钟前 回答
                    </span>

                        <div class="answer fmt mt10">
                            <p>多次使用update接口</p>
                        </div>


                        <div class="post-opt">
                            <ul class="list-inline mb0">

                                <li><a href="/q/1010000003903942/a-1020000003904111">链接</a></li>
                                <li><a href="javascript:void(0);"
                                       class="comments"
                                       data-id="1020000003904111"
                                       data-target="#comment-1020000003904111"> 评论</a></li>
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">更多<b
                                                class="caret"></b></a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                        <li><a href="#911"
                                               data-id="1020000003904111"
                                               data-toggle="modal"
                                               data-target="#911"
                                               data-type="answer"
                                               data-typetext="答案">举报</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <div class="widget-comments hidden" id="comment-1020000003904111" data-id="1020000003904111">
                            <div class="widget-comments__form row">
                                <div class="col-md-12">
                                    请先 <a class="commentLogin" href="javascript:void(0);">登录</a> 后评论
                                </div>

                            </div>
                            <!-- /.widget-comments__form -->
                        </div>
                        <!-- /.widget-comments -->


                    </div>
                </article>
                <!-- /article -->


                <div class="text-center">

                </div>
            </div>
            <!-- /.widget-answers -->


            <h4>撰写答案</h4>
            <input type="hidden" id="draftId" value="">
            <input type="hidden" value="1010000003903942" id="questionId">

            <form action="/question/1010000003903942/answers/add" method="post" class="editor-wrap">
                <div class="editor" id="questionText">
                    <textarea id="answerEditor"
                              name="text"
                              class="form-control"
                              rows="4"
                              placeholder="撰写答案..."></textarea>
                </div>
                <div id="answerSubmit" class="hide mt15 clearfix">
                    <div class="checkbox pull-left">
                        <label><input type="checkbox" class="" id="shareToWeibo">
                            同步到新浪微博</label>
                    </div>
                    <div class="pull-right">
                    <span id="editorStatus" class="hidden text-muted">

                    </span>
                        <a id="dropIt" href="javascript:void(0);" class="mr10 hidden">
                            [舍弃]
                        </a>
                        <button type="submit"
                                id="answerIt"
                                data-id="1010000003903942"
                                class="btn btn-lg btn-primary ml20">提交回答
                        </button>
                    </div>
                </div>
            </form>


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
                    <li data-network="weibo"><a href="javascript:void(0);"
                                                class="entypo-weibo icon-sn-weibo share-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""
                                                data-original-title="分享至新浪微博">新浪微博</a></li>
                    <li data-network="wechart"><a href="javascript:void(0);"
                                                  class="entypo-wechart icon-sn-wechat share-2"
                                                  data-toggle="tooltip"
                                                  data-placement="top"
                                                  title=""
                                                  data-original-title="分享至微信">微信</a></li>
                    <li data-network="twitter"><a href="javascript:void(0);"
                                                  class="entypo-twitter icon-sn-twitter share-3"
                                                  data-toggle="tooltip"
                                                  data-placement="top"
                                                  title=""
                                                  data-original-title="分享至 Twitter">Twitter</a></li>
                    <li data-network="facebook"><a href="javascript:void(0);"
                                                   class="entypo-facebook icon-sn-facebook share-4"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   title=""
                                                   data-original-title="分享至 Facebook">Facebook</a></li>
                    <li data-network="renren"><a href="javascript:void(0);"
                                                 class="entypo-renren icon-sn-renren share-5"
                                                 data-toggle="tooltip"
                                                 data-placement="top"
                                                 title=""
                                                 data-original-title="分享至人人网">人人网</a></li>
                    <li data-network="douban"><a href="javascript:void(0);"
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


<div class="question-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
                'Delete',
                ['delete', 'id' => $model->id],
                [
                        'class' => 'btn btn-danger',
                        'data'  => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method'  => 'post',
                        ],
                ]
        ) ?>
    </p>

    <?= DetailView::widget(
            [
                    'model'      => $model,
                    'attributes' => [
                            'id',
                            'subject',
                            'alias',
                            'content:ntext',
                            'count_views',
                            'count_answer',
                            'count_favorite',
                            'count_follow',
                            'create_at',
                            'create_by',
                            'active_at',
                            'tags',
                    ],
            ]
    ) ?>

</div>
