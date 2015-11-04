<?php
/**
 * UEditor Widget扩展
 *
 * @author xbzbing<xbzbing@gmail.com>
 * @link www.crazydb.com
 *
 * UEditor版本v1.4.3.1
 * Yii版本2.0+
 */
namespace common\components\atwho;

use yii;
use yii\web\AssetBundle;

class AtWhoAsset extends AssetBundle {

    /**
     * @var
     */
    public $sourcePath;

    /**
     * @var array
     */
    public $js = [
        'jquery.caret.min.js',
        'jquery.atwho.min.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'jquery.atwho.min.css'
    ];


    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init() {
        parent::init();
        if($this->sourcePath == null)
            $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
    }

}
