<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/12/6
 * Time: 1:08
 */
use common\entities\VoteEntity;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var $count_useful */
/** @var $id */
?>

<?php Pjax::begin(
    [

        'id'              => 'answer-comment-vote-' . $id,
        'enablePushState' => false,
        'timeout'         => 10000,
    ]
); ?>
<?= Html::a(
    '<span class="sr-only">答案对我有帮助，有参考价值</span>',
    ['answer-comment/vote', 'id' => $id, 'vote' => VoteEntity::VOTE_YES],
    [
        'class'           => 'like' . ($vote_status == VoteEntity::VOTE_YES ? ' active' : ''),
        'title'           => '评论对我有帮助',
        'data-need-login' => true,
        'data-do'         => 'pjax',
    ]
) ?>
<?php if ($count_vote): ?>
    <span class="count"><?= $count_vote; ?></span>
<?php endif; ?>
<?php Pjax::end(); ?>
