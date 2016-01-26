<?php
/**
 * 每个动态类型使用一种模板，　_feed_???.php
 * User: yuyj
 * Date: 1/14
 * Time: 11:55
 * @var $user_event_log_list array CacheUserEventModel
 * @var $user_event_list     array CacheUserEventModel
 */
use common\entities\UserEventLogEntity;
use common\helpers\StringHelper;
use common\helpers\TemplateHelper;
use common\models\CacheAnswerModel;

?>
<div class="widget-active clearfix">
    <h2 class="h4"><?= $user->count_answer ?> 个回答</h2>

    <div class="stream-list board border-top">
        <?php foreach ($data as $item): /* @var $item CacheAnswerModel */ ?>
            <section class="stream-list__item">
                <div class="qa-rank">
                    <div class="votes plus">
                        <?= $item->count_like - $item->count_hate ?>
                        <small>投票</small>
                    </div>
                </div>
                <div class="summary">
                    <p class="text-muted mb0"><?= TemplateHelper::showHumanTime($item->created_at) ?></p>

                    <h2 class="title">
                        <?= $item->question->subject ?>
                    </h2>
                    <ul class="taglist--inline ib">
                        <?= TemplateHelper::showTagLiLabelByName($item->question->tags) ?>
                    </ul>
                    <p class="text-muted mb0"> <?= TemplateHelper::truncateString($item->content, 94) ?></p>
                </div>
            </section>
        <?php endforeach; ?>
    </div>