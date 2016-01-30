<div class="widget-active clearfix">
    <h2 class="h4"><?= $user->count_follow_question ?> 个问题</h2>

    <div class="stream-list border-top board">
        <?= $this->render('//default/question_item_view', ['data' => $data]); ?>
    </div>
</div>