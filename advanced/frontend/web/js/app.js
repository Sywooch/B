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
    success: function (message) {
        $.notify({
            icon: app.user.avatar,
            title: '',
            message: message
        }, {
            type: 'success',
            delay: 8000,
            icon_type: 'image',
            placement: {
                from: "bottom",
                align: "right"
            },
            template: '<div data-notify="container" class="col-xs-9 col-sm-2 alert alert-{0}" role="alert">' +
            '<img data-notify="icon" class="img-circle pull-left">' +
            '<span data-notify="title">{1}</span>' +
            '<span data-notify="message">{2}</span>' +
            '</div>',
            animate: {
                enter: 'animated lightSpeedIn',
                exit: 'animated lightSpeedOut'
            }
        });
    },
    warning: function (message) {
        $.notify({
            // options
            icon: 'glyphicon glyphicon-warning-sign',
            title: message,
            message: ''
        }, {
            // settings
            type: 'danger',
            animate: {
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            }
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
            type: 'danger',
            animate: {
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            }
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
            app.notice.success(response.message);
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
                $('#answer-item-area').append(response.data.answer_item);
                $('#answer-form-area').html(response.data.answer_form);
            }
        },
        //评论
        'afterCommentCreateSuccess': function (response) {
            if (app.ajax.validate(response)) {
                var answer_id = app.ajax.callbacks.target.data('id');
                if ($('#comment-page-' + answer_id).length) {
                    $('#comment-page-' + answer_id).before(response.data);
                } else {
                    $('#comment-area-' + answer_id + ' #comment-pajax-' + answer_id).append(response.data);
                }

                UE.getEditor('answercommententity-content-' + app.ajax.callbacks.target.data('id')).execCommand('cleardoc');
            }
        },
        //显示评论列表
        'afterShowCommentList': function (response) {
            var comment = $('#comment-area-' + app.ajax.callbacks.target.data('id'));
            comment.removeClass('hidden');
            comment.html(response);
        },

    }
};

app.comment = {
    //插入AT
    'insertAT': function (answer_id, username) {
        UE.getEditor('commententity-content-' + answer_id).execCommand("inserthtml", '@' + username + '&nbsp;', true);
    }
};

app.dialog = {
    confirm: function (title, message, redirect) {
        bootbox.setLocale("zh_CN");
        bootbox.dialog({
            message: message,
            title: title,
            buttons: {
                cancel: {
                    label: "取消",
                    className: null,
                    callback: function () {
                    }
                },
                ok: {
                    label: "确定",
                    className: "btn-primary",
                    callback: function () {
                        if (redirect) {
                            window.location.href = redirect;
                        }
                    }
                }
            },
            onEscape: true,
            backdrop: true,
        });


        /*bootbox.confirm(title, function (result) {
         if (result && redirect) {
         window.location.href = redirect;
         }
         });*/
    }
};
app.report = {
    'show': function (report_url) {
        bootbox.setLocale("zh_CN");
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
                            //console.log(response);
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
