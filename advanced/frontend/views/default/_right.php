<?php
use common\helpers\ServerHelper;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\TagService;
use common\services\UserService;
use yii\helpers\Html;

//标签
$tags = TagService::getHotTag(20, 100);

//关注标签
if (!Yii::$app->user->isGuest) {
    $user = UserService::getUserById(Yii::$app->user->id);
    $follow_tag_ids = FollowService::getUserFollowTagIds(Yii::$app->user->id);
    $follow_tags = TagService::getTagListByTagIds($follow_tag_ids);
    $follow_tag_count = $user['count_follow_tag'];
} else {
    $follow_tags = [];
    $follow_tag_count = 0;
}

//热门问题
$question_hottest = QuestionService::fetchHot(15, 0, ServerHelper::checkIsSpider(), 30);

?>

<aside class="widget-welcome">
    <h2 class="h4 title">深圳本地社区</h2>

    <p>最前沿的技术问答，最纯粹的技术切磋。让你不知不觉中开拓眼界，提高技能，认识更多朋友。</p>
</aside>


<?php
if ($follow_tags): ?>
    <div class="widget-box">
        <h2 class="h4 widget-box__title">关注 <?= Html::a($follow_tag_count, ['user']) ?> 个标签</h2>
        <ul class="taglist--inline multi">
            <?php foreach ($follow_tags as $follow_tag) : ?>
                <li class="tagPopup">
                    <?= Html::a(
                        $follow_tag['name'],
                        ['tag/view', 'id' => $follow_tag['id']],
                        [
                            'class' => 'tag',
                        ]
                    ); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($tags): ?>
    <div class="widget-box">
        <h2 class="h4 widget-box__title">热门标签 <a href="/tags" title="更多">&raquo;</a></h2>
        <ul class="taglist--inline multi">
            <?php foreach ($tags as $tag): ?>
                <li class="tagPopup">
                    <?= Html::a(
                        $tag['name'],
                        ['tag/view', 'id' => $tag['id']],
                        [
                            'class' => 'tag',
                        ]
                    ); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($question_hottest): ?>
    <div class="widget-box">
        <h2 class="h4 widget-box__title">最近热门的</h2>
        <ul class="widget-links list-unstyled">
            <?php foreach ($question_hottest as $question): ?>
                <li class="widget-links__item">
                    <?= Html::a(
                        $question['subject'],
                        ['question/view', 'id' => $question['id']]
                    ) ?>
                    <small class="text-muted">
                        <?= $question['count_answer']; ?> 回答
                    </small>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
        