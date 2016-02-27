<?php

namespace common\services;

use common\entities\NotificationEntity;
use common\entities\NotificationDataEntity;
use common\exceptions\ModelSaveErrorException;
use common\models\AssociateDataModel;
use common\models\AssociateModel;
use yii\base\Exception;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use Yii;

class NotificationService extends BaseService
{
    const MAX_SENDER_NUMBER = 5; //每类通知最多显示多少用户

    public static function addNotification($sender, array $receivers, $user_event_id, $associate_type, $associate_id, AssociateDataModel $associate_data, $created_at)
    {
        Yii::trace(sprintf('添加站内通知 user_event_id:%d，通知用户： %s', $user_event_id, implode(',', $receivers)), 'behavior');

        $transaction = NotificationEntity::getDb()->beginTransaction();
        try {
            foreach ($receivers as $receiver) {
                //保存　notification　数据
                $notification_data = [
                    'receiver'       => $receiver,
                    'user_event_id'  => $user_event_id,
                    'associate_type' => $associate_type,
                    'associate_id'   => $associate_id,
                    'date'           => date('Y-m-d', $created_at),
                    'status'         => NotificationEntity::STATUS_UNREAD,
                ];

                $notification_model = NotificationEntity::find()->where(
                    [
                        'receiver'       => $receiver,
                        'user_event_id'  => $user_event_id,
                        'associate_type' => $associate_type,
                        'associate_id'   => $associate_id,
                        'date'           => date('Y-m-d', $created_at),
                    ]
                )->one();

                if (!$notification_model) {
                    $notification_model = new NotificationEntity();
                }

                //同类型通知数加+1
                $notification_model->count_notice = $notification_model->count_notice + 1;

                if ($notification_model->load($notification_data, '') && $notification_model->save()) {
                    $notification_id = $notification_model->id;
                } else {
                    $transaction->rollBack();
                    throw new ModelSaveErrorException($notification_model);
                }


                //同类型通知最多5条
                if ($notification_model->count_notice <= self::MAX_SENDER_NUMBER) {
                    //保存　notification_sender　数据
                    $notification_sender_data = [
                        'notification_id' => $notification_id,
                        'sender'          => $sender,
                        'associate_data'  => Json::encode(array_filter($associate_data->toArray())),
                        'created_at'      => $created_at,
                        'status'          => NotificationEntity::STATUS_UNREAD,
                    ];

                    $notification_data_model = NotificationDataEntity::find()->where(
                        [
                            'notification_id' => $notification_id,
                            'sender'          => $sender,
                        ]
                    )->one();

                    if (!$notification_data_model) {
                        $notification_data_model = new NotificationDataEntity();
                    }

                    if ($notification_data_model->load($notification_sender_data, '') &&
                        $notification_data_model->save()
                    ) {
                        //
                    } else {
                        $transaction->rollBack();
                        throw new ModelSaveErrorException($notification_data_model);
                    }
                }
            }

            $transaction->commit();

            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public static function addNotificationToQueue($sender, array $receivers, $user_event_id, $associate_type, $associate_id, $associate_data, $created_at)
    {
        $cache_key = [RedisKey::REDIS_KEY_NOTIFIER, 'notice'];

        return Yii::$app->redis->rPush(
            $cache_key,
            [
                'sender'         => $sender,
                'receiver'       => $receivers,
                'user_event_id'  => $user_event_id,
                'associate_type' => $associate_type,
                'associate_id'   => $associate_id,
                'associate_data' => $associate_data,
                'status'         => NotificationEntity::STATUS_UNREAD,
                'created_at'     => $created_at,
            ]
        );
    }


    public static function makeUpNotification(array $notification)
    {
        if (empty($notification)) {
            return [];
        }

        //初始化
        $notices = $user_ids = $question_ids = $tag_ids = $users = $questions = $tags = [];

        $notification_ids = ArrayHelper::getColumn($notification, 'id');

        $notification_senders = NotificationDataEntity::find()->where(
            ['notification_id' => $notification_ids]
        )->orderBy('created_at DESC')->orderBy('created_at DESC')->asArray()->limit(5000)->all();

        //print_r($notification_senders);
        //exit;

        $notification_senders_data = ArrayHelper::map($notification_senders, 'created_at', 'sender', 'notification_id');

        foreach ($notification as $notice_model) {
            /* @var $notice_model NotificationEntity */
            $date = $notice_model->date;

            $associate_data = new AssociateDataModel();

            //发送者
            $sender = array_unique(array_values($notification_senders_data[$notice_model->id]));

            //所有用户，用于预先缓存用户数据
            $user_ids = array_merge($user_ids, $sender);
            //所有发送时间
            $created_ats = array_keys($notification_senders_data[$notice_model->id]);

            //处理关联数据
            if ($notice_model->associate_type == AssociateModel::TYPE_QUESTION) {
                $question_ids[] = $notice_model->associate_id;
                $associate_data->question_id = $notice_model->associate_id;
            }

            if ($notice_model->associate_type == AssociateModel::TYPE_ANSWER) {
                $answer_ids[] = $notice_model->associate_id;
                $associate_data->answer_id = $notice_model->associate_id;
            }

            if ($notice_model->associate_type == AssociateModel::TYPE_TAG) {
                $tag_ids[] = $notice_model->associate_id;
                $associate_data->tag_id = $notice_model->associate_id;
            }

            $notices[$date][] = [
                'id'           => $notice_model->id,
                'sender'       => $sender,
                'template'     => self::getNoticeTemplateByUserEventId($notice_model->user_event_id),
                'data'         => $associate_data,
                'status'       => $notice_model->status,
                'count_number' => $notice_model->count_notice,
                'created_at'   => reset($created_ats),//取第一个时间，即最新时间
            ];
        }

        if ($user_ids) {
            $users = UserService::getUserListByIds(array_unique($user_ids));
        }

        if ($question_ids) {
            $questions = QuestionService::getQuestionListByQuestionIds(array_unique($question_ids));
        }

        /*if ($tag_ids) {
            $tags = TagService::getTagListByTagIds(array_unique($tag_ids));
        }*/

        foreach ($notices as $date => &$item) {
            foreach ($item as &$notice) {
                if (preg_match_all('/\{(.+?)\}/i', $notice['template'], $symbols)) {
                    $finder = $symbols[0];
                    unset($symbols[0]);

                    $senders = [];
                    foreach ($notice['sender'] as $sender) {
                        $senders[] = Html::a(
                            $users[$sender]['username'],
                            [
                                '/user/profile/show',
                                'username' => $users[$sender]['username'],

                            ],
                            ['target' => '_blank']
                        );
                    }
                    $who_list = implode('、', $senders);
                    if ($notice['count_number'] > self::MAX_SENDER_NUMBER) {
                        $who_list .= sprintf(' 等<strong>%d</strong>人', $notice['count_number']);
                    }

                    #todo 目前模板中仅支持 {who} {question} 两个替换变量，更多请添加case
                    foreach ($symbols[1] as $key => $symbol) {
                        switch ($symbol) {
                            case 'who':
                                $notice['template'] = str_replace(
                                    $finder[$key],
                                    $who_list,
                                    $notice['template']
                                );
                                break;
                            case 'question':
                                if (!empty($notice['data']['question_id'])) {
                                    $question_url_data = [
                                        'question/view',
                                        'id' => $notice['data']['question_id'],
                                        'ng' => $notice['id'],
                                    ];

                                    if (isset($notice['data']['answer_id'])) {
                                        $question_url_data['answer_id'] = $notice['data']['answer_id'];
                                    }
                                    if (isset($notice['data']['comment_id'])) {
                                        $question_url_data['comment_id'] = $notice['data']['comment_id'];
                                    }

                                    $replace = Html::a(
                                        $questions[$notice['data']['question_id']]['subject'],
                                        $question_url_data,
                                        ['target' => '_blank']
                                    );

                                    $notice['template'] = str_replace($finder[$key], $replace, $notice['template']);
                                }

                                break;

                            default:
                                throw new Exception(sprintf('Notice Symbol:%s does not define.', $symbol));
                                break;
                        }
                    }
                }
                unset($notice['sender'], $notice['data']);
            }
            unset($notice);
        }
        unset($item);

        return $notices;
    }

    private static function getNoticeTemplateByUserEventId($user_event_id)
    {
        $user_event = UserEventService::getUserEventByEventId($user_event_id);

        if ($user_event) {
            return $user_event->notice_template;
        } else {
            return '';
        }
    }

    //todo
    public static function markAllNotificationRead($user_id)
    {
        $transaction = NotificationEntity::getDb()->beginTransaction();
        try {
            //NotificationEntity::updateAllCounters()

            $transaction->commit();

            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error(var_export($e, true));
        }
    }

    public static function getNoticeDataById($id)
    {
        $data = NotificationDataEntity::find()->where(
            [
                'notification_id' => $id,
            ]
        )->all();

        $result = [];
        foreach ($data as $item) {
            $associate_data = new AssociateDataModel(Json::decode($item['associate_data']));
            foreach ($associate_data as $key => $value) {
                if ($value) {
                    $result[$key][] = $value;
                }
            }
        }

        return $result;
    }
}
