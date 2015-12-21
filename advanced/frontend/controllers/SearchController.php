<?php

namespace frontend\controllers;
use common\controllers\BaseController;
use common\entities\TagSearchEntity;
use common\models\xunsearch\QuestionSearch;
use common\services\QuestionService;
use Yii;
use yii\helpers\Url;

class SearchController extends BaseController
{

    public function actionQuickSearch($keyword)
    {
        $question = new QuestionSearch();
        $tags = $question->fenci($keyword);
        if ($tags) {
            $tags = array_merge([$keyword], $tags);
            $data = QuestionService::searchQuestionByTag($tags);
        } else {
            $data = QuestionService::searchQuestionByTag([$keyword]);
        }

        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'subject' => $item['subject'],
                'url'     => Url::to(['question/view', 'id' => $item['id']]),
                'tags'    => $item['tags'],
                'tags'    => $item['tags'],
            ];
        }

        $this->jsonOut($result);
    }

    public function actionQuery($keyword)
    {
        $question = new QuestionSearch();
        $tags = $question->fenci($keyword);

        if ($tags) {
            $data = QuestionService::searchQuestionByTag($tags);
        } else {
            $data = [];
        }


        $this->jsonOut($data);
    }
}
