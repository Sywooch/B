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
    '',
    ['answer-comment/vote', 'id' => $id, 'vote' => VoteEntity::VOTE_YES],
    [
        'class'           => 'like' . ($vote_status == VoteEntity::VOTE_YES ? ' active' : ''),
        'title'           => '评论对我有帮助',
        'data'  => [
            'do'    => 'pjax',
            'need-login'    => true,
        ],
    ]
) ?>
<?php if ($count_vote): ?>
    <span class="count"><?= $count_vote; ?></span>
<?php endif; ?>
<?php Pjax::end(); ?>
