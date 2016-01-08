<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/14
 * Time: 17:56
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\exceptions\ModelSaveErrorException;
use common\models\Attachment;
use Yii;
use yii\db\ActiveRecord;

class AttachmentEntity extends Attachment
{

    const TEMP_FILE_MATCH_REGULAR = '/src=\"(\/uploads\/tmp_attachments.+?)\"/is'; #匹配临时文件资源地址的正则
    const TEMP_ATTACHMENT_PATH = 'tmp_attachments';
    const ATTACHMENT_PATH = 'attachment';


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
     * @param $question_id
     * @param $user_id
     * @param $file_path
     * @param $file_size
     */
    public function addQuestionAttachment($question_id, $user_id, $file_path, $file_size)
    {
        $model = clone $this;
        if ($model->load(
                [
                    'associate_type' => 'question',
                    'associate_id'   => $question_id,
                    'file_location'  => $file_path,
                    'file_size'      => $file_size,
                    'created_by'     => $user_id,
                ],
                ''
            ) && $model->save()
        ) {
            throw new ModelSaveErrorException($model);
        }
    }

    public function getAttachmentsByQuestionId($question_id)
    {
        return self::findAll(['associate_id' => $question_id]);
    }

    public function moveAttachmentFile($question_id, $old_file_physical_path, $new_file_physical_path)
    {
        $new_file_physical_dir = dirname($new_file_physical_path);
        #check dir exist
        if (!file_exists($new_file_physical_dir)) {
            mkdir($new_file_physical_dir, 0755, true);
        }
        #move file
        if (!rename($old_file_physical_path, $new_file_physical_path)) {
            $this->moveFileErrorAlert(
                $question_id,
                $old_file_physical_path,
                $new_file_physical_path
            );
        }

    }


    public function moveFileErrorAlert($question_id, $old_file_path, $new_file_path)
    {
        Yii::$app->mailer->compose()->setFrom(Yii::$app->params['senderEmail'])->setTo(
            Yii::$app->params['adminEmail']
        )->setSubject('文件移动出错啦')->setHtmlBody(
            sprintf(
                '<p>文章ID：%d</p><p>旧文件路径：%s</p><p>新文件路径：%s</p>',
                $question_id,
                $old_file_path,
                $new_file_path
            )
        )->send();
    }
}
