<div class="widget-active clearfix">
    <h2 class="h4"><?= $user->count_favorite ?> 个收藏</h2>

    <div class="stream-list border-top board">
        <?= $this->render('//default/question_item_view', ['data' => $data]); ?>
    </div>
</div>