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

?>
<div class="widget-active clearfix">
    <h2 class="h4">最近动态</h2>
    <?php foreach ($user_event_log_list as $item): /* @var $item UserEventLogEntity */ ?>
        <?= $this->render(
            '_feed_' . $item['associate_type'],
            [
                'user_event_list' => $user_event_list,
                'item'            => $item,
            ]
        ); ?>
    <?php endforeach; ?>
</div>