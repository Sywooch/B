<?php

/**
 * @link https://github.com/BigKuCha/yii2-ueditor-widget
 * @link http://ueditor.baidu.com/website/index.html
 */
namespace common\widgets\UEditor;

use Yii;
use crazydb\ueditor\UEditor as BaseUEditor;


class UEditor extends BaseUEditor
{
    public $style = null;

    public function init()
    {
        #print_r($this->style);exit;
        switch ($this->style) {
            case 'answer':
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
                        'removeformat',
                        'pasteplain',
                        //'undo',
                        //'redo',
                        '|',
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


}