<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 19:29
 */

namespace common\helpers;

use common\entities\UserEntity;
use yii\helpers\Html;

class AtHelper
{
    public static function findAtUsername($content)
    {
        preg_match_all("/\@([^\r\n\s]*)/i", $content, $at_list_tmp);
        $username = [];
        foreach ($at_list_tmp[1] as $value) {
            if (StringHelper::countStringLength($value) > UserEntity::MAX_USERNAME_LENGTH) {
                continue;
            }
            $username[] = $value;
        }

        return $username;
    }
    
    public static function bracketAt()
    {

    }

    public static function decorateAt()
    {

    }

    public static function dealWithAtLink($content)
    {
        $username = self::findAtUsername($content);

        if ($username) {


            $atUsername = array_map(
                function ($item) {
                    return '@' . $item;
                },
                $username
            );

            $atUsernameWithLink = array_map(
                function ($item) {
                    return Html::a(
                        '@' . $item,
                        ['user/profile/show', 'username' => $item],
                        [
                            'target' => '_blank',
                        ]
                    );
                },
                $username
            );

            return strtr($content, array_combine($atUsername, $atUsernameWithLink));
        } else {
            return $content;
        }
    }
}
