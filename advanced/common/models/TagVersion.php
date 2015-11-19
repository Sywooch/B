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
 * @property integer $create_by
 * @property string $create_at
 *
 * @property Tag $tag
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
            [['tag_id', 'content', 'create_by'], 'required'],
            [['tag_id', 'create_by', 'create_at'], 'integer'],
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
            'create_by' => '创建用户',
            'create_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
    }
}
