<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-15
 * Time: 11:54
 */
use yii\helpers\Html;

?>
<div class="container pt30">
    <div class="row">
        <div class="col-xs-12 col-md-9 main">
            <ul class="nav nav-tabs" roles="tablist">

                <li<?= ($active == 'owner') ? ' class="active"' : '' ?>>
                    <?= Html::a(
                        '我创建的收藏夹',
                        ['/user/default/owner-favorite']
                    )
                    ?></li>
                <li<?= ($active == 'answered') ? ' class="active"' : '' ?>><?= Html::a(
                        '我关注的收藏夹',
                        ['/user/default/followed-favorite']
                    ) ?></li>
            </ul>
            <div class="stream-list">
                <?= $this->render(
                    '_favorite_' . $active,
                    [
                        'data' => $data,
                    ]
                ) ?>
            </div>
            <!-- /.stream-list -->

            <div class="text-center">

            </div>
        </div>

        <?= $this->render('_right', []); ?>

    </div>
</div>