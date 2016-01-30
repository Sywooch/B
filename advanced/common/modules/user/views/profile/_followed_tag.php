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
    <h2 class="h4">关注 3 个标签</h2>

    <div class="clearfix stream-list border-top">
        <div class="media stream-list__item">
            <a href="/t/php" class="pull-left">
                <img src="http://sfault-avatar.b0.upaiyun.com/188/805/188805810-1040000000089387_big64"
                     alt="php"
                     class="media-object avatar-40">
            </a>

            <div class="media-body pull-left">
                <h4 class="h5 media-heading">
                    <a href="/t/php">php</a>
                </h4>

                <div class="media-content text-muted">
                    2598 关注者
                </div>

            </div>
        </div>

        <div class="media stream-list__item">
            <a href="/t/%E7%A8%8B%E5%BA%8F%E5%91%98" class="pull-left">
                <img src="http://sfault-avatar.b0.upaiyun.com/323/737/3237371165-1040000000089556_big64"
                     alt="程序员"
                     class="media-object avatar-40">
            </a>

            <div class="media-body pull-left">
                <h4 class="h5 media-heading">
                    <a href="/t/%E7%A8%8B%E5%BA%8F%E5%91%98">程序员</a>
                </h4>

                <div class="media-content text-muted">
                    817 关注者
                </div>
            </div>
        </div>

        <div class="media stream-list__item">
            <div class="media-body pull-left">
                <h4 class="h5 media-heading">
                    <a href="/t/yii2">yii2</a>
                </h4>

                <div class="media-content text-muted">
                    44 关注者
                </div>

            </div>
        </div>
    </div>
</div>