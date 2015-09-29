<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Question */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="question-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="form-group">
        <label for="title" class="sr-only">标题</label>
        <input id="myTitle" type="text" name="title" required data-error="" autocomplete="off"
               class="form-control tagClose input-lg" placeholder="标题：一句话说清问题，用问号结尾"
               value="">
    </div>

    <input type="hidden" name="created" data-value="" value="">

    <div id="titleSuggest" class='panel hidden widget-suggest panel-default'>
        <div class="panel-body">
            <p>
                <strong>这些问题可能有你需要的答案</strong>
                <button type="button" class="widget-suggest__close btn btn-default btn-xs">关闭提示</button>
            </p>
            <ul id="qList" class="list-unstyled widget-suggest__list">
            </ul>
        </div>
    </div>

    <div class="form-group">
        <label for="tags" class="sr-only">标签：至少1个，最多5个</label>
        <input class="tagsInput form-control"
               data-tags="null" name="tags" type="text"
               placeholder="标签，如：php" data-role="tagsinput"/>
    </div>


    <div id="questionText" class="editor">
        <textarea id="myEditor" class="hidden"></textarea>
    </div>

    <div class="operations mt20">
        <div class="pull-right">

            <span class="text-muted hidden" id="editorStatus">已保存</span>
            <a id="dropIt" href="javascript:void(0);" class="mr10 hidden">
                [舍弃]
            </a>
            <button data-type="question" id="publishIt" class="btn btn-primary btn-lg ml10" data-id=""
                    data-do="" data-url="" data-text="发布问题"
                    data-name=""/>
            发布问题
            </button>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
