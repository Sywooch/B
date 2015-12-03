<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/2
 * Time: 17:42
 */

namespace common\components\user;

use Yii;
use yii\base\Exception;
use yii\base\Object;

/**
 * Class UserGrade
 * @package common\components\user
 * @property \common\models\CacheUserModel user
 */
class UserGrade extends Object
{
    public $user;//用户
    public $grade_name;//名称
    public $grade_level;//等级

    //grade_level:对应rbac中的role　grade_name:前端显示的角色名称, min_score:当前级别的最低分数
    public static $grade_rule = [
        'grade_level_a' => [
            'grade_name' => '',
            'min_score'  => 0,
        ],
        'grade_level_b' => [
            'grade_name' => '',
            'min_score'  => 100,
        ],
        'grade_level_c' => [
            'grade_name' => '',
            'min_score'  => 500,
        ],
        'grade_level_d' => [
            'grade_name' => '',
            'min_score'  => 1000,
        ],
        'grade_level_e' => [
            'grade_name' => '',
            'min_score'  => 5000,
        ],
        'grade_level_f' => [
            'grade_name' => '',
            'min_score'  => 10000,
        ],
        'grade_level_g' => [
            'grade_name' => '',
            'min_score'  => 20000,
        ],
        'grade_level_h' => [
            'grade_name' => '',
            'min_score'  => 50000,
        ],
        'grade_level_i' => [
            'grade_name' => '',
            'min_score'  => 100000,
        ],
    ];

    /**
     * @param array $user common\models\CacheUserModel
     * @param null  $config
     */
    public function __construct($user, $config = null)
    {
        $this->user = $user;
        parent::__construct($config);
    }
    
    public function calculateGradeLevel($score)
    {
        foreach (self::$grade_rule as $grade_level => $rule) {
            if ($score < $rule['min_score']) {
                return $grade_level;
            }
        }
    }


    private function getGradeName($grade_level)
    {
        $this->ensureGradeRule($grade_level);

        return self::$grade_rule[$grade_level]['grade_name'];
    }

    private function ensureGradeRule($grade_index)
    {
        if (!isset(self::$grade_rule[$grade_index])) {
            throw new Exception(sprintf('级别%d的等级规则未定义', $grade_index));
        }
    }

    /**
     * 切换等级
     * @param $user_id
     * @param $from_grade
     * @param $to_grade
     * @return bool
     */
    public function changeGrade($user_id, $from_grade, $to_grade)
    {
        $auth = Yii::$app->getAuthManager();
        if ($auth->revoke($from_grade, $user_id) && $auth->assign($to_grade, $user_id)) {
            return true;
        }
    }

    public function adjustGrade()
    {
        //$old_grade = $this->user->grade_level
    }
}
