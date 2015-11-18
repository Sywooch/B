<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:59
 */

namespace frontend\controllers;

use Yii;
use common\models\Area;
use common\controllers\BaseController;

class AreaController extends BaseController
{

    public function actionIndex()
    {
        echo 'test1';
    }

    /**
     * 获取城市
     * @param $province_id
     */
    public function actionCity($province_id)
    {
        $count = Area::find()->where(
            [
                'grade'     => 2,
                'parent_id' => $province_id,
            ]
        )->count();
        $data = Area::find()->where(
            [
                'grade'     => 2,
                'parent_id' => $province_id,
            ]
        )->all();

        $cities = [];
        if ($count > 0) {
            $cities[] = '<option value="0">请选择市</option>';
            foreach ($data as $item) {
                $cities[] = "<option value='" . $item->area_id . "'>" . $item->name . "</option>";
            }
        }

        echo implode('', $cities);
    }

    /**
     * 获取区域
     * @param $city_id
     */
    public function actionDistricts($city_id)
    {
        $count = Area::find()->where(
            [
                'grade'     => 3,
                'parent_id' => $city_id,
            ]
        )->count();
        $data = Area::find()->where(
            [
                'grade'     => 3,
                'parent_id' => $city_id,
            ]
        )->all();

        $districts = [];
        if ($count > 0) {

            $districts[] = '<option value="0">请选择区</option>';
            foreach ($data as $item) {
                $districts[] = "<option value='" . $item->area_id . "'>" . $item->name . "</option>";
            }
        }

        echo implode('', $districts);
    }
}