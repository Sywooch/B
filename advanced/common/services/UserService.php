<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 10:23
 */

namespace common\services;

use common\components\Error;
use common\components\user\UserGrade;
use common\config\RedisKey;
use common\exceptions\NotFoundModelException;
use common\helpers\AvatarHelper;
use common\models\CacheUserModel;
use common\modules\user\models\LoginForm;
use Imagine\Exception\InvalidArgumentException;
use common\entities\UserEntity;
use common\entities\UserProfileEntity;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use Yii;

class UserService extends BaseService
{
    #官方账号的最大ID
    const MAX_OFFICIAL_ACCOUNT_ID = 500;
    const REGISTER_CAPTCHA_ACTION = '/user/registration/captcha';

    public static function checkUserSelf($user_id)
    {
        return (Yii::$app->user && !Yii::$app->user->isGuest && Yii::$app->user->id == $user_id) ? true : false;
    }

    public static function checkWhetherIsOfficialAccount($user_id)
    {
        return $user_id <= self::MAX_OFFICIAL_ACCOUNT_ID;
    }

    public static function increaseNotificationCount(array $user_ids)
    {
        UserProfileEntity::updateAllCounters(['count_notification' => 1], ['user_id' => $user_ids]);
    }

    public static function clearNotificationCount(array $user_ids)
    {
        UserProfileEntity::updateAll(['count_notification' => 0], ['user_id' => $user_ids]);
    }

    public static function deleteAllNotification(array $user_ids)
    {
        UserProfileEntity::deleteAll(['user_id' => $user_ids]);
    }

    /**
     * 获取用户头像
     * @param      $user_id
     * @param int  $size
     * @param bool $auto_make_avatar 是否自动生成
     * @return string
     * @throws \yii\base\Exception
     */
    public static function getAvatar($user_id, $size = 50, $auto_make_avatar = false)
    {
        $user = self::getUserById($user_id);

        if (!empty($user['avatar'])) {
            $avatarPath = Yii::$app->basePath . Yii::$app->params['avatarPath'];
            $avatarCachePath = Yii::$app->basePath . Yii::$app->params['avatarCachePath'];

            #创建文件夹
            FileHelper::createDirectory($avatarCachePath);

            #头像地址
            $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $user['avatar'];

            if (!file_exists($avatarCachePath . $size . '_' . $user['avatar'])) {
                #不存在小图，则生成一个
                try {
                    $avatar_dir = $avatarCachePath . $size . '_' . dirname($user['avatar']);

                    #print_r($avatar_dir);exit;

                    if (!file_exists($avatar_dir)) {
                        mkdir($avatar_dir, 0777, true);
                    }
                    Image::thumbnail(
                        $avatarPath . $user['avatar'],
                        $size,
                        $size
                    )->save($avatarCachePath . $size . '_' . $user['avatar'], ['quality' => 100]);
                    $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $user['avatar'];
                } catch (InvalidArgumentException $e) {
                    $avatar = null;
                }
            }
        } elseif ($auto_make_avatar) {
            #头像不存在，则创建随机头像
            $avatar = (new AvatarHelper($user['id'], $size))->getAvater();
        } else {
            #判断为空
            $avatar = null;
        }

        return $avatar;
    }


    public static function getUserById($user_id)
    {
        $data = self::getUserListByIds([$user_id]);
        if ($data) {
            return array_shift($data);
        } else {
            throw new NotFoundModelException('user', $user_id);
        }

    }

