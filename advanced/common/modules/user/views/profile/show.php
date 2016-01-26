<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use common\helpers\TemplateHelper;
use common\models\CacheTagModel;
use common\models\CacheUserModel;
use common\modules\user\UserAsset;
use common\services\FollowService;
use common\services\UserService;
use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View  $this
 * @var CacheUserModel $user
 */

$this->title = $user['username'];
$this->params['breadcrumbs'][] = $this->title;

UserAsset::register($this);
?>
<?php
$this->beginBlock('top-header');
?>
<header class="user-header bg-gray pt20">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-header media">
                    <div class="pull-left">
                        <?= TemplateHelper::showUserAvatar($user->id, 128, true); ?>
                    </div>

                    <div class="media-body">
                        <h4 class="media-heading"><?= $user->username ?></h4>
                        <ul class="sn-inline">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <ul class="list-unstyled profile-links">
                    <li>现居城市：<a href="/user/settings">填写城市</a></li>
                    <li>现任职位：<a href="/user/profile">填写工作经历</a></li>
                    <li>院校专业：<a href="/user/profile">填写教育经历</a></li>
                    <li>个人网站：<a href="/user/settings">填写个人网站</a></li>
                </ul>
            </div>
            <div class="text-right col-md-3">
                <p class="mt30">
                    <?php if ($user->id != Yii::$app->user->id) { ?>
                        <?= $this->render(
                            '_user_follow',
                            [
                                'id'          => $user->id,
                                'count_fans'  => $user->count_fans,
                                'is_followed' => $follow_status,
                            ]
                        ) ?>
                    <?php } else { ?>
                        <strong>
                            <?= Html::a(
                                $user->count_fans,
                                [''],
                                [
                                    'class' => 'funsCount',
                                ]
                            ); ?>
                        </strong> 个粉丝
                    <?php } ?>
                </p>

                <p>
                    <?php foreach ($fans_list as $fans):/* @var CacheUserModel $fans */ ?>
                        <?= Html::a(
                            Html::img(UserService::getAvatar($fans->id, 24, true), ['class' => 'avatar-24']),
                            [
                                '/user/profile/show',
                                'username'
                                => $fans->username,
                            ],
                            [
                                'data' => [
                                    'toggle'    => 'tooltip',
                                    'placement' => 'bottom',
                                    'title'     => $fans->username,
                                ],
                            ]
                        ) ?>
                    <?php endforeach; ?>
                </p>
            </div>
        </div>
    </div>
</header>

<?php
$this->endBlock();
?>

