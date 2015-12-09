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
    public $avatar;
    #注册用户名正则，允许中英文
    public static $usernameRegexp = '/^[_-a-zA-Z0-9\.\x{4e00}-\x{9fa5}]+$/u';
    
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

    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'user_behavior' => [
                'class' => QuestionBehavior::className(),
            ],
        ];
        return $behaviors;
    }*/
    
    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        //$rules['fieldRequired'] = ['field', 'required'];
        //$rules['usernameLength']=['username', 'string', 'min' => 2, 'max' => 255];
        return $rules;
    }

    public function getProfile()
    {
        return $this->hasOne(UserProfileEntity::className(), ['user_id' => 'id']);
    }
    
    public function getQuestions()
    {
        return $this->hasMany(QuestionEntity::className(), ['create_by' => 'id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(AnswerEntity::className(), ['create_by' => 'id']);
    }
}