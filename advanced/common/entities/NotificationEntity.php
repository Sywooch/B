<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 11:32
 */

namespace common\entities;


use common\behaviors\NotificationBehavior;
use common\models\Notification;
use common\services\UserService;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class NotificationEntity extends Notification
{
    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';
    
    
    const TYPE_SYSTEM = 'system:normal';
    
    const TYPE_ANSWER_AT_ME = 'at:in_answer';
    const TYPE_COMMENT_AT_ME = 'at:in_comment';
    const TYPE_REPLY_COMMENT_TO_ME = 'at:in_reply';
    
    const TYPE_FOLLOW_ME = 'follow:me';
    const TYPE_FOLLOW_MY_SPECIAL_COLUMN = 'follow:my_special_column';
    
    const TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER = 'follow_question:has_new_answer';
    const TYPE_FOLLOW_QUESTION_MODIFY_ANSWER = 'follow_question:modify_answer';
    
    const TYPE_FOLLOW_TAS_HAS_NEW_QUESTION = 'follow_tag:has_new_question';
    const TYPE_FOLLOW_FAVORITE_HAS_NEW_QUESTION = 'follow_favorite:has_new_question';
    
    const TYPE_PRIVATE_MESSAGE_TO_ME = 'pm:to_me';
    
    const TYPE_INVITE_ME_TO_ANSWER = 'invite:to_answer';
    
    const TYPE_MY_QUESTION_IS_MODIFIED = 'question:is_modified';
    const TYPE_MY_QUESTION_IS_LOCK = 'question:is_locked';
    const TYPE_MY_QUESTION_IS_CLOSE = 'question:is_close';
    
    const TYPE_MY_ANSWER_IS_AGREED = 'answer:is_agreed';
    const TYPE_MY_ANSWER_IS_MODIFIED = 'answer:is_modified';
    const TYPE_MY_ANSWER_IS_FOLD = 'answer:is_fold';
    const TYPE_MY_ANSWER_HAS_NEW_COMMENT = 'answer:has_new_comment';
    
    
    public static $notice_type = [
        'at'              => [
            'in_answer'  => [200, '[sender]在[question]的中提到我。'],
            'in_comment' => [201, '[sender]在[question]的中回复我。'],
            'in_reply'   => [202, '[sender]在[question]的中评论我。'],
        ],
        'follow'          => [
            'me'                => [300, '[sender]关注了我'],
            'my_special_column' => [301, '[sender]关注了我的专栏'],
        ],
        'follow_question' => [
            'has_new_answer' => [400, '您关注的[question]，有新的回答！'],
            'modify_answer'  => [401, '您关注的[question]，有人更新了回答！'],
        ],
    ];

    public static $notice_code;
    
    /*public function behaviors()
    {
        return [
            'question_behavior' => [
                'class' => NotificationBehavior::className(),
            ],
        ];
    }*/
    
    public static function addNotify($sender, array $receivers, $notice_code, $associative_data, $create_at)
    {
        $data = [];
        
        foreach ($receivers as $receiver) {
            $data[] = [
                'sender'           => $sender,
                'receiver'         => $receiver,
                'notice_code'      => $notice_code,
                'associative_data' => Json::encode($associative_data),
                'status'           => self::STATUS_UNREAD,
                'create_at'        => $create_at,
            ];
        }
        
        $command = self::getDb()->createCommand()->batchInsert(
            self::tableName(),
            [
                'sender',
                'receiver',
                'notice_code',
                'associative_data',
                'status',
                'create_at',
            ],
            $data
        );
        
        //echo $command->getSql();
        if ($command->execute()) {
            
            #increase count_notification BY trigger
            //$this->trigger(ActiveRecord::EVENT_AFTER_INSERT);
            
            /* @var $userService UserService */
            $userService = Yii::createObject(UserService::className());
            $userService->increaseNotificationCount($receivers);
            
            
            return true;
        } else {
            Yii::error(
                sprintf(
                    'INSERT %s Fail, SQL: %s',
                    NotificationEntity::tableName(),
                    $command->getSql()
                ),
                'notifier'
            );
            
            return false;
        }
    }
    
    public static function getNotificationCode($type, $params)
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
        $notices = [];
        $user_id = $question_id = $tag_id = [];
        foreach ($notification as $notice) {

            $mix_index = implode(
                ':',
                [
                    date('Y-m-d', $notice['create_at']),
                    md5(
                        $notice['notice_code'] . $notice['associative_data']
                    ),
                ]
            );

            $associative_data = Json::decode($notice['associative_data'], true);

            if (!isset($notices[$mix_index])) {
                $notices[$mix_index] = [
                    'sender'    => [],
                    'template'  => self::getNoticeTemplateByCode($notice['notice_code']),
                    'data'      => $associative_data,
                    'status'    => self::STATUS_READ,
                    'create_at' => $notice['create_at'],
                ];
            }

            #if one notice status is unread, the group status is unread.
            if (self::STATUS_UNREAD == $notice['status']) {
                $notices[$mix_index]['status'] = self::STATUS_UNREAD;
            }

            $notices[$mix_index]['sender'][] = $notice['sender'];

            #
            $user_id[] = $notice['sender'];
            $user_id[] = $notice['receiver'];

            #
            if (isset($associative_data['question_id'])) {
                $question_id[] = $associative_data['question_id'];
            }

            #
            if (isset($associative_data['answer_id'])) {
                $question_id[] = $associative_data['answer_id'];
            }

            #
            if (isset($associative_data['tag_id'])) {
                $question_id[] = $associative_data['tag_id'];
            }
        }

        #prebuild cache data
        if ($user_id) {
            $users = UserEntity::getUserListByIds(array_unique($user_id));
        }

        if ($question_id) {
            $questions = QuestionEntity::getQuestionListByQuestionIds(array_unique($question_id));
        }

        if ($tag_id) {
            $tags = TagEntity::getTagListByTagIds(array_unique($tag_id));
        }

        foreach ($notices as $mix_index => &$notice) {
            $new_index = date('Y-m-d', $notice['create_at']);
            if (preg_match_all('/\[(.+?)\]/i', $notice['template'], $symbols)) {
                $finder = $symbols[0];
                unset($symbols[0]);

                #sender data
                $senders = [];
                $notice['sender'] = array_unique($notice['sender']);
                foreach ($notice['sender'] as $sender) {
                    $senders[] = $users[$sender]['username'];
                }

                #other data todo
                foreach ($symbols[1] as $key => $symbol) {
                    switch ($symbol) {
                        case 'sender':
                            $notice['template'] = str_replace(
                                $finder[$key],
                                implode('、', $senders),
                                $notice['template']
                            );
                            break;
                        case 'question':
                            if (!empty($notice['data']['question_id'])) {
                                $replace = $questions[$notice['data']['question_id']]['subject'];
                            }
                            $notice['template'] = str_replace($finder[$key], $replace, $notice['template']);
                            break;

                        default:
                            Yii::error(sprintf('Notice Symbol:%s does not define.'));
                            break;
                    }
                }
            }
        }
        unset($notice);

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