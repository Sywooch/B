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

/** @var $count_vote */
/** @var $id */
/** @var $vote_status */
?>

<?php Pjax::begin(
    [
        'enablePushState' => false,
        'id'              => 'question-vote-pjax',
    ]
); ?>
<?= Html::a(
    '<span class="sr-only">问题对人有帮助，内容完整，我也想知道答案</span>',
    ['question/vote', 'id' => $id, 'vote' => VoteEntity::VOTE_YES],
    [
        'class'           => 'like' . ($vote_status == VoteEntity::VOTE_YES ? ' active' : ''),
        'title'           => '问题对人有帮助，内容完整，我也想知道答案',
        'data-need-login' => true,
    ]
) ?>
<span class="count"><?= $count_vote; ?></span>
<?= Html::a(
    '<span class="sr-only">问题没有实际价值，缺少关键内容，没有改进余地</span>',
    ['question/vote', 'id' => $id, 'vote' => VoteEntity::VOTE_NO],
    [
        'class'           => 'hate' . ($vote_status == VoteEntity::VOTE_NO ? ' active' : ''),
        'title'           => '问题没有实际价值，缺少关键内容，没有改进余地',
        'data-need-login' => true,
    ]
) ?>
<?php Pjax::end(); ?>
