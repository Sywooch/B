<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..." />
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items'   => [
                    ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii']],
                    ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => '站点设置',
                        'icon'  => 'fa fa-share',
                        'url'   => '#',
                        'items' => [
                            ['label' => '参数设置', 'icon' => 'fa fa-file-code-o', 'url' => ['/setting'],],
                            [
                                'label' => '用户设置',
                                'icon'  => 'fa fa-dashboard',
                                'url'   => ['#'],
                                'items' => [
                                    ['label' => '用户事件', 'icon' => 'fa fa-circle-o', 'url' => ['/user-event'],],
                                    ['label' => '积分规则', 'icon' => 'fa fa-circle-o', 'url' => ['/user-score-rule'],],

                                ],
                            ],

                        ],
                    ],
                    [
                        'label' => '日志',
                        'icon'  => 'fa fa-share',
                        'url'   => '#',
                        'items' => [
                            [
                                'label' => '用户日志',
                                'icon'  => 'fa fa-file-code-o',
                                'url'   => ['/setting'],
                                'items' => [
                                    ['label' => '用户事件', 'icon' => 'fa fa-circle-o', 'url' => ['#'],],
                                    ['label' => '积分变动', 'icon' => 'fa fa-circle-o', 'url' => ['#'],],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        )
        ?>

    </section>

</aside>
