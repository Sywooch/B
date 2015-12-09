<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/12/6
 * Time: 1:08
 */
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var $count_useful */
/** @var $id */
?>

<?php Pjax::begin(
        [
                'enablePushState' => false,
                'id'              => 'question-answer-vote',
        ]
); ?>
<?= Html::a(
        '<span class="sr-only">答案对我有帮助，有参考价值</span>',
        ['answer/vote', 'id' => $id, 'choose' => 'like'],
        [
                'class'           => 'like',
                'title'           => '答案对我有帮助，有参考价值',
                'data-need-login' => true,
        ]
) ?>
<span class="count"><?= $count_useful; ?></span>
<?= Html::a(
        '<span class="sr-only">答案没帮助，是错误的答案，答非所问</span>',
        ['question/vote', 'id' => $id, 'choose' => 'hate'],
        [
                'class'           => 'hate',
                'title'           => '答案没帮助\是错误的答案\答非所问\故意捣乱',
                'data-need-login' => true,
        ]
) ?>
<?php Pjax::end(); ?>
