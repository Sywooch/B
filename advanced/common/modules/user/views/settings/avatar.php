<?php
/**
 * @Author: forecho
 * @Date:   2015-01-29 23:23:12
 * @Last Modified by:   forecho
 * @Last Modified time: 2015-01-30 22:56:49
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use bupy7\cropbox\Cropbox;

$this->title = Yii::t('app', 'Avatar');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(
                        [
                                'id'      => 'account-form',
                                'options' => ['enctype' => 'multipart/form-data'],
                        ]
                ); ?>
                <?= Html::img($model->user->getAvatar(200, true)); ?>
                <?= Html::img($model->user->getAvatar(50, true)); ?>
                <?= Html::img($model->user->getAvatar(24, true)); ?>
                <br>
                <br>
                <?= $form->field($model, 'avatar')->fileInput(); ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('yii', 'Update'), ['class' => 'btn btn-success']) ?><br>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
