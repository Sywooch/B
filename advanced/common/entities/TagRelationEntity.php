<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/19
 * Time: 19:11
 */

namespace common\entities;

use common\models\TagRelation;

class TagRelationEntity extends TagRelation
{

    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';

    const TYPE_PARENT = 'parent';
    const TYPE_BROTHER = 'brother';
    const TYPE_CHILD = 'child';
    const TYPE_ALIAS = 'alias';
    const TYPE_RELATE = 'relate';

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagId1()
    {
        return $this->hasOne(TagEntity::className(), ['id' => 'tag_id_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagId2()
    {
        return $this->hasOne(TagEntity::className(), ['id' => 'tag_id_2']);
    }
}
