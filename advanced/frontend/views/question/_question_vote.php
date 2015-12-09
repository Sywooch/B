<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/12/6
 * Time: 1:08
 */
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var $count_like */
/** @var $id */
?>

<?php Pjax::begin(
        [
                'enablePushState' => false,
                'id'              => 'question-vote-pjax',
        ]
); ?>
<?= Html::a(
        '<span class="sr-only">问题对人有帮助，内容完整，我也想知道答案</span>',
        ['question/vote', 'id' => $id, 'choose' => 'like'],
        [
                'class' => 'like',
                'title' => '问题对人有帮助，内容完整，我也想知道答案',
                'data-need-login' => true,
        ]
) ?>
<span class="count"><?= $count_like; ?></span>
<?= Html::a(
        '<span class="sr-only">问题没有实际价值，缺少关键内容，没有改进余地</span>',
        ['question/vote', 'id' => $id, 'choose' => 'hate'],
        [
                'class' => 'hate',
                'title' => '问题没有实际价值，缺少关键内容，没有改进余地',
                'data-need-login' => true,
        ]
) ?>
<?php Pjax::end(); ?>
