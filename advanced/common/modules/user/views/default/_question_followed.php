<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-15
 * Time: 16:24
 */
use common\helpers\TemplateHelper;
use common\models\CacheQuestionModel;
use yii\helpers\Html;

?>
<?php foreach ($data as $item): /* @var $item CacheQuestionModel */ ?>
    <section class="stream-list__item hover-show">

        <div class="bookmark-rank">
            <div class="votes">
                <?= $item->count_follow ?>
                <small>关注</small>
            </div>
            <div class="answers<?= $item->count_answer > 0 ? ' answered' : ''; ?>">
                <?= $item->count_answer ?>
                <small>回答</small>
            </div>
        </div>

        <div class="summary">
            <ul class="author list-inline">
                <li>
                    <?= TemplateHelper::showUsername($item->created_by, true) ?>
                    <span class="split"></span>
                    <?= TemplateHelper::showHumanTime($item->created_at, true) ?>
                </li>
                <li class="pull-right hover-show-obj">
                    <a href="#"
                       class="cancel-follow ml10"
                       data-id="<?= $item->id ?>"
                       data-title="<?= $item->subject ?>"
                       data-do="follow/cancel"
                       data-type="question">取消关注</a>
                </li>
            </ul>
            <h2 class="title">
                <?= Html::a($item->subject, ['question/view', 'id' => $item->id]) ?>
            </h2>
            <ul class="taglist--inline ib">
                <?= TemplateHelper::showTagLiLabelByName($item->tags) ?>
            </ul>
        </div>
    </section>
<?php endforeach; ?>
