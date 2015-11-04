<?php

/**
 * @link https://github.com/BigKuCha/yii2-ueditor-widget
 * @link http://ueditor.baidu.com/website/index.html
 */
namespace common\widgets\UEditor;

use common\components\atwho\AtWhoAsset;
use Yii;
use crazydb\ueditor\UEditor as BaseUEditor;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;


class UEditor extends BaseUEditor
{
    public $style, $associate_id, $atwho_data_path;

    public function init()
    {
        $this->atwho_data_path = Url::to(
            [
                'question/get-associate-user-id-when-answer',
                'user_id'     => Yii::$app->user->id,
                'question_id' => $this->associate_id,
            ],
            true
        );

        switch ($this->style) {
            case 'answer':
                $this->config['initialFrameHeight'] = 150;
                $this->config['elementPathEnabled'] = false;
                $this->config['toolbars'] = [
                    [
                        'fullscreen',
                        //'formatmatch',
                        'bold',
                        'italic',
                        'underline',
                        'strikethrough',
                        '|',
                        //'source',
                        //'removeformat',
                        //'pasteplain',
                        //'undo',
                        //'redo',
                        //'|',
                        //'superscript',
                        //'subscript',
                        'blockquote',
                        '|',
                        'insertimage',
                        'emotion',
                        'scrawl',
                        'insertvideo',
                        'music',
                        'attachment',
                        'map',
                        '|',
                        'horizontal',
                        //'inserttable',
                        '|',
                        //'print',
                        //'searchreplace',
                        //'help',
                    ],
                ];
                break;
            case 'comment':
                $this->config['toolbars'] = [
                    [
                        //'fullscreen',
                        //'formatmatch',
                        'bold',
                        'italic',
                        'underline',
                        'strikethrough',
                        '|',
                        'removeformat',
                        'pasteplain',
                        '|',
                        'blockquote',
                        '|',
                        'insertimage',
                        'emotion',
                        'scrawl',
                        'insertvideo',
                        'music',
                        'attachment',
                        'map',
                    ],
                ];
                break;

            default:
                $this->config['toolbars'] = [
                    [
                        'fullscreen',
                        'source',
                        'formatmatch',
                        'removeformat',
                        'pasteplain',
                        'undo',
                        'redo',
                        '|',
                        'bold',
                        'italic',
                        'underline',
                        'strikethrough',
                        'superscript',
                        'subscript',
                        'blockquote',
                        '|',
                        'forecolor',
                        'insertorderedlist',
                        'insertunorderedlist',
                        '|',
                        'lineheight',
                        '|',
                        'indent',
                        '|',
                    ],
                    [
                        'justifyleft',
                        'justifycenter',
                        'justifyright',
                        'justifyjustify',
                        '|',
                        'link',
                        'unlink',
                        '|',
                        'insertimage',
                        'emotion',
                        'scrawl',
                        'insertvideo',
                        'music',
                        'attachment',
                        'map',
                        '|',
                        'horizontal',
                        'inserttable',
                        '|',
                        'print',
                        'searchreplace',
                        'help',
                    ],
                ];
        }

        /*if(Yii::$app->user->isAdmin()){
                    // add 'source',
                }*/

        parent::init();

    }

    public function run()
    {

        $id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;

        $config = Json::encode($this->config);

        //ready部分代码，是为了缩略图管理。UEditor本身就很大，在后台直接加载大文件图片会很卡。
        $script = <<<UEDITOR
    var {$this->name} = UE.getEditor('{$id}',{$config});
    {$this->name}.ready(function(){
        this.addListener( "beforeInsertImage", function ( type, imgObjs ) {
            for(var i=0;i < imgObjs.length;i++){
                imgObjs[i].src = imgObjs[i].src.replace(".thumbnail","");
            }
        });
    });
UEDITOR;

        /**
         * 回答输入框中，引入atwho
         */
        if ($this->style == 'answer') {

            AtWhoAsset::register($this->getView());

            $template = '<li>123</li>';
            $add_script = sprintf(
                "
                var at_config;
                %s.addListener('focus', function(editor){
                    if(typeof(at_config) == 'undefined'){
                        at_config = {
                                   at: '@',
                                 data: '%s',
                            displayTpl: '%s',
                            insertTpl: '%s',
                                limit: 6
                        };
                        $(this.document.body).atwho(at_config);
                    }
                });
                ",
                $this->name,
                $this->atwho_data_path,
                '<li>${username}</li>',
                '<span>@${username}</span>'

            );

            $script .= $add_script;
        }

        $this->getView()->registerJs($script);

        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute);
        } else {
            return Html::textarea($this->name, $this->value, ['id' => $id]);
        }
    }


}