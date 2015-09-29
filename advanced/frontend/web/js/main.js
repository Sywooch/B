/**
 * Created by yuyj on 2015/9/29.
 */
$(document).ready(function () {


    $("body").delegate("form", "submit", function (t) {
        var e;
        e = $(this), e.attr("method") && e.attr("action") && (t.preventDefault(), e.find("button[type=submit]").attr("disabled", "disabled"), $.ajax({
            url: e.attr("action"),
            type: e.attr("method"),
            data: e.serialize(),
            success: function (t) {
                e.find("button[type=submit]").removeAttr("disabled"), 0 === t.status && ("/api/user?do=login" === e.attr("action") && "/user/login" !== location.pathname ? window.location.reload() : /^\//.test(t.data) ? window.location = t.data : window.location.reload())
            }
        }))
    });

    $("body").delegate("form input", "keydown", function () {
        $(this).removeClass("error"), $(this).parents(".form-group").removeClass("has-error"), $(this).next(".help-block.err").remove(), $(this).next(".error--msg").remove()
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