    public static function getUserListByIds($user_ids)
    {
        $result = $cache_miss_key = $cache_data = [];
        foreach ($user_ids as $user_id) {
            $cache_key = [RedisKey::REDIS_KEY_USER, $user_id];
            $cache_data = Yii::$app->redis->hGetAll($cache_key);
            if (empty($cache_data)) {
                $cache_miss_key[] = $user_id;
                $result[$user_id] = null;
            } else {
                $result[$user_id] = $cache_data;
            }
        }

        if ($cache_miss_key) {
            $cache_data = UserEntity::find()->where(
                [
                    'id' => $cache_miss_key,
                ]
            )->with('profile')->asArray()->all();

            $cache_user_model = new CacheUserModel();
            $username_id_data = [];

            foreach ($cache_data as $item) {
                #filter attributes
                $item = $cache_user_model->filterAttributes($item);
                #计算用户等级
                $user_grade = new UserGrade($item['score']);
                $item['grade_level'] = $user_grade->grade_level;
                $item['grade_name'] = $user_grade->grade_name;

                $user_id = $item['id'];
                $result[$user_id] = $item;
                #cache user
                $cache_key = [RedisKey::REDIS_KEY_USER, $user_id];
                Yii::$app->redis->hMset($cache_key, $item);

                #cache username to userid
                $username_id_data[$item['username']] = $item['id'];
            }

            #cache username id relation data
            if ($username_id_data) {
                Yii::$app->redis->mset([RedisKey::REDIS_KEY_USER_USERNAME_USERID, $username_id_data]);
            }
        }

        return $result;
    }

    public static function getUserByUsername($username)
    {
        #use redis
        if (is_array($username)) {
            $multiple = true;
        } else {
            $multiple = false;
            $username = [$username];
        }

        $username = array_values(array_unique(array_filter($username)));
        $user_ids = self::getUserIdByUsername($username);

        if ($user_ids) {
            $user = self::getUserListByIds($user_ids);
        } else {
            $user = [];
        }


        if ($multiple) {
            $result = $user;
        } else {
            $result = array_shift($user);
        }

        return $result;
    }

    /**
     * @param array|string $username
     * @return array|mixed
     */
    public static function getUserIdByUsername($username)
    {
        #use redis
        if (is_array($username)) {
            $multiple = true;
        } else {
            $multiple = false;
            $username = [$username];
        }

        $username = array_values(array_unique(array_filter($username)));

        $data = self::getUserIdByUsernameUseCache($username);

        if ($data) {
            $combine_data = array_combine($username, $data);
            if ($multiple) {
                $result = $combine_data;
            } else {
                $result = array_shift($combine_data);
            }
        } else {
            $result = false;
        }


        return $result;
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public static function getUsernameByUserId($user_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER, $user_id];
        $username = Yii::$app->redis->hGet($cache_key, 'username');

        if (false === $username) {
            $data = self::getUserById($user_id);
            $username = $data['username'];
        }

        return $username;
    }

    public static function ensureUserHasCached($user_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER, $user_id];
        if (Yii::$app->redis->hLen($cache_key) == 0) {
            return self::getUserById($user_id);
        }

