<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/14
 * Time: 15:53
 */

namespace common\entities;

use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\exceptions\ModelSaveErrorException;
use common\models\QuestionVersion;
use ReflectionClass;
use Yii;
use yii\db\ActiveRecord;

class QuestionVersionEntity extends QuestionVersion
{
    const QUESTION_CHANGE_TYPE_CREATE = 1;
    const QUESTION_CHANGE_TYPE_UPDATE_SUBJECT = 2;
    const QUESTION_CHANGE_TYPE_UPDATE_CONTENT = 4;
    const QUESTION_CHANGE_TYPE_ADD_TAGS = 8;
    const QUESTION_CHANGE_TYPE_REMOVE_TAGS = 16;

    private $_change_type_list = [
        1  => '添加问题',
        2  => '修改问题标题',
        4  => '补充问题内容',
        8  => '添加标签',
        16 => '删除标签',
    ];

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
            'ip'        => [
                'class' => IpBehavior::className(),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(QuestionEntity::className(), ['id' => 'question_id']);
    }

    public function getChangeList($change_type)
    {
        $result = [];
        foreach ($this->_change_type_list as $key => $value) {
            if (($change_type & $key) == $key) {
                $result[] = $this->_change_type_list[$key];
            }
        }

        return implode('、', $result);
    }
}
