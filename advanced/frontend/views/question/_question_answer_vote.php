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
        'enablePushState' => false,
        'id'              => 'question-answer-vote-' . $id,
    ]
); ?>
<?= Html::a(
    '<span class="sr-only">答案对我有帮助，有参考价值</span>',
    ['answer/vote', 'id' => $id, 'vote' => VoteEntity::VOTE_YES],
    [
        'class'           => 'like' . ($vote_status == VoteEntity::VOTE_YES ? ' active' : ''),
        'title'           => '答案对我有帮助，有参考价值',
        'data-need-login' => true,
    ]
) ?>
<span class="count"><?= $count_vote; ?></span>
<?= Html::a(
    '<span class="sr-only">答案没帮助，是错误的答案，答非所问</span>',
    ['answer/vote', 'id' => $id, 'vote' => VoteEntity::VOTE_NO],
    [
        'class'           => 'hate' . ($vote_status == VoteEntity::VOTE_NO ? ' active' : ''),
        'title'           => '答案没帮助\是错误的答案\答非所问\故意捣乱',
        'data-need-login' => true,
    ]
) ?>
<?php Pjax::end(); ?>
