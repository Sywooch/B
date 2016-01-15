<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/12/6
 * Time: 1:08
 */
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var $count_follow */
/** @var $id */
/** @var $is_followed */
?>
<?php Pjax::begin(
    [
        'enablePushState' => false,
        'id'              => 'user-follow-pjax',
        'timeout'         => 10000,
    ]
); ?>
    <strong>
        <?= Html::a(
            $count_fans,
            [''],
            [
                'class' => 'funsCount',
            ]
        ); ?>
    </strong> 个粉丝
<?= Html::a(
    $is_followed ? '取消关注' : '关注',
    ['/user/default/follow', 'user_id' => $id],
    [
        'id'              => 'sideFollow',
        'class'           => 'btn btn-success btn-sm',
        'title'           => '关注后将获得更新提醒',
        'data-need-login' => true,
        'data-do'         => 'pjax',
    ]
) ?>
<?php Pjax::end(); ?>