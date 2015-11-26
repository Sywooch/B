<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/31
 * Time: 11:52
 */

namespace common\helpers;


use common\entities\AnswerEntity;
use common\services\TagService;
use common\services\UserService;
use Yii;
use yii\helpers\Html;

class TemplateHelper
{
    public static function showUsername($user_id, $link = true, $anonymity = AnswerEntity::STATUS_UNANONYMOUS)
    {
        if ($user_id == Yii::$app->user->id || $anonymity == AnswerEntity::STATUS_UNANONYMOUS) {
            $user_name = UserService::getUsernameByUserId($user_id);

            if ($link) {
                $result = Html::a($user_name, ['/member/' . $user_id]);
            } else {
                $result = $user_name;
            }
        } else {
            $result = '匿大侠';
        }


        return $result;
    }

    public static function showUserAvatar(
        $user_id,
        $size = 24,
        $link = true,
        $anonymity = AnswerEntity::STATUS_UNANONYMOUS
    ) {
        //不匿名或当前用户是登陆用户
        if ($user_id == Yii::$app->user->id || $anonymity == AnswerEntity::STATUS_UNANONYMOUS) {

            $avatar = UserService::getAvatar($user_id, $size, true);

            $avatar = Html::img(
                $avatar,
                [
                    'class' => sprintf("avatar-%d", $size),
                ]
            );
            if ($avatar && $link) {
                $avatar = Html::a($avatar, ['/membet/', 'id' => $user_id]);
            }
        } else {
            $avatar = '';
        }


        return $avatar;
    }

    /**
     * @param string|array $tag_id
     * @param bool|true    $link
     */
    public static function showTagById($tag_id, $link = true)
    {

    }

    /**
     * @param string|array $tag_name
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @internal param bool|true $link
     */
    public static function showTagLiLabelByName($tag_name)
    {
        if (empty($tag_name)) {
            return;
        }
        if (!is_array($tag_name)) {
            $tag_name = array_filter(explode(',', $tag_name));
        }

        $html = [];
        $tag_ids = TagService::getTagIdByName($tag_name);
        foreach ($tag_name as $key => $tag) {
            $html[] = sprintf(
                '<li class="tagPopup">%s</li>',
                Html::a(
                    $tag,
                    ['tag/view', 'id' => $tag_ids[$tag]],
                    [
                        'class' => 'tag tag-sm',
                    ]
                )
            );
        }

        return implode('', $html);
    }

    public static function showhumanTime($time)
    {
        return Yii::$app->formatter->asRelativeTime($time);
    }

    public static function showLoginAndRegisterBtn()
    {
        return implode(
            ' / ',
            [
                Html::a('登录', ['/member'], ['class' => 'commentLogin']),
                Html::a('注册', ['/member'], ['class' => 'commentLogin']),
            ]
        );
    }
}