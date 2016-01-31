<?php

namespace common\services;

use common\entities\NotificationEntity;
use common\entities\NotificationSenderEntity;
use common\exceptions\ModelSaveErrorException;
use common\models\AssociateModel;
use yii\base\Exception;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use Yii;

class NotificationService extends BaseService
{

    //system
    const TYPE_SYSTEM = 'system:normal';

    //at
    const TYPE_ANSWER_AT_ME = 'at:in_answer';
    const TYPE_COMMENT_AT_ME = 'at:in_comment';
    const TYPE_REPLY_COMMENT_TO_ME = 'at:in_reply';

    //follow
    const TYPE_FOLLOW_ME = 'follow:me';
    const TYPE_FOLLOW_MY_SPECIAL_COLUMN = 'follow:my_special_column';
    const TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER = 'follow:question_has_new_answer';
    const TYPE_FOLLOW_QUESTION_MODIFY_ANSWER = 'follow_question:question_modify_answer';
    const TYPE_FOLLOW_TAS_HAS_NEW_QUESTION = 'follow:tag_has_new_question';
    const TYPE_FOLLOW_FAVORITE_HAS_NEW_QUESTION = 'follow:favorite_has_new_question';

    //pm
    const TYPE_PRIVATE_MESSAGE_TO_ME = 'pm:to_me';

    //invite
    const TYPE_INVITE_ME_TO_ANSWER_QUESTION = 'invite:to_answer_question';

    //question
    const TYPE_MY_QUESTION_IS_MODIFIED = 'question:is_modified';
    const TYPE_MY_QUESTION_IS_LOCK = 'question:is_locked';
    const TYPE_MY_QUESTION_IS_CLOSE = 'question:is_close';
    const TYPE_MY_QUESTION_IS_AGREED = 'question:is_agreed';

    //answer
    const TYPE_MY_ANSWER_IS_AGREED = 'answer:is_agreed';
    const TYPE_MY_ANSWER_IS_MODIFIED = 'answer:is_modified';
    const TYPE_MY_ANSWER_IS_FOLD = 'answer:is_fold';
    const TYPE_MY_ANSWER_HAS_NEW_COMMENT = 'answer:has_new_comment';

    //comment
    const TYPE_MY_ANSWER_COMMENT_IS_AGREED = 'comment:answer_comment_is_agreed';

    //favorite
    const TYPE_MY_FAVORITE_QUESTION = 'favorite:question';


    /**
     * 模板中可用的替换变量：{who} {question}
     * @var array
     */
    public static $notice_type = [
        'pm'       => [
            'to_me' => [100, '{who} 给我发了条私信。'],
        ],
        'at'       => [
            'in_answer'  => [200, '{who} 在 {question} 的中提到我'],
            'in_comment' => [201, '{who} 在 {question} 的中回复我'],
            'in_reply'   => [202, '{who} 在 {question} 的中评论我'],
        ],
        'follow'   => [
            'me'                        => [300, '{who} 关注了我。'],
            'my_special_column'         => [301, '{who} 关注了我的专栏'],
            'question_has_new_answer'   => [302, '{who} 添加了回答 {question}'],
            'question_modify_answer'    => [303, '{who} 更新了回答 {question}'],
            'tag_has_new_question'      => [304, '关注的标签 {tag} 增加新的问题 {question}'],
            'favorite_has_new_question' => [305, '关注的收藏夹 {favorite} 增加新的问题 {question}'],
        ],
        'comment'  => [
            'answer_comment_is_agreed' => [400, '{who} 赞了你的评论 {question}'],
        ],
        'answer'   => [
            'is_agreed'       => [500, '{who} 赞了你的回答 {question}'],
            'is_modified'     => [501, '{who} 修改了你的回答 {question}'],
            'is_fold'         => [502, '{who} 折叠了你的回答 {question}'],
            'has_new_comment' => [503, '{who} 评论了你的回答 {question}'],
        ],
        'question' => [
            'is_modified' => [600, '{who} 修改了您的问题 {question}'],
            'is_locked'   => [601, '{who} 锁定了您的问题 {question}'],
            'is_close'    => [602, '{who} 关闭了您的问题 {question}'],
            'is_agreed'   => [603, '{who} 赞了你的问题 {question}'],
        ],
        'invite'   => [
            'to_answer_question' => [700, '{who} 邀请我回答问题 {question}'],
        ],
        'favorite' => [
            'question' => [800, '{who} 收藏了您的问题 {question}'],
        ],
    ];

