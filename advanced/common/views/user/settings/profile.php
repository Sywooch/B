<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\Profile $profile
 */

$this->title = Yii::t('user', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

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
                <?php $form = \yii\widgets\ActiveForm::begin([
                        'id'                     => 'profile-form',
                        'options'                => ['class' => 'form-horizontal'],
                        'fieldConfig'            => [
                                'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                                'labelOptions' => ['class' => 'col-lg-3 control-label'],
                        ],
                        'enableAjaxValidation'   => true,
                        'enableClientValidation' => false,
                        'validateOnBlur'         => false,
                ]); ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'sex')->radioList(['男'=>'男', '女'=>'女', '保密'=>'保密'], [
                ]) ?>

                <?= $form->field($model, 'website') ?>


                <?= $form->field($model, 'province')->dropDownList(ArrayHelper::map($provinces, 'area_id', 'name'), [
                        'prompt'   => '请选择省',
                        'onchange' => '
            $.post("index.php?r=area/c&province_id=' . '"+$(this).val(),function(data){
                $("select#profile-city").html(data);
                $("select#profile-district").html("");
            });',
                ]) ?>

                <?= $form->field($model, 'city')->dropDownList(ArrayHelper::map($cities, 'area_id', 'name'), [
                        'prompt'   => '请选择市',
                        'onchange' => '
            $.post("index.php?r=area/d&city_id=' . '"+$(this).val(),function(data){
                $("select#profile-district").html(data);
            });',
                ]) ?>

                <?= $form->field($model, 'district')->dropDownList(ArrayHelper::map($districts, 'area_id', 'name'),
                        ['prompt' => '请选择区']) ?>


                <?= $form->field($model, 'bio')->textarea() ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Save'),
                                ['class' => 'btn btn-block btn-success']) ?>
                        <br>
                    </div>
                </div>

                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


