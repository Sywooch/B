<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/11
 * Time: 11:50
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\models\TagVersion;
use yii\db\ActiveRecord;

class TagVersionEntity extends TagVersion
{

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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(TagEntity::className(), ['id' => 'tag_id']);
    }
}
