/**
 * Created by yuyj on 2015/9/29.
 */
$(document).ready(function () {

    // 防止重复提交
    $('form').on('beforeValidate', function (e) {
        $(':submit').attr('disabled', true).addClass('disabled');
    });
    $('form').on('afterValidate', function (e) {
        if (cheched = $(this).data('yiiActiveForm').validated == false) {
            $(':submit').removeAttr('disabled').removeClass('disabled');
        }
    });
    $('form').on('beforeSubmit', function (e) {
        $(':submit').attr('disabled', true).addClass('disabled');
    });


    $("#searchBox").focus(function () {
        var t;
        t = $(".global-nav .menu").width() + 180 + "px", $(".global-nav .menu").hide(), $(this).animate({width: t}, 200)
    });

    $("#searchBox").blur(function () {
        $(this).animate({width: "180px"}, 200, "swing", function () {
            $(".global-nav .menu").show()
        })
    });

    $("#backtop").click(function () {
        return $("body,html").animate({scrollTop: 0}), !1
    });

    $(document).scroll(function () {
        $(this).scrollTop() > 720 ? $("#backtop").removeClass("hidden") : $("#backtop").addClass("hidden")
    });

    $(".topframe").length && $(".topframe .close").click(function () {
        $(this).parent().remove(), 0 !== $(".topframe").length && $(".topframe .content").text() || $("body").removeClass("have-notify")
    });
});


jQuery(function ($) {
    //登陆验证
    $(document).on('click', '[data-need-login]', function (e) {
        var that = $(this);
        //需要登陆
        if (that.is('a') && app.user.login == false) {
            e.preventDefault();
            return app.login.show();
        }
    });

    //ajax操作
    $(document).on('click', '[data-href]', function (e) {
        var that = $(e.target);
        //评论
        if (that.data('do') == 'comment') {
            //判断是否已经加载过
            if ($('#comment-' + that.data('id') + '>div').length != 0) {
                $('#comment-' + that.data('id')).toggleClass('hidden');
                return;
            }
        }

        app.ajax.handle(e);
    });

    //回复评论
    $(document).on('click', '[data-comment-at-username]', function (e) {
        var that = $(this);
        //需要登陆
        if (that.is('a') && app.user.login == false) {
            e.preventDefault();
            return app.comment.insertAT($(e.target).data('answer-id'),$(e.target).data('comment-at-username'))
        }
    });
    //赞, 踩, 收藏 等操作
    /*
     $(document).on('click', '[data-do]', function (e) {
     var _this = $(this),
     _id = _this.data('id'),
     _do = _this.data('do'),
     _type = _this.data('type');

     if (_this.is('a')) e.preventDefault();

     if (_do == 'show_comment') {
     $.ajax({
     url: '/member/' + [_do, _type, _id].join('/'),
     success: function (result) {

     }
     });

     } else {
     $.ajax({
     url: '/member/' + [_do, _type, _id].join('/'),
     success: function (result) {
     if (result.type != 'success') {
     return alert(result.message);
     }
     //修改记数
     var num = _this.find('span'),
     numValue = parseInt(num.html()),
     active = _this.hasClass('active');
     _this.toggleClass('active');
     if (num.length) {
     num.html(numValue + (active ? -1 : 1));
     }
     if ($.inArray(_do, ['like', 'hate']) >= 0) {
     _this.siblings('[data-do=like],[data-do=hate]').each(function () {
     var __this = $(this),
     __do = __this.data('do'),
     __id = __this.data('id'),
     __active = __this.hasClass('active');
     if (__id != _id) return; // 同一个话题或评论触发

     __this.toggleClass('active', __do == _do);

     var _num = __this.find('span')
     _numValue = parseInt(_num.html());
     if (_num.length) {
     _num.html(_numValue + (__do != _do ? (_numValue > 0 && __active ? -1 : 0) : 1));
     }
     });
     }
     }
     });
     }


     });
     */
});