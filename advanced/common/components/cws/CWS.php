<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/6
 * Time: 10:44
 */

namespace common\components\cws;


use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Object;

class CWS extends Object
{
    public static $instance = null;
    public $dict, $rule, $text;

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        if (class_exists('scws_new')) {
            self::$instance = new scws_new;
        } else {
            require_once(dirname(__FILE__) . '/pscws4.class.php');
            self::$instance = new PSCWS4();

            self::$instance->set_dict(dirname(__FILE__) . '/etc/dict.utf8.xdb');
            self::$instance->set_rule(dirname(__FILE__) . '/etc/rules.utf8.ini');
        }

        self::$instance->set_charset('utf8');
        self::$instance->set_ignore(true);
        //按位异或的 1 | 2 | 4 | 8 分别表示: 短词 | 二元 | 主要单字 | 所有单字
        self::$instance->set_multi(4);
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getWords()
    {
        if (!$this->text) {
            throw new InvalidConfigException();
        }

        while ($some = self::$instance->get_result()) {
            foreach ($some as $word) {
                print_r($word);
            }
        }
    }
    
    public function getTops($limit = 10, $attr = null)
    {
        if (!$this->text) {
            throw new InvalidConfigException();
        }

        $result = self::$instance->get_tops($limit, $attr);

        print_r($result);
    }
}