/**
 * @link http://yiiplayground.com/
 * @copyright Copyright (c) 2014 Giovanni Derks & Yii Playground contributors
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

app.login = {
    show: function () {
        $('#quick-login-modal').modal({
            show: true
        })
    }
};
app.notice = {
    warning: function (message) {
        $.notify({
            // options
            icon: 'glyphicon glyphicon-warning-sign',
            title: message,
            message: ''
        }, {
            // settings
            type: 'danger'
        });
    },
    error: function () {
        $.notify({
            // options
            icon: 'glyphicon glyphicon-warning-sign',
            title: message,
            message: ''
        }, {
            // settings
            type: 'danger'
        });
    }
};
app.ajax = {
    handle: function (e) {

        if (app.user.login == false) {
            return app.login.show();
        }

        e.preventDefault();

        var
            $link = $(e.target),
            callUrl = $link.data('href'),
            formId = $link.data('formId'),
            onDone = $link.data('onDone'),
            onFail = $link.data('onFail'),
            onAlways = $link.data('onAlways'),
            ajaxRequest;


        ajaxRequest = $.ajax({
            type: "post",
            dataType: 'json',
            url: callUrl,
            data: (typeof formId === "string" ? $('#' + formId).serializeArray() : null)
        });

        app.ajax.callbacks.target = $link;

        // Assign done handler
        if (typeof onDone === "string" && app.ajax.callbacks.hasOwnProperty(onDone)) {
            ajaxRequest.done(app.ajax.callbacks[onDone]);
        }

        // Assign fail handler
        if (typeof onFail === "string" && app.ajax.callbacks.hasOwnProperty(onFail)) {
            ajaxRequest.fail(app.ajax.callbacks[onFail]);
        }

        // Assign always handler
        if (typeof onAlways === "string" && app.ajax.callbacks.hasOwnProperty(onAlways)) {
            ajaxRequest.always(app.ajax.callbacks[onAlways]);
        }

    },
    validate: function (response) {
        //console.log(response, typeof(response));
        if (typeof (response) != 'object' || response.code != 0) {
            app.notice.warning(response.message);
            return false;
        } else {
            return true;
        }
    },
    callbacks: {
        'target': null,
        'simpleDone': function (response) {
            // This is called by the link attribute 'data-on-done' => 'simpleDone'
            console.dir(response);
            $('#ajax_result_01').html(response.body);
        },

        //回答
        'afterAnswerCreateSuccess': function (response) {
            // This is called by the link attribute 'data-on-done' => 'linkFormDone';
            // the form name is specified via 'data-form-id' => 'link_form'
            if (app.ajax.validate(response)) {
                $('#answer_item_area').append(response.data.answer_item);
                $('#answer_form_area').html(response.data.answer_form);
            }
        },
        //评论
        'afterCommentCreateSuccess': function (response) {
            if (app.ajax.validate(response)) {
                $('#comment_item_area_' + app.ajax.callbacks.target.data('id')).append(response.data);
                //$('#comment-content-' + app.ajax.callbacks.target.data('id')).val('');

                UE.getEditor('answercommententity-content-' + app.ajax.callbacks.target.data('id')).execCommand('cleardoc');
            }
        },
        //显示评论列表
        'afterShowCommentList': function (response) {
            var comment = $('#comment-' + app.ajax.callbacks.target.data('id'));

            comment.removeClass('hidden');
            comment.html(response);

        },

    }
};

app.comment = {
    //插入AT
    'insertAT': function (answer_id, username) {
        UE.getEditor('answercommententity-content-' + answer_id).execCommand("inserthtml", '@' + username + '&nbsp;', true);
    }
};

app.report = {
    'show': function (report_url) {
        bootbox.dialog({
            title: "我要举报该问题，理由是：",
            message: '\
                <div data-role="base" id="report_form">\
                       <ul class="list-unstyled" data-model="list">\
                        <li class="radio"><label><input name="reason" type="radio" value="垃圾信息">垃圾信息：<span class="text-muted">广告、招聘、SEO 等推广方面的内容</span></label></li>\
                        <li class="radio"><label><input name="reason" type="radio" value="违规内容">违规内容：<span class="text-muted">违反国家法律条款、涉及敏感信息泄露</span></label></li>\
                        <li class="radio"><label><input name="reason" type="radio" value="不友善内容">不友善内容：<span class="text-muted">人身攻击、挑衅、辱骂等</span></label></li>\
                        <li class="radio"><label><input name="reason" type="radio" value="内容质量差">内容质量差：<span class="text-muted">排版差，可读性差，需要编辑改进</span></label></li>\
                        <li class="radio">\
                        <label><input name="reason" type="radio" value="" data-do-supplement="supplement">以上选项都不是：<span class="text-muted">需要管理员介入，请补充说明</span></label>\
                               <textarea id="supplement" autocomplete="false" class="form-control mt10 hide" rows="3" data-role="custom" name="custom" placeholder="请提供详尽的理由说明"></textarea>\
                        </li>\
                    </ul>\
                </div>\
        ',
            onEscape: true,
            backdrop: true,
            buttons: {
                success: {
                    label: "提交举报",
                    className: "btn-success",
                    callback: function () {
                        var option = $('#report_form input[name="reason"]:checked').val();
                        var reason = $('#report_form textarea').val();
                        //console.log(option, reason);
                        $.post(report_url, {"option": option, "reason": reason}, function (response) {
                            console.log(response);
                            app.ajax.validate(response);
                        });
                    }
                },
            }
        });

        $(document).on('click', '[data-do-supplement]', function (e) {
            $('#supplement').removeClass('hide')
        });
    }
}
