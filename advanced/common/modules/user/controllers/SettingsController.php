<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

use common\exceptions\ModelSaveErrorException;
use common\models\Area;
use common\modules\user\models\AvatarForm;
use dektrium\user\controllers\SettingsController as BaseSettingsController;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SettingsController extends BaseSettingsController
{

    /**
     * 通过行为控制访问权限
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'profile',
                            'account',
                            'avatar',
                            'confirm',
                            'networks',
                            'disconnect'
                        ],
                        'roles' => ['@']
                    ],
                ]
            ],
        ];
    }

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
            'grade' => 2,
            'parent_id' => $model->province
        ])->asArray()->all() : [];

        $districts = $model->province ? Area::find()->Where([
            'grade' => 3,
            'parent_id' => $model->city
        ])->asArray()->all() : [];

        return $this->render('profile', [
            'model' => $model,
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
        ]);
    }

    /**
     *  头像设置
     * @return mixed
     */
    public function actionAvatar()
    {
        /** @var SettingsForm $model */
        $model = Yii::createObject(AvatarForm::className());

        if ($model->load(Yii::$app->request->post())) {
            #删除头像
            if ($model->user->profile->avatar) {
                $model->deleteOldAvatar();
            }

            #上传图片
            $image = $model->uploadAvatar();

            if ($model->save()) {
                if ($image !== false) {
                    $path = $model->getImageFile();
                    $image->saveAs($path);
                }
                Yii::$app->session->setFlash('success', '您的头像已修改成功');
                return $this->refresh();
            }else{
                throw new ModelSaveErrorException($model);
            }
        }

        return $this->render('avatar', [
            'model' => $model,
        ]);
    }
}