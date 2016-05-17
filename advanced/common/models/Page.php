<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "page".
 *
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $count_views
 * @property string $view
 * @property string $status
 */
class Page extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content', 'status'], 'string'],
            [['count_views'], 'integer'],
            [['title', 'slug', 'view'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'slug' => '别名',
            'content' => '内容',
            'count_views' => '查看数',
            'view' => '模板名称',
            'status' => '状态',
        ];
    }
}
