<style>
    .tt-suggestion {
        padding: 8px 20px;
    }

    .tt-suggestion + .tt-suggestion {
        border-top: 1px solid #CCCCCC;
    }
    
    .repo-language {
        float: right;
        font-style: italic;
    }
    
    .repo-name {
        font-weight: bold;
    }
    
    .repo-description {
        font-size: 12px;
        padding: 5px 0;
        overflow: hidden;
    }
    
    .league-name {
        border-bottom: 1px solid #CCCCCC;
        margin: 0 20px 5px;
        padding: 10px;
    }</style>


<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/15
 * Time: 11:54
 */

use kartik\widgets\Typeahead;
use yii\helpers\Url;
use yii\web\JsExpression;


echo Typeahead::widget(
    [
        'name'          => 'country_1',
        'options'       => ['placeholder' => 'Filter as you type ...'],
        'scrollable'    => true,
        'pluginOptions' => ['highlight' => true],
        'dataset'       => [
            [
                'prefetch' => Url::to(['test/data']),
                'limit'    => 10,
            ],
        ],
    ]
);
$template = '<div><p class="repo-language">{{language}}</p>' .
    '<p class="repo-name">{{name}}</p>' .
    '<p class="repo-description">{{description}}</p></div>';
echo Typeahead::widget(
    [
        'name'          => 'country',
        'options'       => ['placeholder' => 'Filter as you type ...'],
        'pluginOptions' => ['highlight' => true],
        'dataset'       => [
            [
                'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                'display'        => 'value',
                //'prefetch'       => Url::to(['test/data']),
                'remote'         => [
                    'url'      => Url::to(['test/data']) . '&q=%QUERY',
                    'wildcard' => '%QUERY',
                ],
                'display'        => 'value',
                'templates'      => [
                    'notFound'   => '<div class="text-danger" style="padding:0 8px">Unable to find repositories for selected query.</div>',
                    'suggestion' => new JsExpression("Handlebars.compile('{$template}')"),
                ],
            ],
        ],
    
    ]
);
