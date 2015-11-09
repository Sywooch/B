<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/6
 * Time: 10:44
 */

namespace common\components\cws;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Object;

class CWS extends Component
{
    public static $instance = null;
    public $dict, $rule, $text;

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {


        if (function_exists('scws_open')) {
            if (self::$instance !== null) {
                self::$instance = scws_open();
                scws_set_charset(self::$instance, 'utf8');
                scws_set_multi(self::$instance, true);
            }
        } else {
            Yii::error('can not find function scws_open');

            return self::$instance = false;
        }
    }

    public function __destroy()
    {
        if (self::$instance) {
            scws_close(self::$instance);
        }
    }

    /*public function __call($action, $params)
    {
        return false;
    }*/

    public function text($text)
    {
        $this->text = $text;

        if (!$this->checkLegal()) {
            return $this;
        }

        scws_send_text(self::$instance, $text);

        return $this;
    }
    
    private function checkLegal()
    {
        if (!$this->text) {
            throw new InvalidConfigException();
        }

        if (!self::$instance) {
            return false;
        }
    }

    public function debug()
    {
        if (!$this->checkLegal()) {
            return null;
        }

        return scws_set_debug(self::$instance);
    }

    public function getWords()
    {
        if (!$this->checkLegal()) {
            return null;
        }
        
        while ($some = scws_get_result(self::$instance)) {
            foreach ($some as $word) {
                print_r($word);
            }
        }
    }
    
    public function getTops($limit = 10, $attr = 'n,an,nr,ns,nt,nz')
    {

        if (!$this->checkLegal()) {
            return null;
        }

        if (!$this->text) {
            throw new InvalidConfigException();
        }

        return scws_get_tops(self::$instance, $limit, $attr);

    }
}