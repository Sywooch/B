<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 15:22
 */

namespace common\entities;

use Yii;
use common\models\AnswerVersion;

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

    public function addNewVersion($answer_id, $content, $reason)
    {
        if ($this->ensureExistTheFirstEdition($answer_id)) {
            $model = clone $this;
            if ($model->load(
                    [
                        'answer_id' => $answer_id,
                        'content'   => $content,
                        'reason'    => $reason,
                    ],
                    ''
                ) && $model->save()
            ) {
                return true;
            } else {
                Yii::error(sprintf('%s insert error', __FUNCTION__));
                Yii::error($model->getErrors());
                return false;
            }
        }
    }

    private function ensureExistTheFirstEdition($answer_id)
    {
        if (!self::findOne(['answer_id' => $answer_id])) {
            $answer = AnswerEntity::findOne(['id' => $answer_id]);
            if ($answer) {
                $model = clone $this;
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
                    return true;
                } else {
                    Yii::error(sprintf('%s insert error', __FUNCTION__));
                    Yii::error($model->getErrors());

                    return false;
                }
            }
        }

    }
}