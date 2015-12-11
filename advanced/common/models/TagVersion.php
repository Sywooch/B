<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag_version".
 *
 * @property string $id
 * @property string $tag_id
 * @property string $content
 * @property string $reason
 * @property string $created_by
 * @property string $created_at
 */
class TagVersion extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'content'], 'required'],
            [['tag_id', 'created_by', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['reason'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_id' => 'Tag ID',
            'content' => 'Content',
            'reason' => 'Reason',
            'created_by' => '创建用户',
            'created_at' => '创建时间',
        ];
    }
}
