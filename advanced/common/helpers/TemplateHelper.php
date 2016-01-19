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
    /**
     * @param            $user_id
     * @param bool|true  $link
     * @param bool|false $anonymity 是否匿名　true:匿名　false:不匿名
     * @return mixed|string
     */
    public static function showUsername($user_id, $link = true, $anonymity = false)
    {
        if ($user_id == Yii::$app->user->id || !$anonymity) {
            $user_name = UserService::getUsernameByUserId($user_id);
            if ($link) {
                $result = Html::a($user_name, ['user/profile/show', 'username' => $user_name]);
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
        $user = UserService::getUserById($user_id);
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
                $avatar = Html::a($avatar, ['user/profile/show', 'username' => $user['username']]);
            }
        } else {
            $avatar = '';
        }


        return $avatar;
    }

    public static function showTagById($tag_id, $link = true)
    {

    }

    public static function showTagLiLabelByName($tag_name)
    {
        if (empty($tag_name)) {
            return false;
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
                    ['/tag/view', 'id' => $tag_ids[$tag]],
                    [
                        'class' => 'tag tag-sm',
                    ]
                )
            );
        }

        return implode('', $html);
    }

    public static function showHumanTime($time)
    {
        return Yii::$app->formatter->asRelativeTime($time);
    }

    public static function showLoginAndRegisterBtn()
    {
        return implode(
            ' / ',
            [
                Html::a('登录', ['user/security/login'], ['class' => 'commentLogin', 'data-need-login' => true]),
                Html::a('注册', ['user/registration/register'], ['class' => 'commentLogin', 'target' => '_blank']),
            ]
        );
    }

    public static function showRegisterBtn()
    {
        return Html::a('没有账号？注册一个！', ['user/registration/register'], ['class' => 'ml5', 'target' => '_blank']);
    }

    public static function showHumanCurrency($currency)
    {
        $result = '';
        if ($currency < 0) {
            $result = '负债中...';
        } elseif ($currency >= 0 && $currency < 1000) {
            $result = $currency . '文钱';
        } elseif ($currency % 1000 == 0) {
            $result = sprintf('%d两白银', $currency / 1000);
        } elseif ($currency > 1000 && $currency < 10000) {
            $result = sprintf('≈%d两白银', round($currency / 1000));
        } elseif ($currency % 10000 == 0) {
            $result = sprintf('%d两黄金', $currency / 10000);
        } elseif ($currency > 10000) {
            $result = sprintf('≈%d两黄金', round($currency / 10000));
        }

        return $result;
    }

    public static function showHumanCurrencyValue($currency)
    {
        $result = '';
        if ($currency < 0) {
            $result = $currency;
        } elseif ($currency >= 0 && $currency < 1000) {
            $result = $currency;
        } elseif ($currency % 1000 == 0) {
            $result = $currency / 1000;
        } elseif ($currency > 1000 && $currency < 10000) {
            $result = sprintf('≈%d', round($currency / 1000));
        } elseif ($currency % 10000 == 0) {
            $result = sprintf('%d', $currency / 10000);
        } elseif ($currency > 10000) {
            $result = sprintf('≈%d', round($currency / 10000));
        }

        return $result;
    }

    public static function showHumanCurrencyUnit($currency)
    {
        $result = '';
        if ($currency < 0) {
            $result = '负债中...';
        } elseif ($currency >= 0 && $currency < 1000) {
            $result = '文钱';
        } elseif ($currency >= 1000 && $currency < 10000) {
            $result = '两白银';
        } elseif ($currency >= 10000) {
            $result = '两黄金';
        }

        return $result;
    }
    
    public static function dealWithComment($content)
    {
        //增加 at username 连接
        $content = AtHelper::dealWithAtLink($content);

        return $content;
    }

    public static function truncateString($string, $length = 200, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $str_length = strlen($string);
        for ($i = 0; (($i < $str_length) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $str_length) {
            $result .= $etc;
        }

        return $result;
    }
}
