<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 14:12
 */

namespace common\models\user;

use \dektrium\user\models\User as BaseUser;

class User extends BaseUser
{

    public function attributeLabels()
    {
        $attributes = parent::attributeLabels();
        return ArrayHelper::merge($attributes, [
                'last_login_at' => \Yii::t('user', 'Last login at'),
                'login_times'   => \Yii::t('user', 'Login times'),
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
        //$rules['fieldLength']   = ['field', 'string', 'max' => 10];

        return $rules;
    }
}