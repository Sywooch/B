/**
 * Created by yuyj on 2015/9/29.
 */
$(document).ready(function () {
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

