<?php

namespace common\services;

use common\entities\NotificationEntity;
use common\entities\NotificationSenderEntity;
use common\exceptions\ModelSaveErrorException;
use common\helpers\TimeHelper;
use common\models\AssociateModel;
use yii\base\Exception;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use Yii;

class NotificationService extends BaseService
{
    const MAX_SENDER_NUMBER = 5; //每类通知最多显示多少用户

    //system
    const TYPE_SYSTEM = 'system:normal';


    //question
    const TYPE_QUESTION_BE_CREATED = 'question:be_created';//谁发布了新问题
    const TYPE_QUESTION_BE_MODIFIED = 'question:be_modified';//谁修改了你的问题
    const TYPE_QUESTION_BE_ANSWERED = 'question:be_answered';//谁回答了你的问题
    const TYPE_QUESTION_BE_FOLLOWED = 'question:be_followed';//谁关注了你的问题
    const TYPE_QUESTION_BE_FAVORITE = 'question:be_favorite';//谁收藏了你的问题
    const TYPE_QUESTION_BE_LOCKED = 'question:be_locked';//谁锁定了你的问题
    const TYPE_QUESTION_BE_CLOSED = 'question:be_closed';//谁关闭了你的问题
    const TYPE_QUESTION_BE_AGREED = 'question:be_agreed';//谁赞同了你的问题


    //answer
    const TYPE_ANSWER_BE_CREATED = 'answer:be_created';//谁在文章里增加了回答
    const TYPE_ANSWER_BE_MODIFIED = 'answer:be_modified';//谁修改了你的回答
    const TYPE_ANSWER_BE_AGREED = 'answer:be_agreed';//谁赞同了你的回答
    const TYPE_ANSWER_BE_FOLD = 'answer:be_fold';//谁折叠了你的回答

    //comment
    const TYPE_COMMENT_BE_CREATED_IN_QUESTION = 'comment:be_created_in_questIon';//谁赞同了你的评论
    const TYPE_COMMENT_BE_CREATED_IN_ANSWER = 'comment:be_created_in_answer';//谁赞同了你的评论
    const TYPE_COMMENT_BE_CREATED_IN_BLOG = 'comment:be_created_in_blog';//谁赞同了你的评论
    const TYPE_COMMENT_BE_AGREED_IN_ANSWER = 'comment:be_agreed_in_answer';//谁赞同了你的评论
    const TYPE_COMMENT_BE_DELETED_IN_ANSWER = 'comment:be_deleted_in_answer';//谁删除了你的评论
    const TYPE_COMMENT_BE_MODIFIED_IN_ANSWER = 'comment:be_modified_in_answer';//谁修改了你的评论

    //user
    const TYPE_USER_BE_FOLLOWED = 'user:be_followed';//谁关注你了
    const TYPE_USER_BE_AT_IN_QUESTION = 'user:be_at_in_question';//谁在问题里AT你了
    const TYPE_USER_BE_AT_IN_ANSWER = 'user:be_at_in_answer';//谁在问题里AT你了
    const TYPE_USER_BE_AT_IN_COMMENT = 'user:be_at_in_comment';//谁在评论里AT你了
    const TYPE_USER_BE_INVITE_TO_ANSWER = 'user:be_invite_to_answer';//谁邀请你回答的问题


