<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag_relation".
 *
 * @property string $tag_id_1
 * @property string $tag_id_2
 * @property string $type
 * @property integer $order
 * @property integer $count_relation
 * @property string $status
 *
 * @property Tag $tagId1
 * @property Tag $tagId2
 */
class TagRelation extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_relation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id_1', 'tag_id_2', 'type'], 'required'],
            [['tag_id_1', 'tag_id_2', 'order', 'count_relation'], 'integer'],
            [['type', 'status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id_1' => 'Tag Id 1',
            'tag_id_2' => 'Tag Id 2',
            'type' => 'parent父，chilid子，brother兄，alias假名',
            'order' => 'Order',
            'count_relation' => '关联次数，判断关联度',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagId1()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagId2()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id_2']);
    }
}
