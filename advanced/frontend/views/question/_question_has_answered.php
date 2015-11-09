<?php
use yii\helpers\Html;

?>
<div class="login-warn text-center">这个问题您已经提交过答案, 您可以对
    <?= Html::a(
            '现有答案',
            [
                    '/question/view',
                    'id'        => $question_id,
                    'answer_id' => $answer_id,
            ]
    ) ?> 进行
    <?= Html::a(
            '修改',
            [
                    '/answer/update',
                    'question_id' => $question_id,
                    'id'          => $answer_id,
            ]
    ) ?>
</div>