<div class="wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-4 profile">
                <ul class="list-unstyled profile-ranks">
                    <li title="声望">
                        <strong><?= $user->score ?></strong>
                        <span class="text-muted">声望值</span>

                    </li>
                    <li title="财富">
                        <strong style=""><?= TemplateHelper::showHumanCurrencyValue($user->currency) ?></strong>
                        <span class="text-muted"><?= TemplateHelper::showHumanCurrencyUnit($user->currency) ?></span>
                    </li>
                    <li title="支持">
                        <strong><?= $user->count_useful ?></strong>
                        <span class="text-muted">次被赞</span>
                    </li>
                </ul>
                <ul class="rep-rects clearfix">
                    <?php foreach ($score_list as $date => $score): ?>
                        <?php if (array_sum($score) == 0): ?>
                            <?= Html::tag(
                                'li',
                                '',
                                [
                                    'class' => 'rect bg-gray',
                                    'data'  => [
                                        'toggle'    => 'tooltip',
                                        'placement' => 'top',
                                    ],
                                    'title' => '没有获得声望或货币',
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= Html::tag(
                                'li',
                                '',
                                [
                                    'class' => (array_sum($score) > 0) ? 'rect bg-green' : 'rect bg-red',
                                    'style' => sprintf(
                                        "opacity: %f",
                                        max(
                                            round(
                                                ($score['currency'] > 0 ? ($score['currency'] / $total_currency) : 0) + ($score['credit'] > 0 ? ($score['credit'] / $total_credit) : 0),
                                                1
                                            ),
                                            0.3
                                        )
                                    ),
                                    'data'  => [
                                        'html'      => 'true',
                                        'toggle'    => 'tooltip',
                                        'placement' => 'top',
                                    ],
                                    'title' => sprintf(
                                        "声望：%d<br />货币：%d<br /> %s ",
                                        $score['credit'],
                                        $score['currency'],
                                        $date
                                    ),
                                ]
                            ) ?>

                        <?php endif; ?>

                    <?php endforeach; ?>
                </ul>
                <div class="profile-bio mono">
                    <?php if ($user->description) : ?>
                        <?= $user->description ?>
                    <?php else: ?>
                        <p>个人简介都不给 &lt;(￣ ﹌ ￣)&gt;</p>
                    <?php endif; ?>
                </div>
                <?php if ($tag_list): ?>
                    <div class="profile-goodjob">
                        <h4>擅长标签</h4>

                        <ul class="taglist--inline multi">
                            <?php foreach ($tag_list as $tag): /* @var $tag CacheTagModel */ ?>
                                <li class="tagPopup">
                                    <?= Html::a(
                                        sprintf('%s x %d', $tag->name, $tag->count_passive_follow),
                                        ['/tag/view', 'id' => $tag['id']],
                                        [
                                            'class' => 'tag',
                                        ]
                                    ); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!--<div class="joindate">
                            始于 3月20日
                        </div>-->
                    </div>
                <?php endif; ?>
            </div>

            <!-- Nav tabs -->
            <div class="col-md-8">
                <?php
                $items = [
                    [
                        'label'   => '<i class="glyphicon glyphicon-time"></i> 动态',
                        'content' => $this->render(
                            '_feed',
                            [
                                'user_event_log_list' => $user_event_log_list,
                                'user_event_list'     => $user_event_list,
                            ]
                        ),
                        'active'  => true,
                    ],
                    [
                        'label'       => sprintf(
                            '<i class="glyphicon glyphicon-question-sign"></i> 提问 <span class="badge">%d</span>',
                            $user->count_question
                        ),
                        'content'     => '',
                        'linkOptions' => ['data-url' => Url::to(['profile/owner-question','user_id' => $user->id])],
                    ],
                    [
                        'label'       => sprintf(
                            '<i class="glyphicon glyphicon-comment"></i> 回答 <span class="badge">%d</span>',
                            $user->count_answer
                        ),
                        'content'     => '',
                        'linkOptions' => ['data-url' => Url::to(['profile/answered-question','user_id' => $user->id])],
                    ],
                    /*[
                        'label'       => '<i class="glyphicon glyphicon-pencil"></i> 文章',
                        'content'     => '',
                        'linkOptions' => ['data-url' => Url::to(['/default/fetch-un-answer'])],
                    ],*/
                    [
                        'label' => '<i class="glyphicon glyphicon-star"></i> 收藏',
                        'items' => [
                            [
                                'label'       => sprintf(
                                    '收藏的问题 <span class="badge">%d</span>',
                                    $user->count_favorite
                                ),
                                'encode' => false,
                                'content'     => '',
                                'linkOptions' => ['data-url' => Url::to(['profile/followed-question','user_id' => $user->id])],
                            ],
                            /*[
                                'label'       => '收藏的文章',
                                'content'     => '',
                                'linkOptions' => ['data-url' => Url::to(['/default/fetch-un-answer'])],
                            ],*/
                        ],
                    ],
                    [
                        'label'   => '<i class="glyphicon glyphicon-heart"></i> 关注',
                        'items'   => [
                            [
                                'label'       => sprintf(
                                    '关注的人 <span class="badge">%d</span>',
                                    $user->count_follow_user
                                ),
                                'encode' => false,
                                'content'     => '',
                                'linkOptions' => ['data-url' => Url::to(['profile/followed-user','user_id' => $user->id])],
                            ],
                            [
                                'label'       => sprintf(
                                    '关注的问题 <span class="badge">%d</span>',
                                    $user->count_follow_question
                                ),
                                'encode' => false,
                                'content'     => '',
                                'linkOptions' => ['data-url' => Url::to(['profile/followed-question','user_id' => $user->id])],
                            ],
                            [
                                'label'       => sprintf(
                                    '关注的标签 <span class="badge">%d</span>',
                                    $user->count_follow_tag
                                ),
                                'encode' => false,
                                'content'     => '',
                                'linkOptions' => ['data-url' => Url::to(['profile/followed-tag','user_id' => $user->id])],
                            ],
                            /*[
                                'label'       => '关注的专栏',
                                'content'     => '',
                                'linkOptions' => ['data-url' => Url::to(['profile/followed-blog','user_id' => $user->id])],
                            ],*/
                        ],
                    ],

                ];
                // Ajax Tabs Above
                echo TabsX::widget(
                    [
                        'items'        => $items,
                        'position'     => TabsX::POS_ABOVE,
                        'encodeLabels' => false,
                        'options'      => [
                            'class' => 'nav-pills',
                        ],
                    ]
                );
                ?>
            </div>
        </div>
    </div>
</div>