    /**
     * 模板中可用的替换变量：{who} {question}
     * @var array
     */
    public static $notice_type = [
        'question' => [
            'be_created'  => [100, '{who} 发布了问题 {question}'],
            'be_modified' => [101, '{who} 修改了你的问题 {question}'],
            'be_answered' => [102, '{who} 回答了你的问题 {question}'],
            'be_followed' => [103, '{who} 关注了你的问题 {question}'],
            'be_favorite' => [104, '{who} 收藏了你的问题 {question}'],
            'be_locked'   => [105, '{who} 锁定了你的问题 {question}'],
            'be_closed'   => [106, '{who} 关闭了你的问题 {question}'],
            'be_agreed'   => [107, '{who} 赞同了你的问题 {question}'],

        ],
        'answer'   => [
            'be_created'  => [200, '{who} 回答了问题 {question}'],
            'be_modified' => [201, '{who} 修改了答案 {question}'],
            'be_agreed'   => [202, '{who} 赞同了你的回答 {question}'],
            'be_fold'     => [203, '{who} 折叠了问题 {question}'],
        ],
        'comment'  => [
            'be_created_in_question' => [300, '{who} 评论了你的问题 {question}'],
            'be_created_in_answer'   => [301, '{who} 评论了你的回答 {question}'],
            'be_created_in_blog'     => [302, '{who} 评论了你的文章 {blog}'],
            'be_agreed_in_answer'    => [303, '{who} 赞同了你的评论 {question}'],
            'be_deleted_in_answer'   => [304, '{who} 删除了你的评论 {question}'],
            'be_modified_in_answer'  => [305, '{who} 修改了你的评论 {question}'],
        ],
        'user'     => [
            'be_followed'         => [400, '{who} 关注了你'],
            'be_at_in_question'   => [401, '{who} 在问题里AT你 {question}'],
            'be_at_in_answer'     => [402, '{who} 在回答里AT你 {question}'],
            'be_at_in_comment'    => [403, '{who} 在评论里AT你 {question}'],
            'be_invite_to_answer' => [404, '{who} 邀请你回答问题 {question}'],
        ],
    ];

    public static $notice_code;

