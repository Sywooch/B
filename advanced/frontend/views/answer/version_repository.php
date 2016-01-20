<?php
use common\entities\QuestionVersionEntity;
use common\helpers\TemplateHelper;
use common\models\CacheAnswerModel;
use common\models\CacheQuestionModel;
use yii\data\Pagination;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $question CacheQuestionModel */
/* @var $answer CacheAnswerModel */
/* @var $pages Pagination */

$this->title = $question->subject;
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
        <h1 class="h3 title"><?= \yii\helpers\Html::a(
                $question->subject,
                [
                    '/question/view',
                    'id'        => $question->id,
                    'answer_id' => $answer->id,
                ]
            ) ?></h1>
    </div>
</div>
<?php
$this->endBlock();
?>

<div class="container mt30">
    <div class="row">
        <div class="col-xs-12 main">
            <h3 class="h4 mt0">共被编辑 <?= $pages->totalCount ?> 次</h3>
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
                <?php foreach ($model as $revision => $item): /* @var $item QuestionVersionEntity */ ?>
                    <tr>
                        <td><a href="#r<?= $item->id ?>"
                               class="check-revision"
                               data-toggle="collapse"
                               data-parent="#revision-list">#r<?= ++$revision ?></a>
                        </td>
                        <td><?= TemplateHelper::showHumanTime($item->created_at) ?></td>
                        <td><?= TemplateHelper::showUsername($item->created_by) ?></td>
                        <td><em class="text-muted"><?= $item->reason ? $item->reason :
                                    '添加回答' ?></em></td>
                        <td><a href="#r<?= $item->id ?>"
                               class="check-revision btn btn-xs btn-default collapsed"
                               data-toggle="collapse"
                               data-parent="#revision-list">查看</a></td>
                    </tr>
                    <tr class="revision-item warning panel-collapse collapse"
                        id="r<?= $item->id ?>"
                        style="height: 0px;">
                        <td colspan="5">
                            <div class="revision-content">
                                <div class="fmt"><?= $item->content ?></div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>