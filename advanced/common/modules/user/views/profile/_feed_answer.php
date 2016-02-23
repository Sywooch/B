<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/14
 * Time: 21:17
 * @var                    $user_event_list     array CacheUserEventModel
 * @var UserEventLogEntity $item
 */
use common\entities\UserEventLogEntity;
use common\helpers\TemplateHelper;
use common\services\AnswerService;
use common\services\QuestionService;
use yii\helpers\Html;

$question = QuestionService::getQuestionByQuestionId($item->associate_data['question_id']);
$answer = AnswerService::getAnswerByAnswerId($item->associate_id)
?>
<section class="widget-active__answer">
    <div class="widget-active--left">
        <span class="glyphicon glyphicon-pencil"></span>
    </div>
    <div class="widget-active--right">
        <p class="widget-active--right__info"><?= TemplateHelper::showHumanTime(
                $item->created_at
            ) ?>

            <?php if ($user_event_list[$item->user_event_id]['event_template'] && !empty($item->associate_data['event_template'])): ?>
                <?= vsprintf($user_event_list[$item->user_event_id]['event_template'], $item->associate_data['event_template']); ?>
            <?php else: ?>
                <?= $user_event_list[$item->user_event_id]['name']; ?>
            <?php endif; ?>
            <small class="ml10 glyphicon glyphicon-comment" title="回答数"></small> <?= $question->count_answer ?>
        </p>
        <div class="widget-active--right__title">
            <h4><?= Html::a(
                    $question->subject,
                    [
                        '/question/view',
                        'id'        => $question->id,
                        'answer_id' => $item->associate_id,
                    ]
                ) ?></h4>
            <ul class="taglist--inline ib">
                <?= TemplateHelper::showTagLiLabelByName($question->tags) ?>
            </ul>
        </div>
        <div class="widget-active--right__quote">
            <?= TemplateHelper::truncateString($answer->content, 94) ?>
        </div>
    </div>
</section>