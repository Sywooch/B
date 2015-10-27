<?php

/* @var $this \yii\web\View */
/* @var $content string */

use kartik\icons\Icon;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Block;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);

//Icon::map($this);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin(
            [

                    'brandLabel' => Yii::$app->name,
                    'brandUrl'   => Yii::$app->homeUrl,
                    'options'    => [
                            'class' => 'navbar navbar-default navbar-fixed-top',
                    ],
            ]
    );

    echo '<form class="navbar-form navbar-left" role="search" action="/search" method="get">
                <div class="form-group">
                    <input type="text" value="" name="keyword" class="form-control search_input" id="navbar-search" placeholder="搜索..." data-placement="bottom" data-content="请输入要搜索的关键词！">
                </div>
            </form>';

    echo Nav::widget(
            [
                    'options'      => ['class' => 'nav navbar-nav '],
                    'items'        => [
                        //        ['label' =>  Icon::show('th-large')  . '首页', 'url' => ['/site/index'] ],
                            ['label' => '社区', 'url' => ['/topic'], 'active' => true],

                    ],
                    'encodeLabels' => false,
            ]
    );


    $menuItems = [
            ['label' => 'Home', 'url' => ['/site/index']],
    ];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = [
                'label' => '消息',
                'url'   => ['/site/signup'],

        ];
        $menuItems[] = [
                'label' => '注册 & 登录',
                'url'   => ['/user/security/login'],

        ];
    } else {
        // 撰写
        $menuItems[] = [
                'label' => '撰写',
                'items' => [
                        [
                                'label' => '提问',
                                'url'   => ['/question/create'],
                        ],
                ],
        ];


        /*$menuItems[] = [
            'label'   => '<span class="sr-only">消息</span><span id="messageCount" class="glyphicon glyphicon-envelope"></span>',
            'url'     => false,
        ];*/

        // 个人中心
        $menuItems[] = [
                'label' => Yii::$app->user->identity->username,
                'items' => [
                        [
                                'label' => '我的主页',
                                'url'   => ['/user/profile/show', 'id' => Yii::$app->user->identity->id],
                        ],
                        [
                                'label' => '收藏夹',
                                'url'   => ['/user/default'],
                        ],
                        [
                                'label' => '帐号设置',
                                'url'   => ['/user/settings/profile'],
                        ],
                        '<li class="divider"></li>',
                        [
                                'label'       => '退出',
                                'url'         => ['/user/security/logout'],
                                'linkOptions' => ['data-method' => 'post'],
                        ],
                ],
        ];
    }
    /*
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/user/registration/register']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/user/security/login']];
    } else {
        $menuItems[] = [
            'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }*/


    echo Nav::widget(
            [
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items'   => $menuItems,
            ]
    );
    NavBar::end();
    ?>

    <?php if (isset($this->blocks['top-header'])) {
        echo $this->blocks['top-header'];
    } ?>


    <div class="container">
        <?php
        if (!isset($this->blocks['top-header'])) {
            echo Breadcrumbs::widget(
                    [
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]
            );
        } ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer id="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
