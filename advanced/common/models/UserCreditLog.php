<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_credit_log".
 *
 * @property string $id
 * @property string $event
 * @property integer $score
 * @property integer $currency
 * @property string $created_at
 * @property string $created_by
 */
class UserCreditLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_credit_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event'], 'required'],
            [['score', 'currency', 'created_at', 'created_by'], 'integer'],
            [['event'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'score' => '积分（声誉度）',
            'currency' => '货币',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
