<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/12/6
 * Time: 1:08
 */
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var $count_views */
/** @var $count_favorite */
/** @var $is_favorite */
/** @var $id */
?>
<?php Pjax::begin(
    [
        'id'              => 'question-favorite-pjax',
        'enablePushState' => false,
        'timeout'         => 10000,
    ]
); ?>

<?= Html::a(
    $is_favorite ? '取消收藏' : '收藏',
    ['question/favorite', 'question_id' => $id],
    [
        'id'              => 'sideBookmark',
        'class'           => 'btn btn-default btn-sm',
        'data-need-login' => true,
        'data-do'         => 'pjax',
    ]
) ?>
    <strong><?= $count_favorite ?></strong> 收藏，
<strong class="no-stress"><?= $count_views ?></strong> 浏览
<?php Pjax::end(); ?>
