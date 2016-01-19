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
<div class="border-bottom mt10 pb10">
    <button type="button" class="btn btn-default btn-xs new-bookmark" data-id="123">创建收藏夹</button>
</div>

<?php foreach ($data as $item): /* @var $item CacheQuestionModel */ ?>
    <section class="media stream-list__item" data-id="1230000004304824" data-name="虚拟机" data-desc="" data-isprivate="1">
        <h2 class="small-title">
            <small class="glyphicon glyphicon-lock bookmark-eyes" data-toggle="tooltip" data-placement="top" title="" sr-only="私密收藏夹" data-original-title="私密收藏夹"></small>
            <a href="/bookmark/1230000004304824">虚拟机</a>
        </h2>
        <div class="text-muted">
            1 个条目
        </div>
    </section>
<?php endforeach; ?>
