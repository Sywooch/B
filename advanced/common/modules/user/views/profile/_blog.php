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
    <h2 class="h4 mt20 mb20">关注的专栏文章</h2>

    <div class="stream-list blog-stream border-top board">
        <section class="stream-list__item">
            <h3 class="blog-title">
                <a href="/blog/root"># cd /
                </a>
            </h3>

            <p class="blog-desc wordbreak"></p>

            <div>
                1
                <span class="text-muted">位作者 · </span>
                65
                <span class="text-muted">篇文章</span>
            </div>
        </section>

        <section class="stream-list__item">
            <h3 class="blog-title">
                <a href="/blog/jimmy_thr">前端的那些事
                </a>
            </h3>

            <p class="blog-desc wordbreak"></p>

            <div>
                1
                <span class="text-muted">位作者 · </span>
                15
                <span class="text-muted">篇文章</span>
            </div>
        </section>

    </div>
</div>