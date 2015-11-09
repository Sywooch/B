<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/11/8
 * Time: 13:17
 */
use common\helpers\TemplateHelper;

?>

<div class="widget-comments in">
    <? foreach ($comments_data as $index => $item): ?>
        <div class="widget-comments__item hover-show" id="1050000003912333">
            <div class="votes widget-vote">
                <button class="like "
                        data-id="1050000003912333"
                        type="button"
                        data-do="like"
                        data-type="comment"></button>
                <span class="count">&nbsp;</span></div>
            <div class="comment-content wordbreak">
                <div class="content fmt">
                    <?= $item['content']; ?>
                </div>

                <p class="comment-meta">
                    <a href="/c/1050000003912333"
                       class="text-muted">#<?= $index ?></a>&nbsp;
                    <?= TemplateHelper::showUsername($item['create_by']) ?>· <span class="createdDate">
                        <?= TemplateHelper::showhumanTime(
                                $item['create_at']
                        ) ?></span> ·
                    <a href="#"
                       class="commentReply"
                       data-userid="1030000002644202"
                       data-id="1050000003912333"
                       data-username="青龙道人">回复</a>
                <span class="pull-right commentTools hover-show-obj">                                                <a
                            href="#911"
                            class="ml10"
                            data-toggle="modal"
                            data-target="#911"
                            data-type="comment"
                            data-id="1050000003912333"
                            data-typetext="评论"
                            data-placement="top"
                            title="举报">举报</a>            </span></p></div>
        </div>
    <? endforeach; ?>

    <div class="widget-comments__form row">
        <form class="col-md-10 col-xs-12">
            <div class="form-group mb0">
                <input name="id" type="hidden" value="1020000003912194">
                <textarea name="text"
                          class="form-control"
                          id="commentText-1020000003912194"
                          data-id="1020000003912194"
                          placeholder="添加评论"
                          style="overflow: hidden; word-wrap: break-word; height: 28px;"></textarea>
            </div>
        </form>
        <div class="col-md-2 col-xs-12">
            <button type="submit"
                    class="btn btn-primary btn-sm btn-block postComment m-mt15"
                    data-id="1020000003912194">提交评论
            </button>
            <div class="mt10"><a href="javascript:void(0);" class="toggle-comment-helper">语法提示</a></div>
        </div>
        <div class="col-md-10 col-xs-12 fmt comment-helper" data-rank="203" style="display:none;">
            <div class="alert alert-warning mb10 mt10">评论支持部分 Markdown 语法：<code>**bold**</code> <code>_italic_</code>
                <code>[link](http://example.com)</code> <code>&gt; 引用</code> <code>`code`</code> <code>- 列表</code>。<br>同时，被你
                                                       @ 的用户也会收到通知
            </div>
        </div>

    </div>
    <!-- /.widget-comments__form -->
</div>
