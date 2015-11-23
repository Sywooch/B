<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 15:22
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use Yii;
use common\models\AnswerVersion;
use yii\db\ActiveRecord;

class AnswerVersionEntity extends AnswerVersion
{
    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
        ];
    }

    public static function addNewVersion($answer_id, $content, $reason)
    {
        if (self::ensureExistTheFirstEdition($answer_id) === false) {
            return Error::set(Error::TYPE_ANSWER_ENSURE_EXIST_THE_FIRST_EDITION);
        }

        $model = new self;
        if ($model->load(
                [
                    'answer_id' => $answer_id,
                    'content'   => $content,
                    'reason'    => $reason,
                ],
                ''
            ) && $model->save()
        ) {
            $result = true;
        } else {
            Yii::error(sprintf('%s insert error', __FUNCTION__));
            Yii::error($model->getErrors());
            $result = false;
        }

        return $result;
    }

    private static function ensureExistTheFirstEdition($answer_id)
    {
        $result = false;
        if (!self::findOne(['answer_id' => $answer_id])) {
            $answer = AnswerEntity::findOne(['id' => $answer_id]);
            if ($answer) {
                $model = new self;
                if ($model->load(
                        [
                            'answer_id' => $answer->id,
                            'content'   => $answer->content,
                            'reason'    => null,
                            'create_by' => $answer->create_by,
                            'create_at' => $answer->create_at,
                        ],
                        ''
                    ) && $model->save()
                ) {
                    $result = true;
                } else {
                    Yii::error(sprintf('%s insert error', __FUNCTION__));
                    Yii::error($model->getErrors());
                }
            }
        } else {
            $result = true;
        }

        return $result;
    }

    public static function getAnswerVersionList($answer_id, $limit = 10, $offset = 0)
    {
        return self::find()->where(
            [
                'answer_id' => $answer_id,
            ]
        )->limit($limit)->offset($offset)->orderBy('id DESC')->asArray()->all();
    }
}