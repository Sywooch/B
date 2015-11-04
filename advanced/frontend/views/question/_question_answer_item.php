<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/31
 * Time: 13:09
 */
use common\helpers\TemplateHelper;
use yii\widgets\LinkPager;

//print_r($data);exit;
?>
<?php \yii\widgets\Pjax::begin(
        [
                'timeout'       => 10000,
                'clientOptions' => [
                        'container' => 'pjax-container',
                ],
            'options' => [
                'id' => 'answer_item_area'
            ]
        ]
); ?>
<?php foreach ($data as $item): ?>
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
            <?= TemplateHelper::showUserAvatar($item['create_by'], 24, true, $item['is_anonymous']) ?>
            <strong><?= TemplateHelper::showUsername($item['create_by'], true, $item['is_anonymous']) ?></strong>

        <span class="ml10 text-muted">
            <?= TemplateHelper::showhumanTime($item['create_at']) ?>
                    </span>

            <div class="answer fmt mt10 mb10">
                <?= $item['content']; ?>
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
<?php endforeach; ?>

<?= $pages ? LinkPager::widget(['pagination' => $pages]) : ''; ?>
<?php \yii\widgets\Pjax::end(); ?>