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
        <?= Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
        <h1 class="h3 title" id="questionTitle" data-id="1010000004237070">
            <a href="/q/1010000004237070">yii2中url的重写问题</a></h1>
    </div>
</div>
<?php
$this->endBlock();
?>

<div class="container mt30">
    <div class="row">
        <div class="col-xs-12 main">
            <h3 class="h4 mt0">共被编辑 3 次</h3>
            <table class="table">
                <thead>
                <tr>
                    <th style="width: 10%">版本</th>
                    <th style="width: 15%">更新时间</th>
                    <th style="width: 20%">修改者</th>
                    <th>编辑原因</th>
                    <th style="width: 10%">操作</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><a href="#r3" class="check-revision" data-toggle="collapse" data-parent="#revision-list">#r3</a>
                    </td>
                    <td>刚刚</td>
                    <td><a href="/u/xiamao">瞎猫玩思问</a><strong class="ml10">331</strong></td>
                    <td><em class="text-muted">一般变更</em></td>
                    <td><a href="#r3"
                           class="check-revision btn btn-xs btn-default collapsed"
                           data-toggle="collapse"
                           data-parent="#revision-list">查看</a></td>
                </tr>
                <tr class="revision-item warning panel-collapse collapse" id="r3" style="height: 0px;">
                    <td colspan="5">
                        <div class="revision-content">
                            <h1 class="title h3">yii2中url的重写问题</h1>

                            <div class="fmt"><p>我想把如<code>www.example/index.php?r=site/index?id=49</code>的地址改写为<code>www.example/site/index/49.html</code>这样的，现在我成功去掉了index.php和r=字符，url变成了<code>www.example/site/index?id=49</code>,就卡在urlManager的rules这里,请问我该怎么写rules？<br>另外，我要是想重写为<code>www.example/site/49</code>该怎么写rules呢，求大神给下指点。<br>另外，yii2的重写具体有哪些规则，语法是怎么样的，有资料的给我个地址也行。
                                </p></div>
                            <ul class="taglist--inline mb0">
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2%E8%B7%AF%E7%94%B1"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2路由"
                                                        data-id="1040000003840013"
                                                        data-img="">yii2路由</a></li>
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2-route"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2-route"
                                                        data-id="1040000002941949"
                                                        data-img="">yii2-route</a></li>
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2"
                                                        data-id="1040000000409363"
                                                        data-img="">yii2</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><a href="#r2" class="check-revision" data-toggle="collapse" data-parent="#revision-list">#r2</a>
                    </td>
                    <td>1月2日</td>
                    <td><a href="/u/zhenbianshu">枕边书</a><strong class="ml10">32</strong></td>
                    <td> 补充内容</td>
                    <td><a href="#r2"
                           class="check-revision btn btn-xs btn-default collapsed"
                           data-toggle="collapse"
                           data-parent="#revision-list">查看</a></td>
                </tr>
                <tr class="revision-item warning panel-collapse collapse" id="r2" style="height: 0px;">
                    <td colspan="5">
                        <div class="revision-content">
                            <h1 class="title h3">yii2中url的重写问题</h1>

                            <div class="fmt"><p>我想把如<code>www.example/index.php?r=site/index?id=49</code>的地址改写为<code>www.example/site/index/49.html</code>这样的，现在我成功去掉了index.php和r=字符，url变成了<code>www.example/site/index?id=49</code>,就卡在urlManager的rules这里,请问我该怎么写rules？<br>另外，我要是想重写为<code>www.example/site/49</code>该怎么写rules呢，求大神给下指点。<br>另外，yii2的重写具体有哪些规则，语法是怎么样的，有资料的给我个地址也行。
                                </p></div>
                            <ul class="taglist--inline mb0">
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2%E8%B7%AF%E7%94%B1"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2路由"
                                                        data-id="1040000003840013"
                                                        data-img="">yii2路由</a></li>
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2-route"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2-route"
                                                        data-id="1040000002941949"
                                                        data-img="">yii2-route</a></li>
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2"
                                                        data-id="1040000000409363"
                                                        data-img="">yii2</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><a href="#r1" class="check-revision" data-toggle="collapse" data-parent="#revision-list">#r1</a>
                    </td>
                    <td>1月2日</td>
                    <td><a href="/u/zhenbianshu">枕边书</a><strong class="ml10">32</strong></td>
                    <td> 创建问题</td>
                    <td><a href="#r1"
                           class="check-revision btn btn-xs btn-default collapsed"
                           data-toggle="collapse"
                           data-parent="#revision-list">查看</a></td>
                </tr>
                <tr class="revision-item warning panel-collapse collapse" id="r1" style="height: 0px;">
                    <td colspan="5">
                        <div class="revision-content">
                            <h1 class="title h3">yii2中url的重写问题</h1>

                            <div class="fmt"><p>我想把如<code>www.example/index.php?r=site/index?id=49</code>的地址改写为<code>www.example/site/index/49.html</code>这样的，现在我成功去掉了index.php和r=字符，url变成了<code>www.example/site/index?id=49</code>,就卡在urlManager的rules这里,请问我该怎么写rules？<br>另外，yii2的重写具体有哪些规则，语法是怎么样的，有资料的给我个地址也行。
                                </p></div>
                            <ul class="taglist--inline mb0">
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2%E8%B7%AF%E7%94%B1"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2路由"
                                                        data-id="1040000003840013"
                                                        data-img="">yii2路由</a></li>
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2-route"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2-route"
                                                        data-id="1040000002941949"
                                                        data-img="">yii2-route</a></li>
                                <li class="tagPopup"><a class="tag"
                                                        href="/t/yii2"
                                                        data-toggle="popover"
                                                        data-placement="top"
                                                        data-original-title="yii2"
                                                        data-id="1040000000409363"
                                                        data-img="">yii2</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>