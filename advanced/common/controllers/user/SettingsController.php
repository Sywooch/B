<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\controllers\user;

use common\models\Area;
use dektrium\user\controllers\SettingsController as BaseSettingsController;

class SettingsController extends BaseSettingsController
{
    /**
     * @return mixed
     */
    public function actionProfile()
    {
        $model = $this->finder->findProfileById(\Yii::$app->user->identity->getId());

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {


            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Your profile has been updated'));
            return $this->refresh();
        }


        $provinces = Area::find()->where(['grade' => 1])->asArray()->all();

        $cities = $model->province ? Area::find()->Where([
                'grade'     => 2,
                'parent_id' => $model->province
        ])->asArray()->all() : [];

        $districts = $model->province ? Area::find()->Where([
                'grade'     => 3,
                'parent_id' => $model->city
        ])->asArray()->all() : [];

        return $this->render('profile', [
                'model'     => $model,
                'provinces' => $provinces,
                'cities'    => $cities,
                'districts'    => $districts,
        ]);
    }
}