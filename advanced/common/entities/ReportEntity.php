<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/11
 * Time: 19:14
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\models\Report;
use yii\db\ActiveRecord;

class ReportEntity extends Report
{
    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            /*'tag'       => [
                'class' => TagBehavior::className(),
            ],*/
        ];
    }
}