        return true;
    }

    public static function deleteUserCache($user_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER, $user_id];

        return Yii::$app->redis->delete($cache_key);
    }

    private static function getUserIdByUsernameUseCache(array $username)
    {
        $cache_hit_data = Yii::$app->redis->mget([RedisKey::REDIS_KEY_USER_USERNAME_USERID, $username]);
        $cache_miss_key = Yii::$app->redis->getMissKey($username, $cache_hit_data);

        if (count($cache_miss_key)) {
            $model = UserEntity::find()->select(['id', 'username'])->where(
                [
                    'username' => $cache_miss_key,
                ]
            )->all();

            #cache_miss_data 为数组，格式[index]为索引ID，[value]为保存到redis中的数据
            $cache_miss_data = [];
            foreach ($model as $key => $item) {
                $cache_miss_data[$item['username']] = $item['id'];
            }

            //print_r($cache_miss_data);exit;

            if ($cache_miss_data) {
                #add to redis cache
                Yii::$app->redis->mset([RedisKey::REDIS_KEY_USER_USERNAME_USERID, $cache_miss_data]);

                $cache_hit_data = Yii::$app->redis->paddingMissData(
                    $cache_hit_data,
                    $cache_miss_key,
                    $cache_miss_data
                );
            }
        }

        return $cache_hit_data;
    }

    public static function getUserQuestionList($user_id, $page_no = 1, $page_size = 20)
    {
        return QuestionService::getQuestionListByUserId($user_id, $page_no, $page_size);
    }

    public static function getUserAnswerList($user_id, $page_no = 1, $page_size = 20)
    {
        return AnswerService::getAnswerListByUserId($user_id, $page_no, $page_size);
    }

    public static function getUserFavoriteList($user_id, $page_no = 1, $page_size = 20)
    {
        return FavoriteService::getUserFavoriteList($user_id, $page_no, $page_size);
    }

    public static function getUserFollowTagList($user_id, $page_no = 1, $page_size = 20)
    {
        $tag_ids = FollowService::getUserFollowTagIds($user_id, $page_no, $page_size);

        if ($tag_ids) {
            $tag_list = TagService::getTagListByTagIds($tag_ids);
        } else {
            $tag_list = false;
        }

        return $tag_list;
    }

    public static function getUserFollowQuestionList($user_id, $page_no = 1, $page_size = 20)
    {
        $question_id = FollowService::getUserFollowQuestionIdsByUserId($user_id, $page_no, $page_size);

        if ($question_id) {
            $question_list = QuestionService::getQuestionListByQuestionIds($question_id);
        } else {
            $question_list = false;
        }

        return $question_list;
    }

    public static function getUserBeGoodAtTags($user_id, $limit = 20)
    {
        return FollowService::getTagIdsWhichUserIsGoodAt($user_id, $limit, 90);
    }

    /**
     * 获取用户粉比列表
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array|bool
     */
    public static function getUserFansList($user_id, $page_no = 1, $page_size = 50)
    {
        $user_ids = FollowService::getUserFansUserId($user_id, $page_no, $page_size);

        if ($user_ids) {
            $user_list = UserService::getUserListByIds($user_ids);
        } else {
            $user_list = false;
        }

        return $user_list;
    }

    /**
     * 获取用户关注的好友列表
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array
     */
    public static function getUserFriendsList($user_id, $page_no = 1, $page_size = 50)
    {
        $user_ids = FollowService::getUserFriendsUserId($user_id, $page_no, $page_size);

        if ($user_ids) {
            $user_list = UserService::getUserListByIds($user_ids);
        } else {
            $user_list = [];
        }

        return $user_list;
    }

    public static function getUserDynamicEventList($user_id, $page_no = 1, $page_size = 50)
    {
        //todo
    }

    /**
     * 更新用户缓存
     * @param $user_id
     * @param $data
     * @return bool
     */
    public static function updateUserCache($user_id, $data)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER, $user_id];
        if ($user_id && $data && Yii::$app->redis->hLen($cache_key)) {
            return Yii::$app->redis->hMset($cache_key, $data);
        }

        return true;
    }

    /**
     * 重置用户统计数据
     * @param string $field
     * @return bool|int
     */
    public static function resetUserCount($field)
    {
        $allow_reset_fields = [
            'count_favorite',
            'count_question',
            'count_answer',
            'count_follow_user',
            'count_fans',
            'count_useful',
            'count_common_edit',
            'count_follow_question',
            'count_follow_tag',
        ];

        if (in_array($field, $allow_reset_fields)) {
            return UserProfileEntity::updateAll(
                [$field => 0]
            );
        } else {
            return Error::set(Error::TYPE_USER_NOT_ALLOW_TO_RESET_COUNT);
        }
    }

    public static function autoLoginByUsername($username)
    {
        return self::autoLogin($username);
    }

    public static function autoLoginById($user_id)
    {
        $username = UserService::getUsernameByUserId($user_id);

        if (empty($username)) {
            throw new NotFoundModelException('user', $user_id);
        }

        return self::autoLogin($username);
    }

    private static function autoLogin($username)
    {
        /* @var $login_form LoginForm */
        $login_form = Yii::createObject(LoginForm::className());
        $login_form->login = $username;

        return $login_form->loginWithoutPassword();
    }
}