    public static $notice_code;

    public static function addNotification($sender, array $receivers, $notice_code, $associate_type, $associate_id, $associate_data, $created_at)
    {
        Yii::trace(sprintf('通知关注此问题的人 %s', implode(',', $receivers)), 'behavior');

        $transaction = NotificationEntity::getDb()->beginTransaction();
        try {
            foreach ($receivers as $receiver) {
                //保存　notification　数据
                $notification_data = [
                    'receiver'       => $receiver,
                    'notice_code'    => $notice_code,
                    'associate_type' => $associate_type,
                    'associate_id'   => $associate_id,
                    'associate_data' => $associate_data ? Json::encode($associate_data) : null,
                    'date'           => date('Y-m-d', $created_at),
                    'status'         => NotificationEntity::STATUS_UNREAD,
                ];

                $notification_model = NotificationEntity::find()->where(
                    [
                        'receiver'       => $receiver,
                        'notice_code'    => $notice_code,
                        'associate_type' => $associate_type,
                        'associate_id'   => $associate_id,
                        'date'           => date('Y-m-d', $created_at),
                    ]
                )->one();

                if (!$notification_model) {
                    $notification_model = new NotificationEntity();
                }

                if ($notification_model->load($notification_data, '') && $notification_model->save()) {
                    $notification_id = $notification_model->id;
                } else {
                    $transaction->rollBack();
                    throw new ModelSaveErrorException($notification_model);
                }

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

            $transaction->commit();

            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error(var_export($e, true));
        }
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

            //每种类型最多合并X个
            $max_merge_number = 50;
            if (count($notification_senders_data[$notice['id']] > $max_merge_number)) {
                $notification_senders_data[$notice['id']] = array_slice(
                    $notification_senders_data[$notice['id']],
                    0,
                    $max_merge_number
                );
            }

            //发送者
            $sender = array_unique(array_values($notification_senders_data[$notice['id']]));

            //所有用户，用于预先缓存用户数据
            $user_ids = array_merge($user_ids, $sender);
            //所有发送时间
            $created_ats = array_keys($notification_senders_data[$notice['id']]);

            //拼接关联数据　$associate_data

            //问题
            if ($notice['associate_type'] == AssociateModel::TYPE_QUESTION) {
                $associate_data['question_id'] = $notice['associate_id'];
            }

            //回答
            if ($notice['associate_type'] == AssociateModel::TYPE_ANSWER) {
                $associate_data['answer_id'] = $notice['associate_id'];
            }

            //评论
            if ($notice['associate_type'] == AssociateModel::TYPE_ANSWER_COMMENT) {
                $associate_data['answer_id'] = $notice['associate_id'];
            }

            //用户
            if ($notice['associate_type'] == AssociateModel::TYPE_USER) {
                $associate_data['user_id'] = $notice['associate_id'];
            }

            //标签
            if ($notice['associate_type'] == AssociateModel::TYPE_TAG) {
                $associate_data['tag_id'] = $notice['associate_id'];
            }

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
                'sender'     => $sender,
                'template'   => self::getNoticeTemplateByCode($notice['notice_code']),
                'data'       => $associate_data,
                'status'     => $notice['status'],
                'created_at' => reset($created_ats),//取第一个时间，即最新时间
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

                    #todo 目前模板中仅支持 {who} {question} 两个替换变量，更多请添加case
                    foreach ($symbols[1] as $key => $symbol) {
                        switch ($symbol) {
                            case 'who':
                                $notice['template'] = str_replace(
                                    $finder[$key],
                                    implode('、', $senders),
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
            throw new Exception(sprintf('Notice Code %d doest not define！'));
        } else {
            $result = null;
        }

        return $result;
    }
}