    public static function addNotification($sender, array $receivers, $notice_code, $identifier, $associate_data, $created_at)
    {
        Yii::trace(sprintf('添加站内通知%d，通知用户： %s', $notice_code, implode(',', $receivers)), 'behavior');

        $transaction = NotificationEntity::getDb()->beginTransaction();
        try {
            foreach ($receivers as $receiver) {
                //保存　notification　数据
                $notification_data = [
                    'receiver'       => $receiver,
                    'notice_code'    => $notice_code,
                    'identifier'     => $identifier,
                    'associate_data' => $associate_data ? Json::encode($associate_data) : null,
                    'date'           => date('Y-m-d', $created_at),
                    'status'         => NotificationEntity::STATUS_UNREAD,
                ];

                $notification_model = NotificationEntity::find()->where(
                    [
                        'receiver'    => $receiver,
                        'notice_code' => $notice_code,
                        'identifier'  => $identifier,
                        'date'        => date('Y-m-d', $created_at),
                    ]
                )->one();

                if (!$notification_model) {
                    $notification_model = new NotificationEntity();
                }

                //同类型通知数加+1
                $notification_model->count_number = $notification_model->count_number + 1;

                if ($notification_model->load($notification_data, '') && $notification_model->save()) {
                    $notification_id = $notification_model->id;
                } else {
                    $transaction->rollBack();
                    throw new ModelSaveErrorException($notification_model);
                }

                //同类型通知最多5条
                if ($notification_model->count_number <= self::MAX_SENDER_NUMBER) {
                    //保存　notification_sender　数据
                    $notification_sender_data = [
                        'notification_id' => $notification_id,
                        'sender'          => $sender,
                        'created_at'      => $created_at,
                        'status'          => NotificationEntity::STATUS_UNREAD,
                    ];

                    $notification_sender_model = NotificationSenderEntity::find()->where(
                        [
                            'notification_id' => $notification_id,
                            'sender'          => $sender,
                        ]
                    )->one();

                    if (!$notification_sender_model) {
                        $notification_sender_model = new NotificationSenderEntity();
                    }

                    if ($notification_sender_model->load($notification_sender_data, '') &&
                        $notification_sender_model->save()
                    ) {
                        //
                    } else {
                        $transaction->rollBack();
                        throw new ModelSaveErrorException($notification_sender_model);
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

    public static function addNotificationToQueue($sender, array $receivers, $notice_code, $identifier, $associate_data, $created_at)
    {
        $cache_key = [RedisKey::REDIS_KEY_NOTIFIER, 'notice'];

        return Yii::$app->redis->rPush(
            $cache_key,
            [
                'sender'         => $sender,
                'receiver'       => $receivers,
                'notice_code'    => $notice_code,
                'identifier'     => $identifier,
                'associate_data' => $associate_data,
                'status'         => NotificationEntity::STATUS_UNREAD,
                'created_at'     => $created_at,
            ]
        );
    }

    public static function getNotificationCode($type)
    {
        list($notice_category, $notice_type) = explode(':', $type);

        if (!$notice_category || !$notice_type || !isset(self::$notice_type[$notice_category]) || !isset(self::$notice_type[$notice_category][$notice_type])) {
            throw new Exception(sprintf('Undefined Notify Key [%s]', $type));
        }

        $code = self::$notice_type[$notice_category][$notice_type][0];

        /*if ($params) {
            $params = is_array($params) ? $params : [$params];
            $message = vsprintf(self::$notify[$notice_category][$notice_type][1], $params);
        } else {
            $message = self::$notify[$notice_category][$notice_type][1];
        }*/

        return $code;
    }

    public static function makeUpNotification(array $notification)
    {

        if (empty($notification)) {
            return [];
        }

        //初始化
        $notices = $user_ids = $question_ids = $tag_ids = $users = $questions = $tags = [];

        $notification_ids = ArrayHelper::getColumn($notification, 'id');

        $notification_senders = NotificationSenderEntity::find()->where(
            ['notification_id' => $notification_ids]
        )->orderBy('created_at DESC')->orderBy('')->asArray()->limit(5000)->all();

        //print_r($notification_senders);
        //exit;

        $notification_senders_data = ArrayHelper::map($notification_senders, 'created_at', 'sender', 'notification_id');

        foreach ($notification as $notice) {
            $date = $notice['date'];

            $associate_data = Json::decode($notice['associate_data'], true);

            //发送者
            $sender = array_unique(array_values($notification_senders_data[$notice['id']]));

            //所有用户，用于预先缓存用户数据
            $user_ids = array_merge($user_ids, $sender);
            //所有发送时间
            $created_ats = array_keys($notification_senders_data[$notice['id']]);

            //处理关联数据中支持的三个变量 question_id answer_id tag_id
            if (isset($associate_data['question_id'])) {
                $question_ids[] = $associate_data['question_id'];
            }
            if (isset($associate_data['answer_id'])) {
                $answer_ids[] = $associate_data['answer_id'];
            }
            if (isset($associate_data['tag_id'])) {
                $tag_ids[] = $associate_data['tag_id'];
            }

            $notices[$date][] = [
                'sender'       => $sender,
                'template'     => self::getNoticeTemplateByCode($notice['notice_code']),
                'data'         => $associate_data,
                'status'       => $notice['status'],
                'count_number' => $notice['count_number'],
                'created_at'   => reset($created_ats),//取第一个时间，即最新时间
            ];
        }

        if ($user_ids) {
            $users = UserService::getUserListByIds(array_unique($user_ids));
        }

        if ($question_ids) {
            $questions = QuestionService::getQuestionListByQuestionIds(array_unique($question_ids));
        }

        if ($tag_ids) {
            $tags = TagService::getTagListByTagIds(array_unique($tag_ids));
        }

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

    private static function getNoticeTemplateByCode($code)
    {
        if (self::$notice_code === null) {
            foreach (self::$notice_type as $notice_group_key => $notice_group) {
                foreach ($notice_group as $notice_type => $notice) {
                    self::$notice_code[$notice[0]] = $notice[1];
                }
            }
        }

        if (isset(self::$notice_code[$code])) {
            $result = self::$notice_code[$code];
        } elseif (YII_DEBUG) {
            throw new Exception(sprintf('Notice Code %d doest not define！', $code));
        } else {
            $result = null;
            Yii::error(sprintf('Notice Code %d doest not define！', $code));
        }

        return $result;
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
}
