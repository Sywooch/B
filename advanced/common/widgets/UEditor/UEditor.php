<?php

/**
 * @link https://github.com/BigKuCha/yii2-ueditor-widget
 * @link http://ueditor.baidu.com/website/index.html
 */
namespace common\widgets\UEditor;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


class UEditor extends \kucha\ueditor\UEditor
{
    //配置选项，参阅Ueditor官网文档(定制菜单等)
    public $clientOptions = [];

    //默认配置
    protected $_options;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $options = [
                'serverUrl'          => Url::to(['upload']),
                'initialFrameWidth'  => '100%',
                'initialFrameHeight' => '600',
                'lang'               => 'zh-cn',
                'toolbars'           => [
                        [
                                'fullscreen',
                                'drafts', // 从草稿箱加载
                                '|',
                                'edittip ', //编辑提示
                                'autotypeset', //自动排版
                                '|',
                                //'undo', //撤销
                                //'redo', //重做
                                'forecolor', //字体颜色
                                'bold', //加粗
                                'indent', //首行缩进
                                'italic', //斜体
                                'underline', //下划线
                                'strikethrough', //删除线
                                '|',
                                'paragraph', //段落格式
                                //'subscript', //下标
                                //'superscript', //上标
                                //'formatmatch', //格式刷

                                '|',
                                'blockquote', //引用
                                'pasteplain', //纯文本粘贴模式

                                //'preview', //预览
                                'horizontal', //分隔线
                                'removeformat', //清除格式

                                'link', //超链接
                                'unlink', //取消链接





                                //'emotion', //表情
                                'spechars', //特殊字符

                                //'map', //Baidu地图
                                //'gmap', //Google地图
                                //'insertvideo', //视频


                                'insertorderedlist', //有序列表
                                'insertunorderedlist', //无序列表
                                //'directionalityltr', //从左向右输入
                                //'directionalityrtl', //从右向左输入

                                //'rowspacingtop', //段前距
                                //'rowspacingbottom', //段后距


                                //'imagenone', //默认
                                //'imageleft', //左浮动
                                //'imageright', //右浮动
                                //'imagecenter', //居中

                                //'lineheight', //行间距

                                //'scrawl', //涂鸦
                                //'music', //音乐


                                '|',
                                'charts', // 图表

                                'insertparagraphbeforetable', //"表格前插入行"
                                '|',
                                'edittable', //表格属性
                                'edittd', //单元格属性
                                'inserttable', //插入表格
                                'insertrow', //前插入行
                                'insertcol', //前插入列
                                'mergeright', //右合并单元格
                                'mergedown', //下合并单元格
                                'deleterow', //删除行
                                'deletecol', //删除列
                                'splittorows', //拆分成行
                                'splittocols', //拆分成列
                                'splittocells', //完全拆分单元格
                                //'deletecaption', //删除表格标题
                                'inserttitle', //插入标题
                                'mergecells', //合并多个单元格
                                'deletetable', //删除表格

                                '|',
                                'insertimage', //多图上传
                                //'snapscreen', //截图
                                //'wordimage', //图片转存
                                'attachment', //附件
                                //'|',
                                //'source', //源代码
                                //'preview', //预览
                        ],
                ]
        ];

        $this->clientOptions = ArrayHelper::merge($options, $this->clientOptions);

    }


}