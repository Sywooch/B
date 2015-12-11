<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/9
 * Time: 20:22
 */
use yii\widgets\LinkPager;

?>
<?php \yii\widgets\Pjax::begin(['timeout' => 10000, 'clientOptions' => ['container' => 'pjax-container']]); ?>
<?php foreach ($answer_version_model as $item): ?>


<?php endforeach; ?>
<?php \yii\widgets\Pjax::end(); ?>
<?= $pagination ? LinkPager::widget(['pagination' => $pagination]) : '' ?>