<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 14:12
 */

namespace common\entities;

use common\behaviors\UserEventBehavior;
use yii\helpers\ArrayHelper;
use \dektrium\user\models\User;

use Yii;

/**
 * Class User
 * @package \common\modules\user\models
 * @property  \common\entities\UserProfileEntity $profile
 */
class UserEntity extends User
{
    const MAX_USERNAME_LENGTH = 15;//用户名长度
    const MIN_USERNAME_LENGTH = 2;//用户名长度

    public $avatar;
    public static $usernameRegexp = '/^[_\-a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u';//注册用户名正则，允许中英文

    public static function tableName()
    {
        return 'user';
    }
    
    public function attributeLabels()
    {
        $attributes = parent::attributeLabels();
        
        return ArrayHelper::merge(
            $attributes,
            [
                'last_login_at' => \Yii::t('user', 'Last login at'),
                'login_times'   => \Yii::t('user', 'Login times'),
            ]
        );
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

    public function transactions()
    {
        return [
            //注册场景，走事务
            'register' => self::OP_INSERT,
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    public function getProfile()
    {
        return $this->hasOne(UserProfileEntity::className(), ['user_id' => 'id']);
    }
    
    public function getQuestions()
    {
        return $this->hasMany(QuestionEntity::className(), ['created_by' => 'id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(AnswerEntity::className(), ['created_by' => 'id']);
    }
}