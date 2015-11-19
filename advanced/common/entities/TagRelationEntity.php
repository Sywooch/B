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
    
}