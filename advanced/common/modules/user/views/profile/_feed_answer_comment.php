<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/14
 * Time: 21:17
 * @var $user_event_log_list array CacheUserEventModel
 * @var $user_event_list     array CacheUserEventModel
 */
use common\helpers\TemplateHelper;
use common\services\AnswerService;
use common\services\QuestionService;
use yii\helpers\Html;

$question = QuestionService::getQuestionByQuestionId($item['associate_data']['question_id']);
$answer = AnswerService::getAnswerByAnswerId($item['associate_data']['answer_id']);

?>

<div class="widget-active--left">
    <span class="glyphicon glyphicon-question-sign"></span>
</div>
<div class="widget-active--right">
    <p class="widget-active--right__info"><?= TemplateHelper::showHumanTime($item['created_at'])
        ?><?= $user_event_list[$item['user_event_id']]['name'] ?>
        <small class="ml10 glyphicon glyphicon-comment"></small>
        0
    </p>
    <div class="widget-active--right__title">
        <h4><?= Html::a($question->subject, ['question/view', 'id' => $question->id]) ?></h4>
        <ul class="taglist--inline ib">
            <?= TemplateHelper::showTagLiLabelByName($question->tags) ?>
        </ul>
    </div>
</div>
