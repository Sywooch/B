<?php
namespace common\modules\user;

use yii;
use yii\web\AssetBundle;

class UserAsset extends AssetBundle
{

    /**
     * @var
     */
    public $sourcePath;

    /**
     * @var array
     */
    public $js = [
    ];

    /**
     * @var array
     */
    public $css = [
        'user_all.css',
    ];


    public $depends = [
        'frontend\assets\AppAsset',
    ];

    public function init()
    {
        parent::init();
        if ($this->sourcePath == null) {
            $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
        }
    }

}
