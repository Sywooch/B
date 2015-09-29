<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 14:12
 */

namespace common\modules\user\models;

use common\helpers\AvatarHelper;
use \dektrium\user\models\Profile as BaseProfile;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class Profile extends BaseProfile
{
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    public function attributeLabels()
    {
        $attributes = parent::attributeLabels();
        return ArrayHelper::merge($attributes, [
                'nickname'  => \Yii::t('user', 'Nickname'),
                'title'     => \Yii::t('user', 'Title'),
                'sex'       => \Yii::t('user', 'Sex'),
                'birthday'  => \Yii::t('user', 'Birthday'),
                'avatar'    => \Yii::t('user', 'Avatar'),
                'province'  => \Yii::t('user', 'Province'),
                'city'      => \Yii::t('user', 'City'),
                'district'  => \Yii::t('user', 'District'),
                'address'   => \Yii::t('user', 'Address'),
                'longitude' => \Yii::t('user', 'Longitude'),
                'latitude'  => \Yii::t('user', 'Latitude'),
        ]);
    }

    /**
     * 场景约束
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        //$scenarios['create'][]   = 'field';
        //$scenarios['update'][]   = 'field';
        //$scenarios['register'][] = 'field';
        return $scenarios;
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        //$rules['fieldRequired'] = ['field', 'required'];
        $rules['pcd'] = [['province', 'city', 'district'], 'integer', 'integerOnly' => true];
        $rules['sexIn'] = ['sex', 'in', 'range' => ['男', '女', '保密']];
        $rules['birthday'] = ['birthday', 'date', 'format' => 'php:Y/m/d'];
        $rules['addressLength'] = ['address', 'string', 'max' => '80'];
        $rules['avatarLength'] = ['avatar', 'string', 'max' => '255'];

        return $rules;
    }


}