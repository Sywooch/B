define("template", ["jquery"], function (t) {
    "use strict";
    return function (e, n) {
        var r;
        return r = e || "", t.each(n, function (e, n) {
            var i, a;
            i = typeof n, a = new RegExp("{{\\s*" + e + "\\s*}}", "g"), "object" === i && null !== n ? t.each(n, function (t, n) {
                var i;
                i = new RegExp("{{\\s*" + e + "." + t + "\\s*}}", "g"), r = r.replace(i, n)
            }) : r = r.replace(a, n)
        }), r
    }
}), define("sfModal", ["jquery", "template"], function (t, e) {
    "use strict";
    return function (n) {
        var r, i;
        if ("object" != typeof n) {
            if ("hide" === n)return void t(".sfmodal").modal("hide");
            if ("toggle" === n)return void t(".sfmodal").modal("toggle");
            n = {content: n, hideDone: !0}
        }
        r = t.extend({
            hideTitle: !1,
            hideFooter: !1,
            modalSize: "",
            title: "警告：前方高能！",
            content: "玩脱了",
            wrapper: null,
            $content: null,
            hideClose: !1,
            closeText: "取消",
            closeClass: "btn-default",
            hideDone: !1,
            doneText: "确认",
            doneClass: "btn-primary",
            doneFn: function () {
                t(".sfmodal").modal("hide")
            },
            show: function () {
            },
            shown: function () {
            },
            hide: function () {
            },
            hidden: function () {
            },
            loaded: function () {
            }
        }, n), i = '<div class="sfmodal modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">  <div class="modal-dialog {{modalSize}}">    <div class="modal-content">      ' + (r.hideTitle ? "" : '<div class="modal-header">        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>        <h4 class="modal-title">{{title}}</h4>      </div>') + '<div class="modal-body">        <p class="sfModal-content">          </div>          <div class="modal-footer ' + (r.hideFooter ? "hidden" : "") + '">' + (r.hideClose ? "" : '<button type="button" class="btn {{closeClass}}" data-dismiss="modal">{{closeText}}</button>') + (r.hideDone ? "" : '<button type="button" class="btn {{doneClass}} done-btn">{{doneText}}</button>') + "</div>        </div>      </div>    </div>", t(".sfmodal").length > 0 && (t(".sfmodal").remove(), t(".modal-backdrop").remove()), r.wrapper ? (t(r.wrapper).append(e(i, r)), t(r.wrapper).append('<div class="modal-backdrop in"></div>')) : t("body").append(e(i, r)), r.$content ? t(".sfmodal .sfModal-content").append(r.$content) : t(".sfmodal .sfModal-content").html(r.content), t(".sfmodal").modal({keyboard: !0}), t(".sfmodal").on("show.bs.modal", r.show).on("shown.bs.modal", r.shown).on("hide.bs.modal", function (e) {
            r.hide(e), r.wrapper && t(".modal-backdrop").remove()
        }).on("hidden.bs.modal", r.hidden).on("loaded.bs.modal", r.loaded).modal("show"), t(".sfmodal .done-btn").click(function (e) {
            r.doneFn(e), r.wrapper && t(".modal-backdrop").remove()
        })
    }
}), define("mobile", ["jquery"], function (t) {
    return window.innerWidth > 767 ? {
        login: null,
        signup: null
    } : (t(".hate, .like").data("toggle", "false"), {
        login: function () {
            location.href = "/user/login"
        }, signup: function () {
            location.href = "/user/register"
        }
    })
}), function (t) {
    t.fn.hoverIntent = function (e, n, r, i) {
        var a = {interval: i || 400, sensitivity: 5, timeout: 600};
        a = "object" == typeof e ? t.extend(a, e) : t.isFunction(n) ? t.extend(a, {
            over: e,
            out: n,
            selector: r
        }) : t.extend(a, {over: e, out: e, selector: n});
        var o, s, l, u, c = function (t) {
            o = t.pageX, s = t.pageY
        }, p = function (e, n) {
            return n.hoverIntent_t = clearTimeout(n.hoverIntent_t), Math.sqrt((l - o) * (l - o) + (u - s) * (u - s)) < a.sensitivity ? (t(n).off("mousemove.hoverIntent", c), n.hoverIntent_s = !0, a.over.apply(n, [e])) : (l = o, u = s, n.hoverIntent_t = setTimeout(function () {
                p(e, n)
            }, a.interval), void 0)
        }, h = function (t, e) {
            return e.hoverIntent_t = clearTimeout(e.hoverIntent_t), e.hoverIntent_s = !1, a.out.apply(e, [t])
        }, f = function (e) {
            var n = t.extend({}, e), r = this;
            r.hoverIntent_t && (r.hoverIntent_t = clearTimeout(r.hoverIntent_t)), "mouseenter" === e.type ? (l = n.pageX, u = n.pageY, t(r).on("mousemove.hoverIntent", c), r.hoverIntent_s || (r.hoverIntent_t = setTimeout(function () {
                p(n, r)
            }, a.interval))) : (t(r).off("mousemove.hoverIntent", c), r.hoverIntent_s && (r.hoverIntent_t = setTimeout(function () {
                h(n, r)
            }, a.timeout)))
        };
        return this.on({"mouseenter.hoverIntent": f, "mouseleave.hoverIntent": f}, a.selector)
    }
}(jQuery), define("jquery_hoverIntent", ["jquery"], function (t) {
    return function () {
        var e;
        return e || t.$.fn.hoverIntent
    }
}(this)), define("sfAjax", ["jquery"], function (t) {
    "use strict";
    t.sfAjax = function (e, n, r, i) {
        var a, o, s, l;
        o = t.extend({
            id: e.data("id"),
            "do": e.data("do"),
            type: e.data("type")
        }, r), a = o["do"], s = a.indexOf("/cancel") > 0 ? a.replace("/cancel", "") : a + "/cancel", l = "/api/" + o.type + "/" + o.id + "/" + o["do"], t.post(l, function (t) {
            0 === t.status ? (e.data("do", s), n ? n(t) : location.reload()) : i && i(t)
        })
    }
}), function (t) {
    "function" == typeof define && define.amd ? define("jquery_cookie", ["jquery"], t) : t("object" == typeof exports ? require("jquery") : jQuery)
}(function (t) {
    function e(t) {
        return s.raw ? t : encodeURIComponent(t)
    }

    function n(t) {
        return s.raw ? t : decodeURIComponent(t)
    }

    function r(t) {
        return e(s.json ? JSON.stringify(t) : String(t))
    }

    function i(t) {
        0 === t.indexOf('"') && (t = t.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
        try {
            return t = decodeURIComponent(t.replace(o, " ")), s.json ? JSON.parse(t) : t
        } catch (e) {
        }
    }

    function a(e, n) {
        var r = s.raw ? e : i(e);
        return t.isFunction(n) ? n(r) : r
    }

    var o = /\+/g, s = t.cookie = function (i, o, l) {
        if (void 0 !== o && !t.isFunction(o)) {
            if (l = t.extend({}, s.defaults, l), "number" == typeof l.expires) {
                var u = l.expires, c = l.expires = new Date;
                c.setTime(+c + 864e5 * u)
            }
            return document.cookie = [e(i), "=", r(o), l.expires ? "; expires=" + l.expires.toUTCString() : "", l.path ? "; path=" + l.path : "", l.domain ? "; domain=" + l.domain : "", l.secure ? "; secure" : ""].join("")
        }
        for (var p = i ? void 0 : {}, h = document.cookie ? document.cookie.split("; ") : [], f = 0, d = h.length; d > f; f++) {
            var m = h[f].split("="), g = n(m.shift()), v = m.join("=");
            if (i && i === g) {
                p = a(v, o);
                break
            }
            i || void 0 === (v = a(v)) || (p[g] = v)
        }
        return p
    };
    s.defaults = {}, t.removeCookie = function (e, n) {
        return void 0 === t.cookie(e) ? !1 : (t.cookie(e, "", t.extend({}, n, {expires: -1})), !t.cookie(e))
    }
}), function (t, e, n, r) {
    var i = t(e);
    t.fn.lazyload = function (a) {
        function o() {
            var e = 0;
            l.each(function () {
                var n = t(this);
                if (!u.skip_invisible || n.is(":visible"))if (t.abovethetop(this, u) || t.leftofbegin(this, u)); else if (t.belowthefold(this, u) || t.rightoffold(this, u)) {
                    if (++e > u.failure_limit)return !1
                } else n.trigger("appear"), e = 0
            })
        }

        var s, l = this, u = {
            threshold: 0,
            failure_limit: 0,
            event: "scroll",
            effect: "show",
            container: e,
            data_attribute: "original",
            skip_invisible: !0,
            appear: null,
            load: null,
            placeholder: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
        };
        return a && (r !== a.failurelimit && (a.failure_limit = a.failurelimit, delete a.failurelimit), r !== a.effectspeed && (a.effect_speed = a.effectspeed, delete a.effectspeed), t.extend(u, a)), s = u.container === r || u.container === e ? i : t(u.container), 0 === u.event.indexOf("scroll") && s.bind(u.event, function () {
            return o()
        }), this.each(function () {
            var e = this, n = t(e);
            e.loaded = !1, (n.attr("src") === r || n.attr("src") === !1) && n.is("img") && n.attr("src", u.placeholder), n.one("appear", function () {
                if (!this.loaded) {
                    if (u.appear) {
                        var r = l.length;
                        u.appear.call(e, r, u)
                    }
                    t("<img />").bind("load", function () {
                        var r = n.attr("data-" + u.data_attribute);
                        n.hide(), n.is("img") ? n.attr("src", r) : n.css("background-image", "url('" + r + "')"), n[u.effect](u.effect_speed), e.loaded = !0;
                        var i = t.grep(l, function (t) {
                            return !t.loaded
                        });
                        if (l = t(i), u.load) {
                            var a = l.length;
                            u.load.call(e, a, u)
                        }
                    }).attr("src", n.attr("data-" + u.data_attribute))
                }
            }), 0 !== u.event.indexOf("scroll") && n.bind(u.event, function () {
                e.loaded || n.trigger("appear")
            })
        }), i.bind("resize", function () {
            o()
        }), /(?:iphone|ipod|ipad).*os 5/gi.test(navigator.appVersion) && i.bind("pageshow", function (e) {
            e.originalEvent && e.originalEvent.persisted && l.each(function () {
                t(this).trigger("appear")
            })
        }), t(n).ready(function () {
            o()
        }), this
    }, t.belowthefold = function (n, a) {
        var o;
        return o = a.container === r || a.container === e ? (e.innerHeight ? e.innerHeight : i.height()) + i.scrollTop() : t(a.container).offset().top + t(a.container).height(), o <= t(n).offset().top - a.threshold
    }, t.rightoffold = function (n, a) {
        var o;
        return o = a.container === r || a.container === e ? i.width() + i.scrollLeft() : t(a.container).offset().left + t(a.container).width(), o <= t(n).offset().left - a.threshold
    }, t.abovethetop = function (n, a) {
        var o;
        return o = a.container === r || a.container === e ? i.scrollTop() : t(a.container).offset().top, o >= t(n).offset().top + a.threshold + t(n).height()
    }, t.leftofbegin = function (n, a) {
        var o;
        return o = a.container === r || a.container === e ? i.scrollLeft() : t(a.container).offset().left, o >= t(n).offset().left + a.threshold + t(n).width()
    }, t.inviewport = function (e, n) {
        return !(t.rightoffold(e, n) || t.leftofbegin(e, n) || t.belowthefold(e, n) || t.abovethetop(e, n))
    }, t.extend(t.expr[":"], {
        "below-the-fold": function (e) {
            return t.belowthefold(e, {threshold: 0})
        }, "above-the-top": function (e) {
            return !t.belowthefold(e, {threshold: 0})
        }, "right-of-screen": function (e) {
            return t.rightoffold(e, {threshold: 0})
        }, "left-of-screen": function (e) {
            return !t.rightoffold(e, {threshold: 0})
        }, "in-viewport": function (e) {
            return t.inviewport(e, {threshold: 0})
        }, "above-the-fold": function (e) {
            return !t.belowthefold(e, {threshold: 0})
        }, "right-of-fold": function (e) {
            return t.rightoffold(e, {threshold: 0})
        }, "left-of-fold": function (e) {
            return !t.rightoffold(e, {threshold: 0})
        }
    })
}(jQuery, window, document), define("jquery_lazyload", ["jquery"], function (t) {
    return function () {
        var e;
        return e || t.$.fn.lazyload
    }
}(this)), function () {
    var t = this, e = t._, n = Array.prototype, r = Object.prototype, i = Function.prototype, a = n.push, o = n.slice, s = n.concat, l = r.toString, u = r.hasOwnProperty, c = Array.isArray, p = Object.keys, h = i.bind, f = function (t) {
        return t instanceof f ? t : this instanceof f ? void(this._wrapped = t) : new f(t)
    };
    "undefined" != typeof exports ? ("undefined" != typeof module && module.exports && (exports = module.exports = f), exports._ = f) : t._ = f, f.VERSION = "1.7.0";
    var d = function (t, e, n) {
        if (void 0 === e)return t;
        switch (null == n ? 3 : n) {
            case 1:
                return function (n) {
                    return t.call(e, n)
                };
            case 2:
                return function (n, r) {
                    return t.call(e, n, r)
                };
            case 3:
                return function (n, r, i) {
                    return t.call(e, n, r, i)
                };
            case 4:
                return function (n, r, i, a) {
                    return t.call(e, n, r, i, a)
                }
        }
        return function () {
            return t.apply(e, arguments)
        }
    };
    f.iteratee = function (t, e, n) {
        return null == t ? f.identity : f.isFunction(t) ? d(t, e, n) : f.isObject(t) ? f.matches(t) : f.property(t)
    }, f.each = f.forEach = function (t, e, n) {
        if (null == t)return t;
        e = d(e, n);
        var r, i = t.length;
        if (i === +i)for (r = 0; i > r; r++)e(t[r], r, t); else {
            var a = f.keys(t);
            for (r = 0, i = a.length; i > r; r++)e(t[a[r]], a[r], t)
        }
        return t
    }, f.map = f.collect = function (t, e, n) {
        if (null == t)return [];
        e = f.iteratee(e, n);
        for (var r, i = t.length !== +t.length && f.keys(t), a = (i || t).length, o = Array(a), s = 0; a > s; s++)r = i ? i[s] : s, o[s] = e(t[r], r, t);
        return o
    };
    var m = "Reduce of empty array with no initial value";
    f.reduce = f.foldl = f.inject = function (t, e, n, r) {
        null == t && (t = []), e = d(e, r, 4);
        var i, a = t.length !== +t.length && f.keys(t), o = (a || t).length, s = 0;
        if (arguments.length < 3) {
            if (!o)throw new TypeError(m);
            n = t[a ? a[s++] : s++]
        }
        for (; o > s; s++)i = a ? a[s] : s, n = e(n, t[i], i, t);
        return n
    }, f.reduceRight = f.foldr = function (t, e, n, r) {
        null == t && (t = []), e = d(e, r, 4);
        var i, a = t.length !== +t.length && f.keys(t), o = (a || t).length;
        if (arguments.length < 3) {
            if (!o)throw new TypeError(m);
            n = t[a ? a[--o] : --o]
        }
        for (; o--;)i = a ? a[o] : o, n = e(n, t[i], i, t);
        return n
    }, f.find = f.detect = function (t, e, n) {
        var r;
        return e = f.iteratee(e, n), f.some(t, function (t, n, i) {
            return e(t, n, i) ? (r = t, !0) : void 0
        }), r
    }, f.filter = f.select = function (t, e, n) {
        var r = [];
        return null == t ? r : (e = f.iteratee(e, n), f.each(t, function (t, n, i) {
            e(t, n, i) && r.push(t)
        }), r)
    }, f.reject = function (t, e, n) {
        return f.filter(t, f.negate(f.iteratee(e)), n)
    }, f.every = f.all = function (t, e, n) {
        if (null == t)return !0;
        e = f.iteratee(e, n);
        var r, i, a = t.length !== +t.length && f.keys(t), o = (a || t).length;
        for (r = 0; o > r; r++)if (i = a ? a[r] : r, !e(t[i], i, t))return !1;
        return !0
    }, f.some = f.any = function (t, e, n) {
        if (null == t)return !1;
        e = f.iteratee(e, n);
        var r, i, a = t.length !== +t.length && f.keys(t), o = (a || t).length;
        for (r = 0; o > r; r++)if (i = a ? a[r] : r, e(t[i], i, t))return !0;
        return !1
    }, f.contains = f.include = function (t, e) {
        return null == t ? !1 : (t.length !== +t.length && (t = f.values(t)), f.indexOf(t, e) >= 0)
    }, f.invoke = function (t, e) {
        var n = o.call(arguments, 2), r = f.isFunction(e);
        return f.map(t, function (t) {
            return (r ? e : t[e]).apply(t, n)
        })
    }, f.pluck = function (t, e) {
        return f.map(t, f.property(e))
    }, f.where = function (t, e) {
        return f.filter(t, f.matches(e))
    }, f.findWhere = function (t, e) {
        return f.find(t, f.matches(e))
    }, f.max = function (t, e, n) {
        var r, i, a = -1 / 0, o = -1 / 0;
        if (null == e && null != t) {
            t = t.length === +t.length ? t : f.values(t);
            for (var s = 0, l = t.length; l > s; s++)r = t[s], r > a && (a = r)
        } else e = f.iteratee(e, n), f.each(t, function (t, n, r) {
            i = e(t, n, r), (i > o || i === -1 / 0 && a === -1 / 0) && (a = t, o = i)
        });
        return a
    }, f.min = function (t, e, n) {
        var r, i, a = 1 / 0, o = 1 / 0;
        if (null == e && null != t) {
            t = t.length === +t.length ? t : f.values(t);
            for (var s = 0, l = t.length; l > s; s++)r = t[s], a > r && (a = r)
        } else e = f.iteratee(e, n), f.each(t, function (t, n, r) {
            i = e(t, n, r), (o > i || 1 / 0 === i && 1 / 0 === a) && (a = t, o = i)
        });
        return a
    }, f.shuffle = function (t) {
        for (var e, n = t && t.length === +t.length ? t : f.values(t), r = n.length, i = Array(r), a = 0; r > a; a++)e = f.random(0, a), e !== a && (i[a] = i[e]), i[e] = n[a];
        return i
    }, f.sample = function (t, e, n) {
        return null == e || n ? (t.length !== +t.length && (t = f.values(t)), t[f.random(t.length - 1)]) : f.shuffle(t).slice(0, Math.max(0, e))
    }, f.sortBy = function (t, e, n) {
        return e = f.iteratee(e, n), f.pluck(f.map(t, function (t, n, r) {
            return {value: t, index: n, criteria: e(t, n, r)}
        }).sort(function (t, e) {
            var n = t.criteria, r = e.criteria;
            if (n !== r) {
                if (n > r || void 0 === n)return 1;
                if (r > n || void 0 === r)return -1
            }
            return t.index - e.index
        }), "value")
    };
    var g = function (t) {
        return function (e, n, r) {
            var i = {};
            return n = f.iteratee(n, r), f.each(e, function (r, a) {
                var o = n(r, a, e);
                t(i, r, o)
            }), i
        }
    };
    f.groupBy = g(function (t, e, n) {
        f.has(t, n) ? t[n].push(e) : t[n] = [e]
    }), f.indexBy = g(function (t, e, n) {
        t[n] = e
    }), f.countBy = g(function (t, e, n) {
        f.has(t, n) ? t[n]++ : t[n] = 1
    }), f.sortedIndex = function (t, e, n, r) {
        n = f.iteratee(n, r, 1);
        for (var i = n(e), a = 0, o = t.length; o > a;) {
            var s = a + o >>> 1;
            n(t[s]) < i ? a = s + 1 : o = s
        }
        return a
    }, f.toArray = function (t) {
        return t ? f.isArray(t) ? o.call(t) : t.length === +t.length ? f.map(t, f.identity) : f.values(t) : []
    }, f.size = function (t) {
        return null == t ? 0 : t.length === +t.length ? t.length : f.keys(t).length
    }, f.partition = function (t, e, n) {
        e = f.iteratee(e, n);
        var r = [], i = [];
        return f.each(t, function (t, n, a) {
            (e(t, n, a) ? r : i).push(t)
        }), [r, i]
    }, f.first = f.head = f.take = function (t, e, n) {
        return null == t ? void 0 : null == e || n ? t[0] : 0 > e ? [] : o.call(t, 0, e)
    }, f.initial = function (t, e, n) {
        return o.call(t, 0, Math.max(0, t.length - (null == e || n ? 1 : e)))
    }, f.last = function (t, e, n) {
        return null == t ? void 0 : null == e || n ? t[t.length - 1] : o.call(t, Math.max(t.length - e, 0))
    }, f.rest = f.tail = f.drop = function (t, e, n) {
        return o.call(t, null == e || n ? 1 : e)
    }, f.compact = function (t) {
        return f.filter(t, f.identity)
    };
    var v = function (t, e, n, r) {
        if (e && f.every(t, f.isArray))return s.apply(r, t);
        for (var i = 0, o = t.length; o > i; i++) {
            var l = t[i];
            f.isArray(l) || f.isArguments(l) ? e ? a.apply(r, l) : v(l, e, n, r) : n || r.push(l)
        }
        return r
    };
    f.flatten = function (t, e) {
        return v(t, e, !1, [])
    }, f.without = function (t) {
        return f.difference(t, o.call(arguments, 1))
    }, f.uniq = f.unique = function (t, e, n, r) {
        if (null == t)return [];
        f.isBoolean(e) || (r = n, n = e, e = !1), null != n && (n = f.iteratee(n, r));
        for (var i = [], a = [], o = 0, s = t.length; s > o; o++) {
            var l = t[o];
            if (e)o && a === l || i.push(l), a = l; else if (n) {
                var u = n(l, o, t);
                f.indexOf(a, u) < 0 && (a.push(u), i.push(l))
            } else f.indexOf(i, l) < 0 && i.push(l)
        }
        return i
    }, f.union = function () {
        return f.uniq(v(arguments, !0, !0, []))
    }, f.intersection = function (t) {
        if (null == t)return [];
        for (var e = [], n = arguments.length, r = 0, i = t.length; i > r; r++) {
            var a = t[r];
            if (!f.contains(e, a)) {
                for (var o = 1; n > o && f.contains(arguments[o], a); o++);
                o === n && e.push(a)
            }
        }
        return e
    }, f.difference = function (t) {
        var e = v(o.call(arguments, 1), !0, !0, []);
        return f.filter(t, function (t) {
            return !f.contains(e, t)
        })
    }, f.zip = function (t) {
        if (null == t)return [];
        for (var e = f.max(arguments, "length").length, n = Array(e), r = 0; e > r; r++)n[r] = f.pluck(arguments, r);
        return n
    }, f.object = function (t, e) {
        if (null == t)return {};
        for (var n = {}, r = 0, i = t.length; i > r; r++)e ? n[t[r]] = e[r] : n[t[r][0]] = t[r][1];
        return n
    }, f.indexOf = function (t, e, n) {
        if (null == t)return -1;
        var r = 0, i = t.length;
        if (n) {
            if ("number" != typeof n)return r = f.sortedIndex(t, e), t[r] === e ? r : -1;
            r = 0 > n ? Math.max(0, i + n) : n
        }
        for (; i > r; r++)if (t[r] === e)return r;
        return -1
    }, f.lastIndexOf = function (t, e, n) {
        if (null == t)return -1;
        var r = t.length;
        for ("number" == typeof n && (r = 0 > n ? r + n + 1 : Math.min(r, n + 1)); --r >= 0;)if (t[r] === e)return r;
        return -1
    }, f.range = function (t, e, n) {
        arguments.length <= 1 && (e = t || 0, t = 0), n = n || 1;
        for (var r = Math.max(Math.ceil((e - t) / n), 0), i = Array(r), a = 0; r > a; a++, t += n)i[a] = t;
        return i
    };
    var y = function () {
    };
    f.bind = function (t, e) {
        var n, r;
        if (h && t.bind === h)return h.apply(t, o.call(arguments, 1));
        if (!f.isFunction(t))throw new TypeError("Bind must be called on a function");
        return n = o.call(arguments, 2), r = function () {
            if (!(this instanceof r))return t.apply(e, n.concat(o.call(arguments)));
            y.prototype = t.prototype;
            var i = new y;
            y.prototype = null;
            var a = t.apply(i, n.concat(o.call(arguments)));
            return f.isObject(a) ? a : i
        }
    }, f.partial = function (t) {
        var e = o.call(arguments, 1);
        return function () {
            for (var n = 0, r = e.slice(), i = 0, a = r.length; a > i; i++)r[i] === f && (r[i] = arguments[n++]);
            for (; n < arguments.length;)r.push(arguments[n++]);
            return t.apply(this, r)
        }
    }, f.bindAll = function (t) {
        var e, n, r = arguments.length;
        if (1 >= r)throw new Error("bindAll must be passed function names");
        for (e = 1; r > e; e++)n = arguments[e], t[n] = f.bind(t[n], t);
        return t
    }, f.memoize = function (t, e) {
        var n = function (r) {
            var i = n.cache, a = e ? e.apply(this, arguments) : r;
            return f.has(i, a) || (i[a] = t.apply(this, arguments)), i[a]
        };
        return n.cache = {}, n
    }, f.delay = function (t, e) {
        var n = o.call(arguments, 2);
        return setTimeout(function () {
            return t.apply(null, n)
        }, e)
    }, f.defer = function (t) {
        return f.delay.apply(f, [t, 1].concat(o.call(arguments, 1)))
    }, f.throttle = function (t, e, n) {
        var r, i, a, o = null, s = 0;
        n || (n = {});
        var l = function () {
            s = n.leading === !1 ? 0 : f.now(), o = null, a = t.apply(r, i), o || (r = i = null)
        };
        return function () {
            var u = f.now();
            s || n.leading !== !1 || (s = u);
            var c = e - (u - s);
            return r = this, i = arguments, 0 >= c || c > e ? (clearTimeout(o), o = null, s = u, a = t.apply(r, i), o || (r = i = null)) : o || n.trailing === !1 || (o = setTimeout(l, c)), a
        }
    }, f.debounce = function (t, e, n) {
        var r, i, a, o, s, l = function () {
            var u = f.now() - o;
            e > u && u > 0 ? r = setTimeout(l, e - u) : (r = null, n || (s = t.apply(a, i), r || (a = i = null)))
        };
        return function () {
            a = this, i = arguments, o = f.now();
            var u = n && !r;
            return r || (r = setTimeout(l, e)), u && (s = t.apply(a, i), a = i = null), s
        }
    }, f.wrap = function (t, e) {
        return f.partial(e, t)
    }, f.negate = function (t) {
        return function () {
            return !t.apply(this, arguments)
        }
    }, f.compose = function () {
        var t = arguments, e = t.length - 1;
        return function () {
            for (var n = e, r = t[e].apply(this, arguments); n--;)r = t[n].call(this, r);
            return r
        }
    }, f.after = function (t, e) {
        return function () {
            return --t < 1 ? e.apply(this, arguments) : void 0
        }
    }, f.before = function (t, e) {
        var n;
        return function () {
            return --t > 0 ? n = e.apply(this, arguments) : e = null, n
        }
    }, f.once = f.partial(f.before, 2), f.keys = function (t) {
        if (!f.isObject(t))return [];
        if (p)return p(t);
        var e = [];
        for (var n in t)f.has(t, n) && e.push(n);
        return e
    }, f.values = function (t) {
        for (var e = f.keys(t), n = e.length, r = Array(n), i = 0; n > i; i++)r[i] = t[e[i]];
        return r
    }, f.pairs = function (t) {
        for (var e = f.keys(t), n = e.length, r = Array(n), i = 0; n > i; i++)r[i] = [e[i], t[e[i]]];
        return r
    }, f.invert = function (t) {
        for (var e = {}, n = f.keys(t), r = 0, i = n.length; i > r; r++)e[t[n[r]]] = n[r];
        return e
    }, f.functions = f.methods = function (t) {
        var e = [];
        for (var n in t)f.isFunction(t[n]) && e.push(n);
        return e.sort()
    }, f.extend = function (t) {
        if (!f.isObject(t))return t;
        for (var e, n, r = 1, i = arguments.length; i > r; r++) {
            e = arguments[r];
            for (n in e)u.call(e, n) && (t[n] = e[n])
        }
        return t
    }, f.pick = function (t, e, n) {
        var r, i = {};
        if (null == t)return i;
        if (f.isFunction(e)) {
            e = d(e, n);
            for (r in t) {
                var a = t[r];
                e(a, r, t) && (i[r] = a)
            }
        } else {
            var l = s.apply([], o.call(arguments, 1));
            t = new Object(t);
            for (var u = 0, c = l.length; c > u; u++)r = l[u], r in t && (i[r] = t[r])
        }
        return i
    }, f.omit = function (t, e, n) {
        if (f.isFunction(e))e = f.negate(e); else {
            var r = f.map(s.apply([], o.call(arguments, 1)), String);
            e = function (t, e) {
                return !f.contains(r, e)
            }
        }
        return f.pick(t, e, n)
    }, f.defaults = function (t) {
        if (!f.isObject(t))return t;
        for (var e = 1, n = arguments.length; n > e; e++) {
            var r = arguments[e];
            for (var i in r)void 0 === t[i] && (t[i] = r[i])
        }
        return t
    }, f.clone = function (t) {
        return f.isObject(t) ? f.isArray(t) ? t.slice() : f.extend({}, t) : t
    }, f.tap = function (t, e) {
        return e(t), t
    };
    var b = function (t, e, n, r) {
        if (t === e)return 0 !== t || 1 / t === 1 / e;
        if (null == t || null == e)return t === e;
        t instanceof f && (t = t._wrapped), e instanceof f && (e = e._wrapped);
        var i = l.call(t);
        if (i !== l.call(e))return !1;
        switch (i) {
            case"[object RegExp]":
            case"[object String]":
                return "" + t == "" + e;
            case"[object Number]":
                return +t !== +t ? +e !== +e : 0 === +t ? 1 / +t === 1 / e : +t === +e;
            case"[object Date]":
            case"[object Boolean]":
                return +t === +e
        }
        if ("object" != typeof t || "object" != typeof e)return !1;
        for (var a = n.length; a--;)if (n[a] === t)return r[a] === e;
        var o = t.constructor, s = e.constructor;
        if (o !== s && "constructor"in t && "constructor"in e && !(f.isFunction(o) && o instanceof o && f.isFunction(s) && s instanceof s))return !1;
        n.push(t), r.push(e);
        var u, c;
        if ("[object Array]" === i) {
            if (u = t.length, c = u === e.length)for (; u-- && (c = b(t[u], e[u], n, r)););
        } else {
            var p, h = f.keys(t);
            if (u = h.length, c = f.keys(e).length === u)for (; u-- && (p = h[u], c = f.has(e, p) && b(t[p], e[p], n, r)););
        }
        return n.pop(), r.pop(), c
    };
    f.isEqual = function (t, e) {
        return b(t, e, [], [])
    }, f.isEmpty = function (t) {
        if (null == t)return !0;
        if (f.isArray(t) || f.isString(t) || f.isArguments(t))return 0 === t.length;
        for (var e in t)if (f.has(t, e))return !1;
        return !0
    }, f.isElement = function (t) {
        return !(!t || 1 !== t.nodeType)
    }, f.isArray = c || function (t) {
            return "[object Array]" === l.call(t)
        }, f.isObject = function (t) {
        var e = typeof t;
        return "function" === e || "object" === e && !!t
    }, f.each(["Arguments", "Function", "String", "Number", "Date", "RegExp"], function (t) {
        f["is" + t] = function (e) {
            return l.call(e) === "[object " + t + "]"
        }
    }), f.isArguments(arguments) || (f.isArguments = function (t) {
        return f.has(t, "callee")
    }), "function" != typeof/./ && (f.isFunction = function (t) {
        return "function" == typeof t || !1
    }), f.isFinite = function (t) {
        return isFinite(t) && !isNaN(parseFloat(t))
    }, f.isNaN = function (t) {
        return f.isNumber(t) && t !== +t
    }, f.isBoolean = function (t) {
        return t === !0 || t === !1 || "[object Boolean]" === l.call(t)
    }, f.isNull = function (t) {
        return null === t
    }, f.isUndefined = function (t) {
        return void 0 === t
    }, f.has = function (t, e) {
        return null != t && u.call(t, e)
    }, f.noConflict = function () {
        return t._ = e, this
    }, f.identity = function (t) {
        return t
    }, f.constant = function (t) {
        return function () {
            return t
        }
    }, f.noop = function () {
    }, f.property = function (t) {
        return function (e) {
            return e[t]
        }
    }, f.matches = function (t) {
        var e = f.pairs(t), n = e.length;
        return function (t) {
            if (null == t)return !n;
            t = new Object(t);
            for (var r = 0; n > r; r++) {
                var i = e[r], a = i[0];
                if (i[1] !== t[a] || !(a in t))return !1
            }
            return !0
        }
    }, f.times = function (t, e, n) {
        var r = Array(Math.max(0, t));
        e = d(e, n, 1);
        for (var i = 0; t > i; i++)r[i] = e(i);
        return r
    }, f.random = function (t, e) {
        return null == e && (e = t, t = 0), t + Math.floor(Math.random() * (e - t + 1))
    }, f.now = Date.now || function () {
            return (new Date).getTime()
        };
    var k = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#x27;",
        "`": "&#x60;"
    }, _ = f.invert(k), x = function (t) {
        var e = function (e) {
            return t[e]
        }, n = "(?:" + f.keys(t).join("|") + ")", r = RegExp(n), i = RegExp(n, "g");
        return function (t) {
            return t = null == t ? "" : "" + t, r.test(t) ? t.replace(i, e) : t
        }
    };
    f.escape = x(k), f.unescape = x(_), f.result = function (t, e) {
        if (null == t)return void 0;
        var n = t[e];
        return f.isFunction(n) ? t[e]() : n
    };
    var w = 0;
    f.uniqueId = function (t) {
        var e = ++w + "";
        return t ? t + e : e
    }, f.templateSettings = {evaluate: /<%([\s\S]+?)%>/g, interpolate: /<%=([\s\S]+?)%>/g, escape: /<%-([\s\S]+?)%>/g};
    var S = /(.)^/, $ = {
        "'": "'",
        "\\": "\\",
        "\r": "r",
        "\n": "n",
        "\u2028": "u2028",
        "\u2029": "u2029"
    }, P = /\\|'|\r|\n|\u2028|\u2029/g, A = function (t) {
        return "\\" + $[t]
    };
    f.template = function (t, e, n) {
        !e && n && (e = n), e = f.defaults({}, e, f.templateSettings);
        var r = RegExp([(e.escape || S).source, (e.interpolate || S).source, (e.evaluate || S).source].join("|") + "|$", "g"), i = 0, a = "__p+='";
        t.replace(r, function (e, n, r, o, s) {
            return a += t.slice(i, s).replace(P, A), i = s + e.length, n ? a += "'+\n((__t=(" + n + "))==null?'':_.escape(__t))+\n'" : r ? a += "'+\n((__t=(" + r + "))==null?'':__t)+\n'" : o && (a += "';\n" + o + "\n__p+='"), e
        }), a += "';\n", e.variable || (a = "with(obj||{}){\n" + a + "}\n"), a = "var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};\n" + a + "return __p;\n";
        try {
            var o = new Function(e.variable || "obj", "_", a)
        } catch (s) {
            throw s.source = a, s
        }
        var l = function (t) {
            return o.call(this, t, f)
        }, u = e.variable || "obj";
        return l.source = "function(" + u + "){\n" + a + "}", l
    }, f.chain = function (t) {
        var e = f(t);
        return e._chain = !0, e
    };
    var C = function (t) {
        return this._chain ? f(t).chain() : t
    };
    f.mixin = function (t) {
        f.each(f.functions(t), function (e) {
            var n = f[e] = t[e];
            f.prototype[e] = function () {
                var t = [this._wrapped];
                return a.apply(t, arguments), C.call(this, n.apply(f, t))
            }
        })
    }, f.mixin(f), f.each(["pop", "push", "reverse", "shift", "sort", "splice", "unshift"], function (t) {
        var e = n[t];
        f.prototype[t] = function () {
            var n = this._wrapped;
            return e.apply(n, arguments), "shift" !== t && "splice" !== t || 0 !== n.length || delete n[0], C.call(this, n)
        }
    }), f.each(["concat", "join", "slice"], function (t) {
        var e = n[t];
        f.prototype[t] = function () {
            return C.call(this, e.apply(this._wrapped, arguments))
        }
    }), f.prototype.value = function () {
        return this._wrapped
    }, "function" == typeof define && define.amd && define("underscore", [], function () {
        return f
    })
}.call(this), function (t) {
    function e(e, n, r, i) {
        var a = {
            data: i || 0 === i || i === !1 ? i : n ? n.data : {},
            _wrap: n ? n._wrap : null,
            tmpl: null,
            parent: n || null,
            nodes: [],
            calls: u,
            nest: c,
            wrap: p,
            html: h,
            update: f
        };
        return e && t.extend(a, e, {
            nodes: [],
            parent: n
        }), r && (a.tmpl = r, a._ctnt = a._ctnt || a.tmpl(t, a), a.key = ++_, (w.length ? b : y)[_] = a), a
    }

    function n(e, i, a) {
        var o, s = a ? t.map(a, function (t) {
            return "string" == typeof t ? e.key ? t.replace(/(<\w+)(?=[\s>])(?![^>]*_tmplitem)([^>]*)/g, "$1 " + g + '="' + e.key + '" $2') : t : n(t, e, t._ctnt)
        }) : e;
        return i ? s : (s = s.join(""), s.replace(/^\s*([^<\s][^<]*)?(<[\w\W]+>)([^>]*[^>\s])?\s*$/, function (e, n, i, a) {
            o = t(i).get(), l(o), n && (o = r(n).concat(o)), a && (o = o.concat(r(a)))
        }), o ? o : r(s))
    }

    function r(e) {
        var n = document.createElement("div");
        return n.innerHTML = e, t.makeArray(n.childNodes)
    }

    function i(e) {
        return new Function("jQuery", "$item", "var $=jQuery,call,__=[],$data=$item.data;with($data){__.push('" + t.trim(e).replace(/([\\'])/g, "\\$1").replace(/[\r\t\n]/g, " ").replace(/\$\{([^\}]*)\}/g, "{{= $1}}").replace(/\{\{(\/?)(\w+|.)(?:\(((?:[^\}]|\}(?!\}))*?)?\))?(?:\s+(.*?)?)?(\(((?:[^\}]|\}(?!\}))*?)\))?\s*\}\}/g, function (e, n, r, i, a, s, l) {
                var u, c, p, h = t.tmpl.tag[r];
                if (!h)throw"Unknown template tag: " + r;
                return u = h._default || [], s && !/\w$/.test(a) && (a += s, s = ""), a ? (a = o(a), l = l ? "," + o(l) + ")" : s ? ")" : "", c = s ? a.indexOf(".") > -1 ? a + o(s) : "(" + a + ").call($item" + l : a, p = s ? c : "(typeof(" + a + ")==='function'?(" + a + ").call($item):(" + a + "))") : p = c = u.$1 || "null", i = o(i), "');" + h[n ? "close" : "open"].split("$notnull_1").join(a ? "typeof(" + a + ")!=='undefined' && (" + a + ")!=null" : "true").split("$1a").join(p).split("$1").join(c).split("$2").join(i || u.$2 || "") + "__.push('"
            }) + "');}return __;")
    }

    function a(e, r) {
        e._wrap = n(e, !0, t.isArray(r) ? r : [v.test(r) ? r : t(r).html()]).join("")
    }

    function o(t) {
        return t ? t.replace(/\\'/g, "'").replace(/\\\\/g, "\\") : null
    }

    function s(t) {
        var e = document.createElement("div");
        return e.appendChild(t.cloneNode(!0)), e.innerHTML
    }

    function l(n) {
        function r(n) {
            function r(t) {
                t += u, o = c[t] = c[t] || e(o, y[o.parent.key + u] || o.parent)
            }

            var i, a, o, s, l = n;
            if (s = n.getAttribute(g)) {
                for (; l.parentNode && 1 === (l = l.parentNode).nodeType && !(i = l.getAttribute(g)););
                i !== s && (l = l.parentNode ? 11 === l.nodeType ? 0 : l.getAttribute(g) || 0 : 0, (o = y[s]) || (o = b[s], o = e(o, y[l] || b[l]), o.key = ++_, y[_] = o), x && r(s)), n.removeAttribute(g)
            } else x && (o = t.data(n, "tmplItem")) && (r(o.key), y[o.key] = o, l = t.data(n.parentNode, "tmplItem"), l = l ? l.key : 0);
            if (o) {
                for (a = o; a && a.key != l;)a.nodes.push(n), a = a.parent;
                delete o._ctnt, delete o._wrap, t.data(n, "tmplItem", o)
            }
        }

        var i, a, o, s, l, u = "_" + x, c = {};
        for (o = 0, s = n.length; s > o; o++)if (1 === (i = n[o]).nodeType) {
            for (a = i.getElementsByTagName("*"), l = a.length - 1; l >= 0; l--)r(a[l]);
            r(i)
        }
    }

    function u(t, e, n, r) {
        return t ? void w.push({_: t, tmpl: e, item: this, data: n, options: r}) : w.pop()
    }

    function c(e, n, r) {
        return t.tmpl(t.template(e), n, r, this)
    }

    function p(e, n) {
        var r = e.options || {};
        return r.wrapped = n, t.tmpl(t.template(e.tmpl), e.data, r, e.item)
    }

    function h(e, n) {
        var r = this._wrap;
        return t.map(t(t.isArray(r) ? r.join("") : r).filter(e || "*"), function (t) {
            return n ? t.innerText || t.textContent : t.outerHTML || s(t)
        })
    }

    function f() {
        var e = this.nodes;
        t.tmpl(null, null, null, this).insertBefore(e[0]), t(e).remove()
    }

    var d, m = t.fn.domManip, g = "_tmplitem", v = /^[^<]*(<[\w\W]+>)[^>]*$|\{\{\! /, y = {}, b = {}, k = {
        key: 0,
        data: {}
    }, _ = 0, x = 0, w = [];
    t.each({
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith"
    }, function (e, n) {
        t.fn[e] = function (r) {
            var i, a, o, s, l = [], u = t(r), c = 1 === this.length && this[0].parentNode;
            if (d = y || {}, c && 11 === c.nodeType && 1 === c.childNodes.length && 1 === u.length)u[n](this[0]), l = this; else {
                for (a = 0, o = u.length; o > a; a++)x = a, i = (a > 0 ? this.clone(!0) : this).get(), t(u[a])[n](i), l = l.concat(i);
                x = 0, l = this.pushStack(l, e, u.selector)
            }
            return s = d, d = null, t.tmpl.complete(s), l
        }
    }), t.fn.extend({
        tmpl: function (e, n, r) {
            return t.tmpl(this[0], e, n, r)
        }, tmplItem: function () {
            return t.tmplItem(this[0])
        }, template: function (e) {
            return t.template(e, this[0])
        }, domManip: function (e, n, r) {
            if (e[0] && t.isArray(e[0])) {
                for (var i, a = t.makeArray(arguments), o = e[0], s = o.length, l = 0; s > l && !(i = t.data(o[l++], "tmplItem")););
                i && x && (a[2] = function (e) {
                    t.tmpl.afterManip(this, e, r)
                }), m.apply(this, a)
            } else m.apply(this, arguments);
            return x = 0, !d && t.tmpl.complete(y), this
        }
    }), t.extend({
        tmpl: function (r, i, o, s) {
            var l, u = !s;
            if (u)s = k, r = t.template[r] || t.template(null, r), b = {}; else if (!r)return r = s.tmpl, y[s.key] = s, s.nodes = [], s.wrapped && a(s, s.wrapped), t(n(s, null, s.tmpl(t, s)));
            return r ? ("function" == typeof i && (i = i.call(s || {})), o && o.wrapped && a(o, o.wrapped), l = t.isArray(i) ? t.map(i, function (t) {
                return t ? e(o, s, r, t) : null
            }) : [e(o, s, r, i)], u ? t(n(s, null, l)) : l) : []
        }, tmplItem: function (e) {
            var n;
            for (e instanceof t && (e = e[0]); e && 1 === e.nodeType && !(n = t.data(e, "tmplItem")) && (e = e.parentNode););
            return n || k
        }, template: function (e, n) {
            return n ? ("string" == typeof n ? n = i(n) : n instanceof t && (n = n[0] || {}), n.nodeType && (n = t.data(n, "tmpl") || t.data(n, "tmpl", i(n.innerHTML))), "string" == typeof e ? t.template[e] = n : n) : e ? "string" != typeof e ? t.template(null, e) : t.template[e] || t.template(null, v.test(e) ? e : t(e)) : null
        }, encode: function (t) {
            return ("" + t).split("<").join("&lt;").split(">").join("&gt;").split('"').join("&#34;").split("'").join("&#39;")
        }
    }), t.extend(t.tmpl, {
        tag: {
            tmpl: {
                _default: {$2: "null"},
                open: "if($notnull_1){__=__.concat($item.nest($1,$2));}"
            },
            wrap: {
                _default: {$2: "null"},
                open: "$item.calls(__,$1,$2);__=[];",
                close: "call=$item.calls();__=call._.concat($item.wrap(call,__));"
            },
            each: {
                _default: {$2: "$index, $value"},
                open: "if($notnull_1){$.each($1a,function($2){with(this){",
                close: "}});}"
            },
            "if": {open: "if(($notnull_1) && $1a){", close: "}"},
            "else": {_default: {$1: "true"}, open: "}else if(($notnull_1) && $1a){"},
            html: {open: "if($notnull_1){__.push($1a);}"},
            "=": {_default: {$1: "$data"}, open: "if($notnull_1){__.push($.encode($1a));}"},
            "!": {open: ""}
        }, complete: function () {
            y = {}
        }, afterManip: function (e, n, r) {
            var i = 11 === n.nodeType ? t.makeArray(n.childNodes) : 1 === n.nodeType ? [n] : [];
            r.call(e, n), l(i), x++
        }
    })
}(jQuery), define("jquery_tmpl", ["jquery"], function (t) {
    return function () {
        var e;
        return e || t.$.fn.tmpl
    }
}(this)), define("typeHelper", ["jquery", "jquery_tmpl"], function (t) {
    "use strict";
    var e, n, r, i, a, o, s, l;
    s = '<ul class="dropdown-menu" role="menu"></ul>', i = void 0, e = void 0, r = void 0, n = void 0, a = void 0, o = ["gmail.com", "qq.com", "163.com", "hotmail.com", "sina.com", "126.com", "live.com", "live.cn", "vip.sina.com", "vip.qq.com", "sina.cn", "sohu.com", "139.com", "wo.com.cn", "189.cn", "21cn.com"], l = '<button class="btn btn-default result" type="button">${result}</button><a href="#" class="i-cancel ml10 delete-result">&times;</a>', t.fn.typeHelperOld = function (u) {
        var c;
        i = t.extend({
            data: null,
            tpl: '<li><a href="#" data-value="${name}">${name}</a></li>',
            defaultList: [],
            showNum: 5,
            remoteData: null,
            submitKey: null,
            onlyResult: !0,
            autoSelect: !0,
            emailMode: !1,
            insertHandler: function () {
            }
        }, u), c = [], n = t(this), 0 !== n.length && (n.after(t("<div></div>").addClass("typehelper")).siblings(".typehelper").append(n), e = n.parent().css("position", "relative"), t.tmpl(s).insertAfter(n), r = e.find("ul").hide().css("minWidth", n.outerWidth()), t.each(i.defaultList, function (e, n) {
            t.tmpl(i.tpl, n).appendTo(r)
        }), n.on("focus", function () {
            i.defaultList.length > 0 && t(this).parent().trigger("show.typehelper")
        }), n.on("input", function () {
            t(this).parent().trigger("search.typehelper"), t(this).parent().trigger("show.typehelper")
        }), n.on("keydown", function (e) {
            t(this).parent().trigger("select.typehelper", e)
        }), r.delegate("li", "mouseover", function () {
            t(this).siblings("li").removeClass("active"), t(this).addClass("active")
        }), n.on("blur", function () {
            var e, n;
            t(this).parent().trigger("hide.typehelper"), i.autoSelect && (t(this).siblings("ul").find(".active").length > 0 ? (e = r.find(".active").index(), n = c[e], t(this).parent().trigger("insert.typehelper", [r.find(".active a"), n])) : t(this).val(""))
        }), e.on("show.typehelper", function () {
            t(this).find("ul").show()
        }), e.on("hide.typehelper", function () {
            t(this).find("ul").hide()
        }), e.on("insert.typehelper", function (a, o, s) {
            e = t(this), n = e.find("input"), r = e.find("ul"), n.val(t(o).data("value")), t(".result", e).length > 0 && (t(".result", e).remove(), e.find(".delete-result").remove()), i.onlyResult && (n.hide(), r.after(t.tmpl(l, {result: n.val()})), i.submitKey && n.data(i.submitKey, t(o).data(i.submitKey)), e.find(".result").on("click", function () {
                t(this).siblings(".delete-result").remove(), t(this).remove(), n.show().focus(), e.trigger("search.typehelper")
            }), e.find(".delete-result").on("click", function () {
                return t(this).siblings(".result").remove(), t(this).remove(), n.val("").show(), !1
            })), e.parents(".form-group").next(".form-group").find("input").focus(), i.insertHandler(s)
        }), e.on("select.typehelper", function (i, a) {
            var o, s, l, u, p, h;
            if (e = t(this), n = e.find("input"), r = e.find("ul"), o = r.find("li"), a)switch (a.keyCode) {
                case 38:
                    a.preventDefault(), o.length && (r.find(".active").length ? r.find(".active").removeClass("active").prev("li").addClass("active") : o.last().addClass("active"));
                    break;
                case 40:
                    a.preventDefault(), o.length && (r.find(".active").length ? r.find(".active").removeClass("active").next("li").addClass("active") : o.first().addClass("active"));
                    break;
                case 13:
                    if (a.stopPropagation(), a.preventDefault(), r.find(".active").length <= 0)return;
                    u = r.find(".active").index(), h = c[u], e.trigger("insert.typehelper", [r.find(".active a"), h]), e.trigger("hide.typehelper");
                    break;
                case 9:
                    s = t(this).parents("form").find("input"), l = s.index(t("input:focus")), -1 !== l && (p = s.slice(l + 1), p.length && p.each(function () {
                        var e, n;
                        return n = t(this).attr("type"), e = ["text", "email", "password", "url"], t(this).val() || -1 === e.indexOf(n) ? void 0 : (t(this).focus(), !1)
                    }));
                    break;
                case 27:
                    a.preventDefault();
                    break;
                default:
                    return
            }
        }), e.on("search.typehelper", function () {
            var s, l, u, p;
            if (e = t(this), n = e.find("input"), r = e.find("ul"), n.val().length)if (u = [], i.remoteData)i.remoteData(n.val(), function (e) {
                c = e, u = e, u.length > 0 && (r.children().remove(), t.tmpl(i.tpl, u).appendTo(r), 1 === u.length && r.children().first().addClass("active"))
            }); else {
                for (p = 0, i.emailMode && (i.data = [{name: n.val()}], t.each(o, function (t, e) {
                    i.data.push({name: n.val().replace(/@.*$/, "") + "@" + e})
                })), a = i.data.length, l = 0; a > l && (s = i.data[l], !(0 === s.name.toLowerCase().indexOf(n.val().toLowerCase()) && (u.push(s), p++, p >= i.showNum)));)l++;
                u.length > 0 && (r.children().remove(), t.tmpl(i.tpl, u).appendTo(r), (1 === u.length || i.emailMode) && r.children().first().addClass("active"))
            }
        }), n.val().length > 0 && e.trigger("insert.typehelper", n))
    }
}), define("main", ["sfModal", "mobile", "jquery_hoverIntent", "sfAjax", "bootstrap", "jquery_cookie", "jquery_lazyload", "underscore", "typeHelper"], function (t, e) {
    var n, r, i, a, o, s, l, u, c, p, h, f, d;
    return l = function () {
        var t, e, n;
        n = "test", e = window.sessionStorage;
        try {
            return e.setItem(n, "1"), e.removeItem(n), !0
        } catch (r) {
            return t = r, !1
        }
    }, f = function (t) {
        var e;
        if (l() && !(t > 500))return e = localStorage.getItem("show-app-promotion-bar"), e = e || !0, e !== !0 ? $(".app-promotion-bar").hide() : $(".app-promotion-bar").show(), $("body").on("click", ".close", function () {
            return $(".app-promotion-bar").hide(), localStorage.setItem("show-app-promotion-bar", !1)
        }), $("body").on("click", ".icon", function () {
            return $(".app-promotion-bar").hide(), localStorage.setItem("show-app-promotion-bar", !1)
        })
    }, f(document.body.clientWidth), a = function (t) {
        var e, n;
        switch (n = "", e = new Date(t), e.getDay()) {
            case 0:
                n = "周日";
                break;
            case 1:
                n = "周一";
                break;
            case 2:
                n = "周二";
                break;
            case 3:
                n = "周三";
                break;
            case 4:
                n = "周四";
                break;
            case 5:
                n = "周五";
                break;
            case 6:
                n = "周六"
        }
        return n
    }, o = {
        events: function (t) {
            var e;
            e = $("title"), t.data.events > 0 ? (t.data.events > 99 && (t.data.events = "99+"), $("#messageCount").siblings(".has-unread__count").remove(), $("#messageCount").after('<span class="has-unread__count">' + t.data.events + "</span>"), $("#m-messageCount").text(t.data.events), $(".mobile-menu__unreadpoint").show(), e.text(/^\(\d+[\+]?\)/.test(e.text()) ? e.text().replace(/^\(\d+[\+]?\)/, "(" + t.data.events + ")") : "(" + t.data.events + ")" + $("title").text())) : 0 === t.data.events && ($(".has-unread__count").remove(), e.text(e.text().replace(/^\(\d+[\+]?\)/, "")))
        }, drafts: function (t) {
            0 !== t.data.drafts && ($("#draftCount .badge").remove(), $("#draftCount").append('<span class="badge">' + t.data.drafts + "</span>"))
        }, invites: function (t) {
            0 !== t.data.invites && ($("#inviteCount .badge").remove(), $("#inviteCount").append('<span class="badge">' + t.data.invites + "</span>"))
        }
    }, i = function (t, e) {
        return $.ajax({
            dataType: "json", url: "/api/user/stat", data: {types: t}, success: function (t) {
                0 === t.status && t.data && e && e(t)
            }
        })
    }, c = function () {
        i(["events", "drafts", "invites"], function (t) {
            return o.events(t), o.drafts(t), o.invites(t)
        })
    }, p = function () {
        i(["events"], function (t) {
            return o.events(t)
        })
    }, u = function () {
        t({
            modalSize: "modal-lg",
            title: "登录",
            doneText: "登陆",
            hideClose: !0,
            hideDone: !0,
            hideFooter: !0,
            content: $("#loginModal").text(),
            show: function () {
                return $("[name=mail]").first().focus(), $("#loginShowMore").click(function (t) {
                    t.preventDefault(), $(this).hide(), $(this).siblings().removeClass("hidden")
                }), $(".sfmodal .widget-login a").click(function (t) {
                    t.preventDefault(), window.open($(this).attr("href"), "_blank", "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=500")
                }), $("#loginReloadCaptcha").click(function () {
                    $(this).find("img").attr("src", "/user/captcha?w=240&h=50")
                })
            }
        }), $(".register-mail").typeHelperOld({emailMode: !0, onlyResult: !1})
    }, $(".addWeek").each(function () {
        $(this).append(" " + a($(this).html()))
    }), $('[data-toggle="tooltip"]').tooltip({container: "body"}), $("img.lazy").lazyload({effect: "fadeIn"}), window.oauthLogin = function () {
        -1 !== location.hostname.indexOf("segmentfault") ? location.reload() : location.href = "/"
    }, window.oauthRegister = function () {
        location.href = "/user/bind"
    }, n = {
        _: window.SF.token,
        staticUrl: window.SF.staticUrl,
        userId: $("#SFUserId").attr("value"),
        userRank: $("#SFUserRank").attr("value"),
        renderUserStatEvents: p,
        login: e.login || u
    }, $(document).ajaxError(function (t, e, n) {
        413 === e.status ? ($("#uploading") && $("#uploading").text(""), alert("文件大小或尺寸超出限制，请修改后重新上传！")) : console.log("Ajax " + e.status + ": ", n.url)
    }), $(document).ajaxSend(function (t, e, r) {
        r.url = -1 === r.url.indexOf("?") ? r.url + "?_=" + n._ : r.url + "&_=" + n._
    }), $(document).ajaxComplete(function (e, r, i) {
        var a, o, s, l, u;
        r.responseText && (-1 !== r.responseText.indexOf("<br />") || -1 !== r.responseText.indexOf("<pre>exception ") ? console.log("警告：前方高能！", r.responseText) : r.responseJSON && 0 === r.responseJSON.status ? (-1 === i.url.indexOf("do=autoComplete") || -1 === i.url.indexOf("draft")) && ($(".error, .has-error").removeClass("error has-error"), $(".error--msg").remove()) : r.responseJSON && 1 === r.responseJSON.status && (u = r.responseJSON, -1 === i.url.indexOf("/user/stat") && "login" === u.data[0] ? n.login() : "robot" === u.data[0] ? location.href = "/stop-robot" : "unactivated" === u.data[0] ? $("#activate").modal("show") : "author" === u.data[0] ? t({
            title: "限制作者本人",
            content: "你是作者，无法对自己进行此操作",
            hideDone: !0
        }) : "follow" === u.data[0] ? t({
            title: "限制本人",
            content: "无法对自己进行此操作",
            hideDone: !0
        }) : "rank" === u.data[0] ? t({
            title: "声望值不够",
            content: "此操作要求你的声望值至少达到 " + u.data[1] + '，<a href="http://segmentfault.com/repu">如何获得声望？</a>',
            hideDone: !0
        }) : "like" === u.data[0] ? t({
            title: "无法进行此操作",
            content: "你已经赞过该条目。",
            hideDone: !0
        }) : "blocked" === u.data[0] ? t({
            title: "账号问题",
            content: '你的帐号因未知原因已被系统自动锁定，如需帮助，请发送邮件至 <a target="_blank" href="mailto:pr@segmentfault.com">pr@segmentfault.com</a> 联系管理员解决。',
            hideDone: !0
        }) : "form" === u.data[0] && (l = i.url.split("/")[2], l = l.split("?")[0], o = new RegExp("[?&]_=" + n._), s = i.url.replace(o, ""), a = !0, $.each(u.data[1], function (t, e) {
            var n, r, i, o;
            return "captcha" === t && ($("[name=captcha]").parents(".form-group").show(), $(".captcha").parent("a").click()), i = t.toLowerCase().replace(/\b[a-z]/g, function (t) {
                return t.toUpperCase()
            }), o = "#" + l + i, r = $("form#" + l + " *[name=" + t + "]").not("[type=hidden]").parents(".form-group"), 0 === r.length && (r = $("form#" + l.replace(/s$/, "") + " *[name=" + t + "]").not("[type=hidden]").parents(".form-group")), n = $('form[action="' + s + '"] *[name=' + t + "]").not("[type=hidden]").parents(".form-group"), n.length || (n = $("form *[name=" + t + "]").not("[type=hidden]").parents(".form-group")), n.length ? (n.find(".help-block").remove(), n.addClass("has-error"), n.find(".input-group").length > 0 ? n.find(".input-group").after('<span class="help-block err">' + e + "</span>") : n.find(".bootstrap-tagsinput").length ? n.find(".bootstrap-tagsinput").addClass("error").after('<span class="help-block err">' + e + "</span>") : n.find("[name=" + t + "]").not("[type=hidden]").after('<span class="help-block err">' + e + "</span>")) : r.length ? (r.find(".help-block.err").remove(), r.addClass("has-error"), r.find("[name=" + t + "]").not("[type=hidden]").after('<span class="help-block err">' + e + "</span>")) : ($("form#" + l + " *[name=" + t + "]").not("[type=hidden]").siblings(".error--msg").remove(), $("form#" + l + " *[name=" + t + "]").not("[type=hidden]").addClass("error").attr("data-error", e).after('<span class="error--msg">' + e + "</span>")), $(o).length ? $(o).addClass("error").attr("data-error", e) : (o = "#" + l.replace(/s$/, "") + i, $(o).addClass("error").attr("data-error", e).after('<span class="error--msg">' + e + "</span>")), a ? (n.length && n.find("[name=" + t + "]").not("[type=hidden]").focus(), r.length && r.find("[name=" + t + "]").not("[type=hidden]").focus(), $(o).length && $(o).focus(), a = !1) : void 0
        }))))
    }), $("body").delegate("form", "submit", function (t) {
        var e;
        e = $(this), e.attr("method") && e.attr("action") && (t.preventDefault(), e.find("button[type=submit]").attr("disabled", "disabled"), $.ajax({
            url: e.attr("action"),
            type: e.attr("method"),
            data: e.serialize(),
            success: function (t) {
                e.find("button[type=submit]").removeAttr("disabled"), 0 === t.status && ("/api/user?do=login" === e.attr("action") && "/user/login" !== location.pathname ? window.location.reload() : /^\//.test(t.data) ? window.location = t.data : window.location.reload())
            }
        }))
    }), $("body").delegate("form input", "keydown", function () {
        $(this).removeClass("error"), $(this).parents(".form-group").removeClass("has-error"), $(this).next(".help-block.err").remove(), $(this).next(".error--msg").remove()
    }), r = null, n.userId && (c(), r = setInterval(p, 6e4)), s = void 0, d = void 0, "undefined" != typeof document.hidden ? (s = "hidden", d = "visibilitychange") : "undefined" != typeof document.mozHidden ? (s = "mozHidden", d = "mozvisibilitychange") : "undefined" != typeof document.msHidden ? (s = "msHidden", d = "msvisibilitychange") : "undefined" != typeof document.webkitHidden && (s = "webkitHidden", d = "webkitvisibilitychange"), $(document).on(d, function () {
        document[s] ? clearInterval(r) : n.userId ? (c(), r = setInterval(p, 6e4)) : $.getJSON("/api/user/stat", function (t) {
            0 === t.status && ($("body").addClass("have-notify"), $("body").prepend('<div class="alert alert-warning topframe" role="alert">您已登录，请 <button class="btn btn-warning btn-xs" type="button" onclick="location.reload()">重新加载</button></div>'))
        })
    }), $("#searchBox").focus(function () {
        var t;
        t = $(".nav .menu").width() + 180 + "px", $(".nav .menu").hide(), $(this).animate({width: t}, 200)
    }), $("#searchBox").blur(function () {
        $(this).animate({width: "180px"}, 200, "swing", function () {
            $(".nav .menu").show()
        })
    }), $("#backtop").click(function () {
        return $("body,html").animate({scrollTop: 0}), !1
    }), $(document).scroll(function () {
        $(this).scrollTop() > 720 ? $("#backtop").removeClass("hidden") : $("#backtop").addClass("hidden")
    }), $(".topframe").length && $(".topframe .close").click(function () {
        $(this).parent().remove(), 0 !== $(".topframe").length && $(".topframe .content").text() || $("body").removeClass("have-notify")
    }), window.SFHacker = {
        setOldVersion: function () {
            $.cookie("v", "old"), window.location.reload()
        }, unSetOldVersion: function () {
            $.removeCookie("v", {path: "/"}), window.location.reload()
        }, makePureTextarea: function () {
            $.cookie("typemode", "native"), window.location.reload()
        }, unMakePureTextarea: function () {
            $.removeCookie("typemode", {path: "/"}), window.location.reload()
        }
    }, $(".SFLogin").click(function (t) {
        t.preventDefault(), n.login()
    }), $(".3rdLogin").click(function (t) {
        t.preventDefault(), window.open($(this).attr("href"), "_blank", "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=500")
    }), $(".hoverDropdown").hoverIntent(function () {
        $(this).hasClass("open") || $(this).find(".dropdownBtn").dropdown("toggle")
    }, function () {
        $(this).hasClass("open") && $(this).find(".dropdownBtn").dropdown("toggle")
    }, null, 1), $(".dropdownBtn").click(function (t) {
        var e;
        $(this).parent(".hoverDropdown").hasClass("open") && (t.preventDefault(), e = $(this).attr("href"), t.ctrlKey || t.metaKey ? window.open(e) : location.href = e)
    }), h = function (t, e, n, r) {
        var i;
        return i = t, i.length > 0 && $(".write-btns a").each(function () {
            $(this).click(function () {
                return i.modal("show"), !1
            })
        }), $(".activate-change", i).click(function () {
            $(".activate-showmail").hide(), $(".activate-form").show()
        }), $(".activate-cancel", i).click(function () {
            $(".activate-showmail").show(), $(".activate-form").hide()
        }), $(".activate-form").on("submit", function (t) {
            var e;
            return t.preventDefault(), e = $(this), $.post(n, {mail: e.find(".mail").val()}, function (t) {
                0 === t.status && (e.parent().find(".session-mail").text(e.find(".mail").val()), $(".activate-showmail").show(), $(".activate-form").hide(), location.reload())
            }), !1
        }), $(".activate-resend", i).click(function () {
            var t, n, i, a;
            n = $(this), t = n.siblings("span").find("span"), i = void 0, a = void 0, i = 120, i--, $.post(e, function (e) {
                0 === e.status ? ($(".company-activete-tips").length && $(".company-activete-tips").html('<div class="alert alert-success">' + e.message + "</div>"), r && r(), a = setInterval(function () {
                    return 0 === i ? (clearInterval(a), n.show(), void n.siblings("span").hide()) : void t.text(i--)
                }, 1e3)) : ($(".company-activete-tips").length && $(".company-activete-tips").html('<div class="alert alert-danger">' + e.data[1] + "</div>"), setTimeout(function () {
                    $(".company-activete-tips").length && $(".company-activete-tips").html('<div class="alert alert-success">激活邮件已发送 （<span>120</span>）</div>'), n.show()
                }, 9e5))
            })
        })
    }, h($("#activate"), "/api/user/reactivate", "/api/settings/mail/edit"), h($("#companyActivate"), "/api/company/reactivate", "/api/settings/mail/edit"), $.cookie("readTour") || n.userId || $("#messageCount").after('<span class="has-unread__count">1</span>'), n
}), !function (t, e) {
    "object" == typeof exports && "object" == typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define("handlebars", e) : "object" == typeof exports ? exports.Handlebars = e() : t.Handlebars = e()
}(this, function () {
    return function (t) {
        function e(r) {
            if (n[r])return n[r].exports;
            var i = n[r] = {exports: {}, id: r, loaded: !1};
            return t[r].call(i.exports, i, i.exports, e), i.loaded = !0, i.exports
        }

        var n = {};
        return e.m = t, e.c = n, e.p = "", e(0)
    }([function (t, e, n) {
        "use strict";
        function r() {
            var t = v();
            return t.compile = function (e, n) {
                return c.compile(e, n, t)
            }, t.precompile = function (e, n) {
                return c.precompile(e, n, t)
            }, t.AST = l["default"], t.Compiler = c.Compiler, t.JavaScriptCompiler = h["default"], t.Parser = u.parser, t.parse = u.parse, t
        }

        var i = n(8)["default"];
        e.__esModule = !0;
        var a = n(1), o = i(a), s = n(2), l = i(s), u = n(3), c = n(4), p = n(5), h = i(p), f = n(6), d = i(f), m = n(7), g = i(m), v = o["default"].create, y = r();
        y.create = r, g["default"](y), y.Visitor = d["default"], y["default"] = y, e["default"] = y, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        function r() {
            var t = new s.HandlebarsEnvironment;
            return f.extend(t, s), t.SafeString = u["default"], t.Exception = p["default"], t.Utils = f, t.escapeExpression = f.escapeExpression, t.VM = m, t.template = function (e) {
                return m.template(e, t)
            }, t
        }

        var i = n(9)["default"], a = n(8)["default"];
        e.__esModule = !0;
        var o = n(10), s = i(o), l = n(11), u = a(l), c = n(12), p = a(c), h = n(13), f = i(h), d = n(14), m = i(d), g = n(7), v = a(g), y = r();
        y.create = r, v["default"](y), y["default"] = y, e["default"] = y, t.exports = e["default"]
    }, function (t, e) {
        "use strict";
        e.__esModule = !0;
        var n = {
            helpers: {
                helperExpression: function (t) {
                    return "SubExpression" === t.type || ("MustacheStatement" === t.type || "BlockStatement" === t.type) && !!(t.params && t.params.length || t.hash)
                }, scopedId: function (t) {
                    return /^\.|this\b/.test(t.original)
                }, simpleId: function (t) {
                    return 1 === t.parts.length && !n.helpers.scopedId(t) && !t.depth
                }
            }
        };
        e["default"] = n, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        function r(t, e) {
            if ("Program" === t.type)return t;
            s["default"].yy = f, f.locInfo = function (t) {
                return new f.SourceLocation(e && e.srcName, t)
            };
            var n = new u["default"](e);
            return n.accept(s["default"].parse(t))
        }

        var i = n(8)["default"], a = n(9)["default"];
        e.__esModule = !0, e.parse = r;
        var o = n(15), s = i(o), l = n(16), u = i(l), c = n(17), p = a(c), h = n(13);
        e.parser = s["default"];
        var f = {};
        h.extend(f, p)
    }, function (t, e, n) {
        "use strict";
        function r() {
        }

        function i(t, e, n) {
            if (null == t || "string" != typeof t && "Program" !== t.type)throw new c["default"]("You must pass a string or Handlebars AST to Handlebars.precompile. You passed " + t);
            e = e || {}, "data"in e || (e.data = !0), e.compat && (e.useDepths = !0);
            var r = n.parse(t, e), i = (new n.Compiler).compile(r, e);
            return (new n.JavaScriptCompiler).compile(i, e)
        }

        function a(t, e, n) {
            function r() {
                var r = n.parse(t, e), i = (new n.Compiler).compile(r, e), a = (new n.JavaScriptCompiler).compile(i, e, void 0, !0);
                return n.template(a)
            }

            function i(t, e) {
                return a || (a = r()), a.call(this, t, e)
            }

            if (void 0 === e && (e = {}), null == t || "string" != typeof t && "Program" !== t.type)throw new c["default"]("You must pass a string or Handlebars AST to Handlebars.compile. You passed " + t);
            "data"in e || (e.data = !0), e.compat && (e.useDepths = !0);
            var a = void 0;
            return i._setup = function (t) {
                return a || (a = r()), a._setup(t)
            }, i._child = function (t, e, n, i) {
                return a || (a = r()), a._child(t, e, n, i)
            }, i
        }

        function o(t, e) {
            if (t === e)return !0;
            if (p.isArray(t) && p.isArray(e) && t.length === e.length) {
                for (var n = 0; n < t.length; n++)if (!o(t[n], e[n]))return !1;
                return !0
            }
        }

        function s(t) {
            if (!t.path.parts) {
                var e = t.path;
                t.path = {
                    type: "PathExpression",
                    data: !1,
                    depth: 0,
                    parts: [e.original + ""],
                    original: e.original + "",
                    loc: e.loc
                }
            }
        }

        var l = n(8)["default"];
        e.__esModule = !0, e.Compiler = r, e.precompile = i, e.compile = a;
        var u = n(12), c = l(u), p = n(13), h = n(2), f = l(h), d = [].slice;
        r.prototype = {
            compiler: r, equals: function (t) {
                var e = this.opcodes.length;
                if (t.opcodes.length !== e)return !1;
                for (var n = 0; e > n; n++) {
                    var r = this.opcodes[n], i = t.opcodes[n];
                    if (r.opcode !== i.opcode || !o(r.args, i.args))return !1
                }
                e = this.children.length;
                for (var n = 0; e > n; n++)if (!this.children[n].equals(t.children[n]))return !1;
                return !0
            }, guid: 0, compile: function (t, e) {
                this.sourceNode = [], this.opcodes = [], this.children = [], this.options = e, this.stringParams = e.stringParams, this.trackIds = e.trackIds, e.blockParams = e.blockParams || [];
                var n = e.knownHelpers;
                if (e.knownHelpers = {
                        helperMissing: !0,
                        blockHelperMissing: !0,
                        each: !0,
                        "if": !0,
                        unless: !0,
                        "with": !0,
                        log: !0,
                        lookup: !0
                    }, n)for (var r in n)r in n && (e.knownHelpers[r] = n[r]);
                return this.accept(t)
            }, compileProgram: function (t) {
                var e = new this.compiler, n = e.compile(t, this.options), r = this.guid++;
                return this.usePartial = this.usePartial || n.usePartial, this.children[r] = n, this.useDepths = this.useDepths || n.useDepths, r
            }, accept: function (t) {
                if (!this[t.type])throw new c["default"]("Unknown type: " + t.type, t);
                this.sourceNode.unshift(t);
                var e = this[t.type](t);
                return this.sourceNode.shift(), e
            }, Program: function (t) {
                this.options.blockParams.unshift(t.blockParams);
                for (var e = t.body, n = e.length, r = 0; n > r; r++)this.accept(e[r]);
                return this.options.blockParams.shift(), this.isSimple = 1 === n, this.blockParams = t.blockParams ? t.blockParams.length : 0, this
            }, BlockStatement: function (t) {
                s(t);
                var e = t.program, n = t.inverse;
                e = e && this.compileProgram(e), n = n && this.compileProgram(n);
                var r = this.classifySexpr(t);
                "helper" === r ? this.helperSexpr(t, e, n) : "simple" === r ? (this.simpleSexpr(t), this.opcode("pushProgram", e), this.opcode("pushProgram", n), this.opcode("emptyHash"), this.opcode("blockValue", t.path.original)) : (this.ambiguousSexpr(t, e, n), this.opcode("pushProgram", e), this.opcode("pushProgram", n), this.opcode("emptyHash"), this.opcode("ambiguousBlockValue")), this.opcode("append")
            }, DecoratorBlock: function (t) {
                var e = t.program && this.compileProgram(t.program), n = this.setupFullMustacheParams(t, e, void 0), r = t.path;
                this.useDecorators = !0, this.opcode("registerDecorator", n.length, r.original)
            }, PartialStatement: function (t) {
                this.usePartial = !0;
                var e = t.program;
                e && (e = this.compileProgram(t.program));
                var n = t.params;
                if (n.length > 1)throw new c["default"]("Unsupported number of partial arguments: " + n.length, t);
                n.length || (this.options.explicitPartialContext ? this.opcode("pushLiteral", "undefined") : n.push({
                    type: "PathExpression",
                    parts: [],
                    depth: 0
                }));
                var r = t.name.original, i = "SubExpression" === t.name.type;
                i && this.accept(t.name), this.setupFullMustacheParams(t, e, void 0, !0);
                var a = t.indent || "";
                this.options.preventIndent && a && (this.opcode("appendContent", a), a = ""), this.opcode("invokePartial", i, r, a), this.opcode("append")
            }, PartialBlockStatement: function (t) {
                this.PartialStatement(t)
            }, MustacheStatement: function (t) {
                this.SubExpression(t), this.opcode(t.escaped && !this.options.noEscape ? "appendEscaped" : "append")
            }, Decorator: function (t) {
                this.DecoratorBlock(t)
            }, ContentStatement: function (t) {
                t.value && this.opcode("appendContent", t.value)
            }, CommentStatement: function () {
            }, SubExpression: function (t) {
                s(t);
                var e = this.classifySexpr(t);
                "simple" === e ? this.simpleSexpr(t) : "helper" === e ? this.helperSexpr(t) : this.ambiguousSexpr(t)
            }, ambiguousSexpr: function (t, e, n) {
                var r = t.path, i = r.parts[0], a = null != e || null != n;
                this.opcode("getContext", r.depth), this.opcode("pushProgram", e), this.opcode("pushProgram", n), r.strict = !0, this.accept(r), this.opcode("invokeAmbiguous", i, a)
            }, simpleSexpr: function (t) {
                var e = t.path;
                e.strict = !0, this.accept(e), this.opcode("resolvePossibleLambda")
            }, helperSexpr: function (t, e, n) {
                var r = this.setupFullMustacheParams(t, e, n), i = t.path, a = i.parts[0];
                if (this.options.knownHelpers[a])this.opcode("invokeKnownHelper", r.length, a); else {
                    if (this.options.knownHelpersOnly)throw new c["default"]("You specified knownHelpersOnly, but used the unknown helper " + a, t);
                    i.strict = !0, i.falsy = !0, this.accept(i), this.opcode("invokeHelper", r.length, i.original, f["default"].helpers.simpleId(i))
                }
            }, PathExpression: function (t) {
                this.addDepth(t.depth), this.opcode("getContext", t.depth);
                var e = t.parts[0], n = f["default"].helpers.scopedId(t), r = !t.depth && !n && this.blockParamIndex(e);
                r ? this.opcode("lookupBlockParam", r, t.parts) : e ? t.data ? (this.options.data = !0, this.opcode("lookupData", t.depth, t.parts, t.strict)) : this.opcode("lookupOnContext", t.parts, t.falsy, t.strict, n) : this.opcode("pushContext")
            }, StringLiteral: function (t) {
                this.opcode("pushString", t.value)
            }, NumberLiteral: function (t) {
                this.opcode("pushLiteral", t.value)
            }, BooleanLiteral: function (t) {
                this.opcode("pushLiteral", t.value)
            }, UndefinedLiteral: function () {
                this.opcode("pushLiteral", "undefined")
            }, NullLiteral: function () {
                this.opcode("pushLiteral", "null")
            }, Hash: function (t) {
                var e = t.pairs, n = 0, r = e.length;
                for (this.opcode("pushHash"); r > n; n++)this.pushParam(e[n].value);
                for (; n--;)this.opcode("assignToHash", e[n].key);
                this.opcode("popHash")
            }, opcode: function (t) {
                this.opcodes.push({opcode: t, args: d.call(arguments, 1), loc: this.sourceNode[0].loc})
            }, addDepth: function (t) {
                t && (this.useDepths = !0)
            }, classifySexpr: function (t) {
                var e = f["default"].helpers.simpleId(t.path), n = e && !!this.blockParamIndex(t.path.parts[0]), r = !n && f["default"].helpers.helperExpression(t), i = !n && (r || e);
                if (i && !r) {
                    var a = t.path.parts[0], o = this.options;
                    o.knownHelpers[a] ? r = !0 : o.knownHelpersOnly && (i = !1)
                }
                return r ? "helper" : i ? "ambiguous" : "simple"
            }, pushParams: function (t) {
                for (var e = 0, n = t.length; n > e; e++)this.pushParam(t[e])
            }, pushParam: function (t) {
                var e = null != t.value ? t.value : t.original || "";
                if (this.stringParams)e.replace && (e = e.replace(/^(\.?\.\/)*/g, "").replace(/\//g, ".")), t.depth && this.addDepth(t.depth), this.opcode("getContext", t.depth || 0), this.opcode("pushStringParam", e, t.type), "SubExpression" === t.type && this.accept(t); else {
                    if (this.trackIds) {
                        var n = void 0;
                        if (!t.parts || f["default"].helpers.scopedId(t) || t.depth || (n = this.blockParamIndex(t.parts[0])), n) {
                            var r = t.parts.slice(1).join(".");
                            this.opcode("pushId", "BlockParam", n, r)
                        } else e = t.original || e, e.replace && (e = e.replace(/^this(?:\.|$)/, "").replace(/^\.\//, "").replace(/^\.$/, "")), this.opcode("pushId", t.type, e)
                    }
                    this.accept(t)
                }
            }, setupFullMustacheParams: function (t, e, n, r) {
                var i = t.params;
                return this.pushParams(i), this.opcode("pushProgram", e), this.opcode("pushProgram", n), t.hash ? this.accept(t.hash) : this.opcode("emptyHash", r), i
            }, blockParamIndex: function (t) {
                for (var e = 0, n = this.options.blockParams.length; n > e; e++) {
                    var r = this.options.blockParams[e], i = r && p.indexOf(r, t);
                    if (r && i >= 0)return [e, i]
                }
            }
        }
    }, function (t, e, n) {
        "use strict";
        function r(t) {
            this.value = t
        }

        function i() {
        }

        function a(t, e, n, r) {
            var i = e.popStack(), a = 0, o = n.length;
            for (t && o--; o > a; a++)i = e.nameLookup(i, n[a], r);
            return t ? [e.aliasable("container.strict"), "(", i, ", ", e.quotedString(n[a]), ")"] : i
        }

        var o = n(8)["default"];
        e.__esModule = !0;
        var s = n(10), l = n(12), u = o(l), c = n(13), p = n(18), h = o(p);
        i.prototype = {
            nameLookup: function (t, e) {
                return i.isValidJavaScriptVariableName(e) ? [t, ".", e] : [t, "[", JSON.stringify(e), "]"]
            }, depthedLookup: function (t) {
                return [this.aliasable("container.lookup"), '(depths, "', t, '")']
            }, compilerInfo: function () {
                var t = s.COMPILER_REVISION, e = s.REVISION_CHANGES[t];
                return [t, e]
            }, appendToBuffer: function (t, e, n) {
                return c.isArray(t) || (t = [t]), t = this.source.wrap(t, e), this.environment.isSimple ? ["return ", t, ";"] : n ? ["buffer += ", t, ";"] : (t.appendToBuffer = !0, t)
            }, initializeBuffer: function () {
                return this.quotedString("")
            }, compile: function (t, e, n, r) {
                this.environment = t, this.options = e, this.stringParams = this.options.stringParams, this.trackIds = this.options.trackIds, this.precompile = !r, this.name = this.environment.name, this.isChild = !!n, this.context = n || {
                        decorators: [],
                        programs: [],
                        environments: []
                    }, this.preamble(), this.stackSlot = 0, this.stackVars = [], this.aliases = {}, this.registers = {list: []}, this.hashes = [], this.compileStack = [], this.inlineStack = [], this.blockParams = [], this.compileChildren(t, e), this.useDepths = this.useDepths || t.useDepths || t.useDecorators || this.options.compat, this.useBlockParams = this.useBlockParams || t.useBlockParams;
                var i = t.opcodes, a = void 0, o = void 0, s = void 0, l = void 0;
                for (s = 0, l = i.length; l > s; s++)a = i[s], this.source.currentLocation = a.loc, o = o || a.loc, this[a.opcode].apply(this, a.args);
                if (this.source.currentLocation = o, this.pushSource(""), this.stackSlot || this.inlineStack.length || this.compileStack.length)throw new u["default"]("Compile completed with content left on stack");
                this.decorators.isEmpty() ? this.decorators = void 0 : (this.useDecorators = !0, this.decorators.prepend("var decorators = container.decorators;\n"), this.decorators.push("return fn;"), r ? this.decorators = Function.apply(this, ["fn", "props", "container", "depth0", "data", "blockParams", "depths", this.decorators.merge()]) : (this.decorators.prepend("function(fn, props, container, depth0, data, blockParams, depths) {\n"), this.decorators.push("}\n"), this.decorators = this.decorators.merge()));
                var c = this.createFunctionContext(r);
                if (this.isChild)return c;
                var p = {compiler: this.compilerInfo(), main: c};
                this.decorators && (p.main_d = this.decorators, p.useDecorators = !0);
                var h = this.context, f = h.programs, d = h.decorators;
                for (s = 0, l = f.length; l > s; s++)f[s] && (p[s] = f[s], d[s] && (p[s + "_d"] = d[s], p.useDecorators = !0));
                return this.environment.usePartial && (p.usePartial = !0), this.options.data && (p.useData = !0), this.useDepths && (p.useDepths = !0), this.useBlockParams && (p.useBlockParams = !0), this.options.compat && (p.compat = !0), r ? p.compilerOptions = this.options : (p.compiler = JSON.stringify(p.compiler), this.source.currentLocation = {
                    start: {
                        line: 1,
                        column: 0
                    }
                }, p = this.objectLiteral(p), e.srcName ? (p = p.toStringWithSourceMap({file: e.destName}), p.map = p.map && p.map.toString()) : p = p.toString()), p
            }, preamble: function () {
                this.lastContext = 0, this.source = new h["default"](this.options.srcName), this.decorators = new h["default"](this.options.srcName)
            }, createFunctionContext: function (t) {
                var e = "", n = this.stackVars.concat(this.registers.list);
                n.length > 0 && (e += ", " + n.join(", "));
                var r = 0;
                for (var i in this.aliases) {
                    var a = this.aliases[i];
                    this.aliases.hasOwnProperty(i) && a.children && a.referenceCount > 1 && (e += ", alias" + ++r + "=" + i, a.children[0] = "alias" + r)
                }
                var o = ["container", "depth0", "helpers", "partials", "data"];
                (this.useBlockParams || this.useDepths) && o.push("blockParams"), this.useDepths && o.push("depths");
                var s = this.mergeSource(e);
                return t ? (o.push(s), Function.apply(this, o)) : this.source.wrap(["function(", o.join(","), ") {\n  ", s, "}"])
            }, mergeSource: function (t) {
                var e = this.environment.isSimple, n = !this.forceBuffer, r = void 0, i = void 0, a = void 0, o = void 0;
                return this.source.each(function (t) {
                    t.appendToBuffer ? (a ? t.prepend("  + ") : a = t, o = t) : (a && (i ? a.prepend("buffer += ") : r = !0, o.add(";"), a = o = void 0), i = !0, e || (n = !1))
                }), n ? a ? (a.prepend("return "), o.add(";")) : i || this.source.push('return "";') : (t += ", buffer = " + (r ? "" : this.initializeBuffer()), a ? (a.prepend("return buffer + "), o.add(";")) : this.source.push("return buffer;")), t && this.source.prepend("var " + t.substring(2) + (r ? "" : ";\n")), this.source.merge()
            }, blockValue: function (t) {
                var e = this.aliasable("helpers.blockHelperMissing"), n = [this.contextName(0)];
                this.setupHelperArgs(t, 0, n);
                var r = this.popStack();
                n.splice(1, 0, r), this.push(this.source.functionCall(e, "call", n))
            }, ambiguousBlockValue: function () {
                var t = this.aliasable("helpers.blockHelperMissing"), e = [this.contextName(0)];
                this.setupHelperArgs("", 0, e, !0), this.flushInline();
                var n = this.topStack();
                e.splice(1, 0, n), this.pushSource(["if (!", this.lastHelper, ") { ", n, " = ", this.source.functionCall(t, "call", e), "}"])
            }, appendContent: function (t) {
                this.pendingContent ? t = this.pendingContent + t : this.pendingLocation = this.source.currentLocation, this.pendingContent = t
            }, append: function () {
                if (this.isInline())this.replaceStack(function (t) {
                    return [" != null ? ", t, ' : ""']
                }), this.pushSource(this.appendToBuffer(this.popStack())); else {
                    var t = this.popStack();
                    this.pushSource(["if (", t, " != null) { ", this.appendToBuffer(t, void 0, !0), " }"]), this.environment.isSimple && this.pushSource(["else { ", this.appendToBuffer("''", void 0, !0), " }"])
                }
            }, appendEscaped: function () {
                this.pushSource(this.appendToBuffer([this.aliasable("container.escapeExpression"), "(", this.popStack(), ")"]))
            }, getContext: function (t) {
                this.lastContext = t
            }, pushContext: function () {
                this.pushStackLiteral(this.contextName(this.lastContext))
            }, lookupOnContext: function (t, e, n, r) {
                var i = 0;
                r || !this.options.compat || this.lastContext ? this.pushContext() : this.push(this.depthedLookup(t[i++])), this.resolvePath("context", t, i, e, n)
            }, lookupBlockParam: function (t, e) {
                this.useBlockParams = !0, this.push(["blockParams[", t[0], "][", t[1], "]"]), this.resolvePath("context", e, 1)
            }, lookupData: function (t, e, n) {
                this.pushStackLiteral(t ? "container.data(data, " + t + ")" : "data"), this.resolvePath("data", e, 0, !0, n)
            }, resolvePath: function (t, e, n, r, i) {
                var o = this;
                if (this.options.strict || this.options.assumeObjects)return void this.push(a(this.options.strict && i, this, e, t));
                for (var s = e.length; s > n; n++)this.replaceStack(function (i) {
                    var a = o.nameLookup(i, e[n], t);
                    return r ? [" && ", a] : [" != null ? ", a, " : ", i]
                })
            }, resolvePossibleLambda: function () {
                this.push([this.aliasable("container.lambda"), "(", this.popStack(), ", ", this.contextName(0), ")"])
            }, pushStringParam: function (t, e) {
                this.pushContext(), this.pushString(e), "SubExpression" !== e && ("string" == typeof t ? this.pushString(t) : this.pushStackLiteral(t))
            }, emptyHash: function (t) {
                this.trackIds && this.push("{}"), this.stringParams && (this.push("{}"), this.push("{}")), this.pushStackLiteral(t ? "undefined" : "{}")
            }, pushHash: function () {
                this.hash && this.hashes.push(this.hash), this.hash = {values: [], types: [], contexts: [], ids: []}
            }, popHash: function () {
                var t = this.hash;
                this.hash = this.hashes.pop(), this.trackIds && this.push(this.objectLiteral(t.ids)), this.stringParams && (this.push(this.objectLiteral(t.contexts)), this.push(this.objectLiteral(t.types))), this.push(this.objectLiteral(t.values))
            }, pushString: function (t) {
                this.pushStackLiteral(this.quotedString(t))
            }, pushLiteral: function (t) {
                this.pushStackLiteral(t)
            }, pushProgram: function (t) {
                this.pushStackLiteral(null != t ? this.programExpression(t) : null)
            }, registerDecorator: function (t, e) {
                var n = this.nameLookup("decorators", e, "decorator"), r = this.setupHelperArgs(e, t);
                this.decorators.push(["fn = ", this.decorators.functionCall(n, "", ["fn", "props", "container", r]), " || fn;"])
            }, invokeHelper: function (t, e, n) {
                var r = this.popStack(), i = this.setupHelper(t, e), a = n ? [i.name, " || "] : "", o = ["("].concat(a, r);
                this.options.strict || o.push(" || ", this.aliasable("helpers.helperMissing")), o.push(")"), this.push(this.source.functionCall(o, "call", i.callParams))
            }, invokeKnownHelper: function (t, e) {
                var n = this.setupHelper(t, e);
                this.push(this.source.functionCall(n.name, "call", n.callParams))
            }, invokeAmbiguous: function (t, e) {
                this.useRegister("helper");
                var n = this.popStack();
                this.emptyHash();
                var r = this.setupHelper(0, t, e), i = this.lastHelper = this.nameLookup("helpers", t, "helper"), a = ["(", "(helper = ", i, " || ", n, ")"];
                this.options.strict || (a[0] = "(helper = ", a.push(" != null ? helper : ", this.aliasable("helpers.helperMissing"))), this.push(["(", a, r.paramsInit ? ["),(", r.paramsInit] : [], "),", "(typeof helper === ", this.aliasable('"function"'), " ? ", this.source.functionCall("helper", "call", r.callParams), " : helper))"])
            }, invokePartial: function (t, e, n) {
                var r = [], i = this.setupParams(e, 1, r);
                t && (e = this.popStack(), delete i.name), n && (i.indent = JSON.stringify(n)), i.helpers = "helpers", i.partials = "partials", r.unshift(t ? e : this.nameLookup("partials", e, "partial")), this.options.compat && (i.depths = "depths"), i = this.objectLiteral(i), r.push(i), this.push(this.source.functionCall("container.invokePartial", "", r))
            }, assignToHash: function (t) {
                var e = this.popStack(), n = void 0, r = void 0, i = void 0;
                this.trackIds && (i = this.popStack()), this.stringParams && (r = this.popStack(), n = this.popStack());
                var a = this.hash;
                n && (a.contexts[t] = n), r && (a.types[t] = r), i && (a.ids[t] = i), a.values[t] = e
            }, pushId: function (t, e, n) {
                "BlockParam" === t ? this.pushStackLiteral("blockParams[" + e[0] + "].path[" + e[1] + "]" + (n ? " + " + JSON.stringify("." + n) : "")) : "PathExpression" === t ? this.pushString(e) : this.pushStackLiteral("SubExpression" === t ? "true" : "null")
            }, compiler: i, compileChildren: function (t, e) {
                for (var n = t.children, r = void 0, i = void 0, a = 0, o = n.length; o > a; a++) {
                    r = n[a], i = new this.compiler;
                    var s = this.matchExistingProgram(r);
                    null == s ? (this.context.programs.push(""), s = this.context.programs.length, r.index = s, r.name = "program" + s, this.context.programs[s] = i.compile(r, e, this.context, !this.precompile), this.context.decorators[s] = i.decorators, this.context.environments[s] = r, this.useDepths = this.useDepths || i.useDepths, this.useBlockParams = this.useBlockParams || i.useBlockParams) : (r.index = s, r.name = "program" + s, this.useDepths = this.useDepths || r.useDepths, this.useBlockParams = this.useBlockParams || r.useBlockParams)
                }
            }, matchExistingProgram: function (t) {
                for (var e = 0, n = this.context.environments.length; n > e; e++) {
                    var r = this.context.environments[e];
                    if (r && r.equals(t))return e
                }
            }, programExpression: function (t) {
                var e = this.environment.children[t], n = [e.index, "data", e.blockParams];
                return (this.useBlockParams || this.useDepths) && n.push("blockParams"), this.useDepths && n.push("depths"), "container.program(" + n.join(", ") + ")"
            }, useRegister: function (t) {
                this.registers[t] || (this.registers[t] = !0, this.registers.list.push(t))
            }, push: function (t) {
                return t instanceof r || (t = this.source.wrap(t)), this.inlineStack.push(t), t
            }, pushStackLiteral: function (t) {
                this.push(new r(t))
            }, pushSource: function (t) {
                this.pendingContent && (this.source.push(this.appendToBuffer(this.source.quotedString(this.pendingContent), this.pendingLocation)), this.pendingContent = void 0), t && this.source.push(t)
            }, replaceStack: function (t) {
                var e = ["("], n = void 0, i = void 0, a = void 0;
                if (!this.isInline())throw new u["default"]("replaceStack on non-inline");
                var o = this.popStack(!0);
                if (o instanceof r)n = [o.value], e = ["(", n], a = !0; else {
                    i = !0;
                    var s = this.incrStack();
                    e = ["((", this.push(s), " = ", o, ")"], n = this.topStack()
                }
                var l = t.call(this, n);
                a || this.popStack(), i && this.stackSlot--, this.push(e.concat(l, ")"))
            }, incrStack: function () {
                return this.stackSlot++, this.stackSlot > this.stackVars.length && this.stackVars.push("stack" + this.stackSlot), this.topStackName()
            }, topStackName: function () {
                return "stack" + this.stackSlot
            }, flushInline: function () {
                var t = this.inlineStack;
                this.inlineStack = [];
                for (var e = 0, n = t.length; n > e; e++) {
                    var i = t[e];
                    if (i instanceof r)this.compileStack.push(i); else {
                        var a = this.incrStack();
                        this.pushSource([a, " = ", i, ";"]), this.compileStack.push(a)
                    }
                }
            }, isInline: function () {
                return this.inlineStack.length
            }, popStack: function (t) {
                var e = this.isInline(), n = (e ? this.inlineStack : this.compileStack).pop();
                if (!t && n instanceof r)return n.value;
                if (!e) {
                    if (!this.stackSlot)throw new u["default"]("Invalid stack pop");
                    this.stackSlot--
                }
                return n
            }, topStack: function () {
                var t = this.isInline() ? this.inlineStack : this.compileStack, e = t[t.length - 1];
                return e instanceof r ? e.value : e
            }, contextName: function (t) {
                return this.useDepths && t ? "depths[" + t + "]" : "depth" + t
            }, quotedString: function (t) {
                return this.source.quotedString(t)
            }, objectLiteral: function (t) {
                return this.source.objectLiteral(t)
            }, aliasable: function (t) {
                var e = this.aliases[t];
                return e ? (e.referenceCount++, e) : (e = this.aliases[t] = this.source.wrap(t), e.aliasable = !0, e.referenceCount = 1, e)
            }, setupHelper: function (t, e, n) {
                var r = [], i = this.setupHelperArgs(e, t, r, n), a = this.nameLookup("helpers", e, "helper");
                return {params: r, paramsInit: i, name: a, callParams: [this.contextName(0)].concat(r)}
            }, setupParams: function (t, e, n) {
                var r = {}, i = [], a = [], o = [], s = !n, l = void 0;
                s && (n = []), r.name = this.quotedString(t), r.hash = this.popStack(), this.trackIds && (r.hashIds = this.popStack()), this.stringParams && (r.hashTypes = this.popStack(), r.hashContexts = this.popStack());
                var u = this.popStack(), c = this.popStack();
                (c || u) && (r.fn = c || "container.noop", r.inverse = u || "container.noop");
                for (var p = e; p--;)l = this.popStack(), n[p] = l, this.trackIds && (o[p] = this.popStack()), this.stringParams && (a[p] = this.popStack(), i[p] = this.popStack());
                return s && (r.args = this.source.generateArray(n)), this.trackIds && (r.ids = this.source.generateArray(o)), this.stringParams && (r.types = this.source.generateArray(a), r.contexts = this.source.generateArray(i)), this.options.data && (r.data = "data"), this.useBlockParams && (r.blockParams = "blockParams"), r
            }, setupHelperArgs: function (t, e, n, r) {
                var i = this.setupParams(t, e, n);
                return i = this.objectLiteral(i), r ? (this.useRegister("options"), n.push("options"), ["options=", i]) : n ? (n.push(i), "") : i
            }
        }, function () {
            for (var t = "break else new var case finally return void catch for switch while continue function this with default if throw delete in try do instanceof typeof abstract enum int short boolean export interface static byte extends long super char final native synchronized class float package throws const goto private transient debugger implements protected volatile double import public let yield await null true false".split(" "), e = i.RESERVED_WORDS = {}, n = 0, r = t.length; r > n; n++)e[t[n]] = !0
        }(), i.isValidJavaScriptVariableName = function (t) {
            return !i.RESERVED_WORDS[t] && /^[a-zA-Z_$][0-9a-zA-Z_$]*$/.test(t)
        }, e["default"] = i, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        function r() {
            this.parents = []
        }

        function i(t) {
            this.acceptRequired(t, "path"), this.acceptArray(t.params), this.acceptKey(t, "hash")
        }

        function a(t) {
            i.call(this, t), this.acceptKey(t, "program"), this.acceptKey(t, "inverse")
        }

        function o(t) {
            this.acceptRequired(t, "name"), this.acceptArray(t.params), this.acceptKey(t, "hash")
        }

        var s = n(8)["default"];
        e.__esModule = !0;
        var l = n(12), u = s(l);
        r.prototype = {
            constructor: r,
            mutating: !1,
            acceptKey: function (t, e) {
                var n = this.accept(t[e]);
                if (this.mutating) {
                    if (n && !r.prototype[n.type])throw new u["default"]('Unexpected node type "' + n.type + '" found when accepting ' + e + " on " + t.type);
                    t[e] = n
                }
            },
            acceptRequired: function (t, e) {
                if (this.acceptKey(t, e), !t[e])throw new u["default"](t.type + " requires " + e)
            },
            acceptArray: function (t) {
                for (var e = 0, n = t.length; n > e; e++)this.acceptKey(t, e), t[e] || (t.splice(e, 1), e--, n--)
            },
            accept: function (t) {
                if (t) {
                    if (!this[t.type])throw new u["default"]("Unknown type: " + t.type, t);
                    this.current && this.parents.unshift(this.current), this.current = t;
                    var e = this[t.type](t);
                    return this.current = this.parents.shift(), !this.mutating || e ? e : e !== !1 ? t : void 0
                }
            },
            Program: function (t) {
                this.acceptArray(t.body)
            },
            MustacheStatement: i,
            Decorator: i,
            BlockStatement: a,
            DecoratorBlock: a,
            PartialStatement: o,
            PartialBlockStatement: function (t) {
                o.call(this, t), this.acceptKey(t, "program")
            },
            ContentStatement: function () {
            },
            CommentStatement: function () {
            },
            SubExpression: i,
            PathExpression: function () {
            },
            StringLiteral: function () {
            },
            NumberLiteral: function () {
            },
            BooleanLiteral: function () {
            },
            UndefinedLiteral: function () {
            },
            NullLiteral: function () {
            },
            Hash: function (t) {
                this.acceptArray(t.pairs)
            },
            HashPair: function (t) {
                this.acceptRequired(t, "value")
            }
        }, e["default"] = r, t.exports = e["default"]
    }, function (t, e) {
        (function (n) {
            "use strict";
            e.__esModule = !0, e["default"] = function (t) {
                var e = "undefined" != typeof n ? n : window, r = e.Handlebars;
                t.noConflict = function () {
                    e.Handlebars === t && (e.Handlebars = r)
                }
            }, t.exports = e["default"]
        }).call(e, function () {
            return this
        }())
    }, function (t, e) {
        "use strict";
        e["default"] = function (t) {
            return t && t.__esModule ? t : {"default": t}
        }, e.__esModule = !0
    }, function (t, e) {
        "use strict";
        e["default"] = function (t) {
            if (t && t.__esModule)return t;
            var e = {};
            if (null != t)for (var n in t)Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n]);
            return e["default"] = t, e
        }, e.__esModule = !0
    }, function (t, e, n) {
        "use strict";
        function r(t, e, n) {
            this.helpers = t || {}, this.partials = e || {}, this.decorators = n || {}, l.registerDefaultHelpers(this), u.registerDefaultDecorators(this)
        }

        var i = n(8)["default"];
        e.__esModule = !0, e.HandlebarsEnvironment = r;
        var a = n(13), o = n(12), s = i(o), l = n(19), u = n(20), c = n(21), p = i(c), h = "4.0.0";
        e.VERSION = h;
        var f = 7;
        e.COMPILER_REVISION = f;
        var d = {
            1: "<= 1.0.rc.2",
            2: "== 1.0.0-rc.3",
            3: "== 1.0.0-rc.4",
            4: "== 1.x.x",
            5: "== 2.0.0-alpha.x",
            6: ">= 2.0.0-beta.1",
            7: ">= 4.0.0"
        };
        e.REVISION_CHANGES = d;
        var m = "[object Object]";
        r.prototype = {
            constructor: r, logger: p["default"], log: p["default"].log, registerHelper: function (t, e) {
                if (a.toString.call(t) === m) {
                    if (e)throw new s["default"]("Arg not supported with multiple helpers");
                    a.extend(this.helpers, t)
                } else this.helpers[t] = e
            }, unregisterHelper: function (t) {
                delete this.helpers[t]
            }, registerPartial: function (t, e) {
                if (a.toString.call(t) === m)a.extend(this.partials, t); else {
                    if ("undefined" == typeof e)throw new s["default"]("Attempting to register a partial as undefined");
                    this.partials[t] = e
                }
            }, unregisterPartial: function (t) {
                delete this.partials[t]
            }, registerDecorator: function (t, e) {
                if (a.toString.call(t) === m) {
                    if (e)throw new s["default"]("Arg not supported with multiple decorators");
                    a.extend(this.decorators, t)
                } else this.decorators[t] = e
            }, unregisterDecorator: function (t) {
                delete this.decorators[t]
            }
        };
        var g = p["default"].log;
        e.log = g, e.createFrame = a.createFrame, e.logger = p["default"]
    }, function (t, e) {
        "use strict";
        function n(t) {
            this.string = t
        }

        e.__esModule = !0, n.prototype.toString = n.prototype.toHTML = function () {
            return "" + this.string
        }, e["default"] = n, t.exports = e["default"]
    }, function (t, e) {
        "use strict";
        function n(t, e) {
            var i = e && e.loc, a = void 0, o = void 0;
            i && (a = i.start.line, o = i.start.column, t += " - " + a + ":" + o);
            for (var s = Error.prototype.constructor.call(this, t), l = 0; l < r.length; l++)this[r[l]] = s[r[l]];
            Error.captureStackTrace && Error.captureStackTrace(this, n), i && (this.lineNumber = a, this.column = o)
        }

        e.__esModule = !0;
        var r = ["description", "fileName", "lineNumber", "message", "name", "number", "stack"];
        n.prototype = new Error, e["default"] = n, t.exports = e["default"]
    }, function (t, e) {
        "use strict";
        function n(t) {
            return c[t]
        }

        function r(t) {
            for (var e = 1; e < arguments.length; e++)for (var n in arguments[e])Object.prototype.hasOwnProperty.call(arguments[e], n) && (t[n] = arguments[e][n]);
            return t
        }

        function i(t, e) {
            for (var n = 0, r = t.length; r > n; n++)if (t[n] === e)return n;
            return -1
        }

        function a(t) {
            if ("string" != typeof t) {
                if (t && t.toHTML)return t.toHTML();
                if (null == t)return "";
                if (!t)return t + "";
                t = "" + t
            }
            return h.test(t) ? t.replace(p, n) : t
        }

        function o(t) {
            return t || 0 === t ? m(t) && 0 === t.length ? !0 : !1 : !0
        }

        function s(t) {
            var e = r({}, t);
            return e._parent = t, e
        }

        function l(t, e) {
            return t.path = e, t
        }

        function u(t, e) {
            return (t ? t + "." : "") + e
        }

        e.__esModule = !0, e.extend = r, e.indexOf = i, e.escapeExpression = a, e.isEmpty = o, e.createFrame = s, e.blockParams = l, e.appendContextPath = u;
        var c = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#x27;",
            "`": "&#x60;",
            "=": "&#x3D;"
        }, p = /[&<>"'`=]/g, h = /[&<>"'`=]/, f = Object.prototype.toString;
        e.toString = f;
        var d = function (t) {
            return "function" == typeof t
        };
        d(/x/) && (e.isFunction = d = function (t) {
            return "function" == typeof t && "[object Function]" === f.call(t)
        }), e.isFunction = d;
        var m = Array.isArray || function (t) {
                return t && "object" == typeof t ? "[object Array]" === f.call(t) : !1
            };
        e.isArray = m
    }, function (t, e, n) {
        "use strict";
        function r(t) {
            var e = t && t[0] || 1, n = v.COMPILER_REVISION;
            if (e !== n) {
                if (n > e) {
                    var r = v.REVISION_CHANGES[n], i = v.REVISION_CHANGES[e];
                    throw new g["default"]("Template was precompiled with an older version of Handlebars than the current runtime. Please update your precompiler to a newer version (" + r + ") or downgrade your runtime to an older version (" + i + ").")
                }
                throw new g["default"]("Template was precompiled with a newer version of Handlebars than the current runtime. Please update your runtime to a newer version (" + t[1] + ").")
            }
        }

        function i(t, e) {
            function n(n, r, i) {
                i.hash && (r = d.extend({}, r, i.hash), i.ids && (i.ids[0] = !0)), n = e.VM.resolvePartial.call(this, n, r, i);
                var a = e.VM.invokePartial.call(this, n, r, i);
                if (null == a && e.compile && (i.partials[i.name] = e.compile(n, t.compilerOptions, e), a = i.partials[i.name](r, i)), null != a) {
                    if (i.indent) {
                        for (var o = a.split("\n"), s = 0, l = o.length; l > s && (o[s] || s + 1 !== l); s++)o[s] = i.indent + o[s];
                        a = o.join("\n")
                    }
                    return a
                }
                throw new g["default"]("The partial " + i.name + " could not be compiled when running in runtime-only mode")
            }

            function r(e) {
                function n(e) {
                    return "" + t.main(i, e, i.helpers, i.partials, o, l, s)
                }

                var a = arguments.length <= 1 || void 0 === arguments[1] ? {} : arguments[1], o = a.data;
                r._setup(a), !a.partial && t.useData && (o = u(e, o));
                var s = void 0, l = t.useBlockParams ? [] : void 0;
                return t.useDepths && (s = a.depths ? e !== a.depths[0] ? [e].concat(a.depths) : a.depths : [e]), (n = c(t.main, n, i, a.depths || [], o, l))(e, a)
            }

            if (!e)throw new g["default"]("No environment passed to template");
            if (!t || !t.main)throw new g["default"]("Unknown template object: " + typeof t);
            t.main.decorator = t.main_d, e.VM.checkRevision(t.compiler);
            var i = {
                strict: function (t, e) {
                    if (!(e in t))throw new g["default"]('"' + e + '" not defined in ' + t);
                    return t[e]
                }, lookup: function (t, e) {
                    for (var n = t.length, r = 0; n > r; r++)if (t[r] && null != t[r][e])return t[r][e]
                }, lambda: function (t, e) {
                    return "function" == typeof t ? t.call(e) : t
                }, escapeExpression: d.escapeExpression, invokePartial: n, fn: function (e) {
                    var n = t[e];
                    return n.decorator = t[e + "_d"], n
                }, programs: [], program: function (t, e, n, r, i) {
                    var o = this.programs[t], s = this.fn(t);
                    return e || i || r || n ? o = a(this, t, s, e, n, r, i) : o || (o = this.programs[t] = a(this, t, s)), o
                }, data: function (t, e) {
                    for (; t && e--;)t = t._parent;
                    return t
                }, merge: function (t, e) {
                    var n = t || e;
                    return t && e && t !== e && (n = d.extend({}, e, t)), n
                }, noop: e.VM.noop, compilerInfo: t.compiler
            };
            return r.isTop = !0, r._setup = function (n) {
                n.partial ? (i.helpers = n.helpers, i.partials = n.partials, i.decorators = n.decorators) : (i.helpers = i.merge(n.helpers, e.helpers), t.usePartial && (i.partials = i.merge(n.partials, e.partials)), t.useDecorators && (i.decorators = i.merge(n.decorators, e.decorators)))
            }, r._child = function (e, n, r, o) {
                if (t.useBlockParams && !r)throw new g["default"]("must pass block params");
                if (t.useDepths && !o)throw new g["default"]("must pass parent depths");
                return a(i, e, t[e], n, 0, r, o)
            }, r
        }

        function a(t, e, n, r, i, a, o) {
            function s(e) {
                var i = arguments.length <= 1 || void 0 === arguments[1] ? {} : arguments[1], s = o;
                return o && e !== o[0] && (s = [e].concat(o)), n(t, e, t.helpers, t.partials, i.data || r, a && [i.blockParams].concat(a), s)
            }

            return s = c(n, s, t, o, r, a), s.program = e, s.depth = o ? o.length : 0, s.blockParams = i || 0, s
        }

        function o(t, e, n) {
            return t ? t.call || n.name || (n.name = t, t = n.partials[t]) : t = "@partial-block" === n.name ? n.data["partial-block"] : n.partials[n.name], t
        }

        function s(t, e, n) {
            n.partial = !0, n.ids && (n.data.contextPath = n.ids[0] || n.data.contextPath);
            var r = void 0;
            if (n.fn && n.fn !== l && (r = n.data["partial-block"] = n.fn, r.partials && (n.partials = d.extend({}, n.partials, r.partials))), void 0 === t && r && (t = r), void 0 === t)throw new g["default"]("The partial " + n.name + " could not be found");
            return t instanceof Function ? t(e, n) : void 0
        }

        function l() {
            return ""
        }

        function u(t, e) {
            return e && "root"in e || (e = e ? v.createFrame(e) : {}, e.root = t), e
        }

        function c(t, e, n, r, i, a) {
            if (t.decorator) {
                var o = {};
                e = t.decorator(e, o, n, r && r[0], i, a, r), d.extend(e, o)
            }
            return e
        }

        var p = n(9)["default"], h = n(8)["default"];
        e.__esModule = !0, e.checkRevision = r, e.template = i, e.wrapProgram = a, e.resolvePartial = o, e.invokePartial = s, e.noop = l;
        var f = n(13), d = p(f), m = n(12), g = h(m), v = n(10)
    }, function (t, e) {
        "use strict";
        var n = function () {
            function t() {
                this.yy = {}
            }

            var e = {
                trace: function () {
                },
                yy: {},
                symbols_: {
                    error: 2,
                    root: 3,
                    program: 4,
                    EOF: 5,
                    program_repetition0: 6,
                    statement: 7,
                    mustache: 8,
                    block: 9,
                    rawBlock: 10,
                    partial: 11,
                    partialBlock: 12,
                    content: 13,
                    COMMENT: 14,
                    CONTENT: 15,
                    openRawBlock: 16,
                    rawBlock_repetition_plus0: 17,
                    END_RAW_BLOCK: 18,
                    OPEN_RAW_BLOCK: 19,
                    helperName: 20,
                    openRawBlock_repetition0: 21,
                    openRawBlock_option0: 22,
                    CLOSE_RAW_BLOCK: 23,
                    openBlock: 24,
                    block_option0: 25,
                    closeBlock: 26,
                    openInverse: 27,
                    block_option1: 28,
                    OPEN_BLOCK: 29,
                    openBlock_repetition0: 30,
                    openBlock_option0: 31,
                    openBlock_option1: 32,
                    CLOSE: 33,
                    OPEN_INVERSE: 34,
                    openInverse_repetition0: 35,
                    openInverse_option0: 36,
                    openInverse_option1: 37,
                    openInverseChain: 38,
                    OPEN_INVERSE_CHAIN: 39,
                    openInverseChain_repetition0: 40,
                    openInverseChain_option0: 41,
                    openInverseChain_option1: 42,
                    inverseAndProgram: 43,
                    INVERSE: 44,
                    inverseChain: 45,
                    inverseChain_option0: 46,
                    OPEN_ENDBLOCK: 47,
                    OPEN: 48,
                    mustache_repetition0: 49,
                    mustache_option0: 50,
                    OPEN_UNESCAPED: 51,
                    mustache_repetition1: 52,
                    mustache_option1: 53,
                    CLOSE_UNESCAPED: 54,
                    OPEN_PARTIAL: 55,
                    partialName: 56,
                    partial_repetition0: 57,
                    partial_option0: 58,
                    openPartialBlock: 59,
                    OPEN_PARTIAL_BLOCK: 60,
                    openPartialBlock_repetition0: 61,
                    openPartialBlock_option0: 62,
                    param: 63,
                    sexpr: 64,
                    OPEN_SEXPR: 65,
                    sexpr_repetition0: 66,
                    sexpr_option0: 67,
                    CLOSE_SEXPR: 68,
                    hash: 69,
                    hash_repetition_plus0: 70,
                    hashSegment: 71,
                    ID: 72,
                    EQUALS: 73,
                    blockParams: 74,
                    OPEN_BLOCK_PARAMS: 75,
                    blockParams_repetition_plus0: 76,
                    CLOSE_BLOCK_PARAMS: 77,
                    path: 78,
                    dataName: 79,
                    STRING: 80,
                    NUMBER: 81,
                    BOOLEAN: 82,
                    UNDEFINED: 83,
                    NULL: 84,
                    DATA: 85,
                    pathSegments: 86,
                    SEP: 87,
                    $accept: 0,
                    $end: 1
                },
                terminals_: {
                    2: "error",
                    5: "EOF",
                    14: "COMMENT",
                    15: "CONTENT",
                    18: "END_RAW_BLOCK",
                    19: "OPEN_RAW_BLOCK",
                    23: "CLOSE_RAW_BLOCK",
                    29: "OPEN_BLOCK",
                    33: "CLOSE",
                    34: "OPEN_INVERSE",
                    39: "OPEN_INVERSE_CHAIN",
                    44: "INVERSE",
                    47: "OPEN_ENDBLOCK",
                    48: "OPEN",
                    51: "OPEN_UNESCAPED",
                    54: "CLOSE_UNESCAPED",
                    55: "OPEN_PARTIAL",
                    60: "OPEN_PARTIAL_BLOCK",
                    65: "OPEN_SEXPR",
                    68: "CLOSE_SEXPR",
                    72: "ID",
                    73: "EQUALS",
                    75: "OPEN_BLOCK_PARAMS",
                    77: "CLOSE_BLOCK_PARAMS",
                    80: "STRING",
                    81: "NUMBER",
                    82: "BOOLEAN",
                    83: "UNDEFINED",
                    84: "NULL",
                    85: "DATA",
                    87: "SEP"
                },
                productions_: [0, [3, 2], [4, 1], [7, 1], [7, 1], [7, 1], [7, 1], [7, 1], [7, 1], [7, 1], [13, 1], [10, 3], [16, 5], [9, 4], [9, 4], [24, 6], [27, 6], [38, 6], [43, 2], [45, 3], [45, 1], [26, 3], [8, 5], [8, 5], [11, 5], [12, 3], [59, 5], [63, 1], [63, 1], [64, 5], [69, 1], [71, 3], [74, 3], [20, 1], [20, 1], [20, 1], [20, 1], [20, 1], [20, 1], [20, 1], [56, 1], [56, 1], [79, 2], [78, 1], [86, 3], [86, 1], [6, 0], [6, 2], [17, 1], [17, 2], [21, 0], [21, 2], [22, 0], [22, 1], [25, 0], [25, 1], [28, 0], [28, 1], [30, 0], [30, 2], [31, 0], [31, 1], [32, 0], [32, 1], [35, 0], [35, 2], [36, 0], [36, 1], [37, 0], [37, 1], [40, 0], [40, 2], [41, 0], [41, 1], [42, 0], [42, 1], [46, 0], [46, 1], [49, 0], [49, 2], [50, 0], [50, 1], [52, 0], [52, 2], [53, 0], [53, 1], [57, 0], [57, 2], [58, 0], [58, 1], [61, 0], [61, 2], [62, 0], [62, 1], [66, 0], [66, 2], [67, 0], [67, 1], [70, 1], [70, 2], [76, 1], [76, 2]],
                performAction: function (t, e, n, r, i, a) {
                    var o = a.length - 1;
                    switch (i) {
                        case 1:
                            return a[o - 1];
                        case 2:
                            this.$ = r.prepareProgram(a[o]);
                            break;
                        case 3:
                            this.$ = a[o];
                            break;
                        case 4:
                            this.$ = a[o];
                            break;
                        case 5:
                            this.$ = a[o];
                            break;
                        case 6:
                            this.$ = a[o];
                            break;
                        case 7:
                            this.$ = a[o];
                            break;
                        case 8:
                            this.$ = a[o];
                            break;
                        case 9:
                            this.$ = {
                                type: "CommentStatement",
                                value: r.stripComment(a[o]),
                                strip: r.stripFlags(a[o], a[o]),
                                loc: r.locInfo(this._$)
                            };
                            break;
                        case 10:
                            this.$ = {type: "ContentStatement", original: a[o], value: a[o], loc: r.locInfo(this._$)};
                            break;
                        case 11:
                            this.$ = r.prepareRawBlock(a[o - 2], a[o - 1], a[o], this._$);
                            break;
                        case 12:
                            this.$ = {path: a[o - 3], params: a[o - 2], hash: a[o - 1]};
                            break;
                        case 13:
                            this.$ = r.prepareBlock(a[o - 3], a[o - 2], a[o - 1], a[o], !1, this._$);
                            break;
                        case 14:
                            this.$ = r.prepareBlock(a[o - 3], a[o - 2], a[o - 1], a[o], !0, this._$);
                            break;
                        case 15:
                            this.$ = {
                                open: a[o - 5],
                                path: a[o - 4],
                                params: a[o - 3],
                                hash: a[o - 2],
                                blockParams: a[o - 1],
                                strip: r.stripFlags(a[o - 5], a[o])
                            };
                            break;
                        case 16:
                            this.$ = {
                                path: a[o - 4],
                                params: a[o - 3],
                                hash: a[o - 2],
                                blockParams: a[o - 1],
                                strip: r.stripFlags(a[o - 5], a[o])
                            };
                            break;
                        case 17:
                            this.$ = {
                                path: a[o - 4],
                                params: a[o - 3],
                                hash: a[o - 2],
                                blockParams: a[o - 1],
                                strip: r.stripFlags(a[o - 5], a[o])
                            };
                            break;
                        case 18:
                            this.$ = {strip: r.stripFlags(a[o - 1], a[o - 1]), program: a[o]};
                            break;
                        case 19:
                            var s = r.prepareBlock(a[o - 2], a[o - 1], a[o], a[o], !1, this._$), l = r.prepareProgram([s], a[o - 1].loc);
                            l.chained = !0, this.$ = {strip: a[o - 2].strip, program: l, chain: !0};
                            break;
                        case 20:
                            this.$ = a[o];
                            break;
                        case 21:
                            this.$ = {path: a[o - 1], strip: r.stripFlags(a[o - 2], a[o])};
                            break;
                        case 22:
                            this.$ = r.prepareMustache(a[o - 3], a[o - 2], a[o - 1], a[o - 4], r.stripFlags(a[o - 4], a[o]), this._$);
                            break;
                        case 23:
                            this.$ = r.prepareMustache(a[o - 3], a[o - 2], a[o - 1], a[o - 4], r.stripFlags(a[o - 4], a[o]), this._$);
                            break;
                        case 24:
                            this.$ = {
                                type: "PartialStatement",
                                name: a[o - 3],
                                params: a[o - 2],
                                hash: a[o - 1],
                                indent: "",
                                strip: r.stripFlags(a[o - 4], a[o]),
                                loc: r.locInfo(this._$)
                            };
                            break;
                        case 25:
                            this.$ = r.preparePartialBlock(a[o - 2], a[o - 1], a[o], this._$);
                            break;
                        case 26:
                            this.$ = {
                                path: a[o - 3],
                                params: a[o - 2],
                                hash: a[o - 1],
                                strip: r.stripFlags(a[o - 4], a[o])
                            };
                            break;
                        case 27:
                            this.$ = a[o];
                            break;
                        case 28:
                            this.$ = a[o];
                            break;
                        case 29:
                            this.$ = {
                                type: "SubExpression",
                                path: a[o - 3],
                                params: a[o - 2],
                                hash: a[o - 1],
                                loc: r.locInfo(this._$)
                            };
                            break;
                        case 30:
                            this.$ = {type: "Hash", pairs: a[o], loc: r.locInfo(this._$)};
                            break;
                        case 31:
                            this.$ = {type: "HashPair", key: r.id(a[o - 2]), value: a[o], loc: r.locInfo(this._$)};
                            break;
                        case 32:
                            this.$ = r.id(a[o - 1]);
                            break;
                        case 33:
                            this.$ = a[o];
                            break;
                        case 34:
                            this.$ = a[o];
                            break;
                        case 35:
                            this.$ = {type: "StringLiteral", value: a[o], original: a[o], loc: r.locInfo(this._$)};
                            break;
                        case 36:
                            this.$ = {
                                type: "NumberLiteral",
                                value: Number(a[o]),
                                original: Number(a[o]),
                                loc: r.locInfo(this._$)
                            };
                            break;
                        case 37:
                            this.$ = {
                                type: "BooleanLiteral",
                                value: "true" === a[o],
                                original: "true" === a[o],
                                loc: r.locInfo(this._$)
                            };
                            break;
                        case 38:
                            this.$ = {
                                type: "UndefinedLiteral",
                                original: void 0,
                                value: void 0,
                                loc: r.locInfo(this._$)
                            };
                            break;
                        case 39:
                            this.$ = {type: "NullLiteral", original: null, value: null, loc: r.locInfo(this._$)};
                            break;
                        case 40:
                            this.$ = a[o];
                            break;
                        case 41:
                            this.$ = a[o];
                            break;
                        case 42:
                            this.$ = r.preparePath(!0, a[o], this._$);
                            break;
                        case 43:
                            this.$ = r.preparePath(!1, a[o], this._$);
                            break;
                        case 44:
                            a[o - 2].push({part: r.id(a[o]), original: a[o], separator: a[o - 1]}), this.$ = a[o - 2];
                            break;
                        case 45:
                            this.$ = [{part: r.id(a[o]), original: a[o]}];
                            break;
                        case 46:
                            this.$ = [];
                            break;
                        case 47:
                            a[o - 1].push(a[o]);
                            break;
                        case 48:
                            this.$ = [a[o]];
                            break;
                        case 49:
                            a[o - 1].push(a[o]);
                            break;
                        case 50:
                            this.$ = [];
                            break;
                        case 51:
                            a[o - 1].push(a[o]);
                            break;
                        case 58:
                            this.$ = [];
                            break;
                        case 59:
                            a[o - 1].push(a[o]);
                            break;
                        case 64:
                            this.$ = [];
                            break;
                        case 65:
                            a[o - 1].push(a[o]);
                            break;
                        case 70:
                            this.$ = [];
                            break;
                        case 71:
                            a[o - 1].push(a[o]);
                            break;
                        case 78:
                            this.$ = [];
                            break;
                        case 79:
                            a[o - 1].push(a[o]);
                            break;
                        case 82:
                            this.$ = [];
                            break;
                        case 83:
                            a[o - 1].push(a[o]);
                            break;
                        case 86:
                            this.$ = [];
                            break;
                        case 87:
                            a[o - 1].push(a[o]);
                            break;
                        case 90:
                            this.$ = [];
                            break;
                        case 91:
                            a[o - 1].push(a[o]);
                            break;
                        case 94:
                            this.$ = [];
                            break;
                        case 95:
                            a[o - 1].push(a[o]);
                            break;
                        case 98:
                            this.$ = [a[o]];
                            break;
                        case 99:
                            a[o - 1].push(a[o]);
                            break;
                        case 100:
                            this.$ = [a[o]];
                            break;
                        case 101:
                            a[o - 1].push(a[o])
                    }
                },
                table: [{
                    3: 1,
                    4: 2,
                    5: [2, 46],
                    6: 3,
                    14: [2, 46],
                    15: [2, 46],
                    19: [2, 46],
                    29: [2, 46],
                    34: [2, 46],
                    48: [2, 46],
                    51: [2, 46],
                    55: [2, 46],
                    60: [2, 46]
                }, {1: [3]}, {5: [1, 4]}, {
                    5: [2, 2],
                    7: 5,
                    8: 6,
                    9: 7,
                    10: 8,
                    11: 9,
                    12: 10,
                    13: 11,
                    14: [1, 12],
                    15: [1, 20],
                    16: 17,
                    19: [1, 23],
                    24: 15,
                    27: 16,
                    29: [1, 21],
                    34: [1, 22],
                    39: [2, 2],
                    44: [2, 2],
                    47: [2, 2],
                    48: [1, 13],
                    51: [1, 14],
                    55: [1, 18],
                    59: 19,
                    60: [1, 24]
                }, {1: [2, 1]}, {
                    5: [2, 47],
                    14: [2, 47],
                    15: [2, 47],
                    19: [2, 47],
                    29: [2, 47],
                    34: [2, 47],
                    39: [2, 47],
                    44: [2, 47],
                    47: [2, 47],
                    48: [2, 47],
                    51: [2, 47],
                    55: [2, 47],
                    60: [2, 47]
                }, {
                    5: [2, 3],
                    14: [2, 3],
                    15: [2, 3],
                    19: [2, 3],
                    29: [2, 3],
                    34: [2, 3],
                    39: [2, 3],
                    44: [2, 3],
                    47: [2, 3],
                    48: [2, 3],
                    51: [2, 3],
                    55: [2, 3],
                    60: [2, 3]
                }, {
                    5: [2, 4],
                    14: [2, 4],
                    15: [2, 4],
                    19: [2, 4],
                    29: [2, 4],
                    34: [2, 4],
                    39: [2, 4],
                    44: [2, 4],
                    47: [2, 4],
                    48: [2, 4],
                    51: [2, 4],
                    55: [2, 4],
                    60: [2, 4]
                }, {
                    5: [2, 5],
                    14: [2, 5],
                    15: [2, 5],
                    19: [2, 5],
                    29: [2, 5],
                    34: [2, 5],
                    39: [2, 5],
                    44: [2, 5],
                    47: [2, 5],
                    48: [2, 5],
                    51: [2, 5],
                    55: [2, 5],
                    60: [2, 5]
                }, {
                    5: [2, 6],
                    14: [2, 6],
                    15: [2, 6],
                    19: [2, 6],
                    29: [2, 6],
                    34: [2, 6],
                    39: [2, 6],
                    44: [2, 6],
                    47: [2, 6],
                    48: [2, 6],
                    51: [2, 6],
                    55: [2, 6],
                    60: [2, 6]
                }, {
                    5: [2, 7],
                    14: [2, 7],
                    15: [2, 7],
                    19: [2, 7],
                    29: [2, 7],
                    34: [2, 7],
                    39: [2, 7],
                    44: [2, 7],
                    47: [2, 7],
                    48: [2, 7],
                    51: [2, 7],
                    55: [2, 7],
                    60: [2, 7]
                }, {
                    5: [2, 8],
                    14: [2, 8],
                    15: [2, 8],
                    19: [2, 8],
                    29: [2, 8],
                    34: [2, 8],
                    39: [2, 8],
                    44: [2, 8],
                    47: [2, 8],
                    48: [2, 8],
                    51: [2, 8],
                    55: [2, 8],
                    60: [2, 8]
                }, {
                    5: [2, 9],
                    14: [2, 9],
                    15: [2, 9],
                    19: [2, 9],
                    29: [2, 9],
                    34: [2, 9],
                    39: [2, 9],
                    44: [2, 9],
                    47: [2, 9],
                    48: [2, 9],
                    51: [2, 9],
                    55: [2, 9],
                    60: [2, 9]
                }, {
                    20: 25,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 36,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    4: 37,
                    6: 3,
                    14: [2, 46],
                    15: [2, 46],
                    19: [2, 46],
                    29: [2, 46],
                    34: [2, 46],
                    39: [2, 46],
                    44: [2, 46],
                    47: [2, 46],
                    48: [2, 46],
                    51: [2, 46],
                    55: [2, 46],
                    60: [2, 46]
                }, {
                    4: 38,
                    6: 3,
                    14: [2, 46],
                    15: [2, 46],
                    19: [2, 46],
                    29: [2, 46],
                    34: [2, 46],
                    44: [2, 46],
                    47: [2, 46],
                    48: [2, 46],
                    51: [2, 46],
                    55: [2, 46],
                    60: [2, 46]
                }, {13: 40, 15: [1, 20], 17: 39}, {
                    20: 42,
                    56: 41,
                    64: 43,
                    65: [1, 44],
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    4: 45,
                    6: 3,
                    14: [2, 46],
                    15: [2, 46],
                    19: [2, 46],
                    29: [2, 46],
                    34: [2, 46],
                    47: [2, 46],
                    48: [2, 46],
                    51: [2, 46],
                    55: [2, 46],
                    60: [2, 46]
                }, {
                    5: [2, 10],
                    14: [2, 10],
                    15: [2, 10],
                    18: [2, 10],
                    19: [2, 10],
                    29: [2, 10],
                    34: [2, 10],
                    39: [2, 10],
                    44: [2, 10],
                    47: [2, 10],
                    48: [2, 10],
                    51: [2, 10],
                    55: [2, 10],
                    60: [2, 10]
                }, {
                    20: 46,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 47,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 48,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 42,
                    56: 49,
                    64: 43,
                    65: [1, 44],
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    33: [2, 78],
                    49: 50,
                    65: [2, 78],
                    72: [2, 78],
                    80: [2, 78],
                    81: [2, 78],
                    82: [2, 78],
                    83: [2, 78],
                    84: [2, 78],
                    85: [2, 78]
                }, {
                    23: [2, 33],
                    33: [2, 33],
                    54: [2, 33],
                    65: [2, 33],
                    68: [2, 33],
                    72: [2, 33],
                    75: [2, 33],
                    80: [2, 33],
                    81: [2, 33],
                    82: [2, 33],
                    83: [2, 33],
                    84: [2, 33],
                    85: [2, 33]
                }, {
                    23: [2, 34],
                    33: [2, 34],
                    54: [2, 34],
                    65: [2, 34],
                    68: [2, 34],
                    72: [2, 34],
                    75: [2, 34],
                    80: [2, 34],
                    81: [2, 34],
                    82: [2, 34],
                    83: [2, 34],
                    84: [2, 34],
                    85: [2, 34]
                }, {
                    23: [2, 35],
                    33: [2, 35],
                    54: [2, 35],
                    65: [2, 35],
                    68: [2, 35],
                    72: [2, 35],
                    75: [2, 35],
                    80: [2, 35],
                    81: [2, 35],
                    82: [2, 35],
                    83: [2, 35],
                    84: [2, 35],
                    85: [2, 35]
                }, {
                    23: [2, 36],
                    33: [2, 36],
                    54: [2, 36],
                    65: [2, 36],
                    68: [2, 36],
                    72: [2, 36],
                    75: [2, 36],
                    80: [2, 36],
                    81: [2, 36],
                    82: [2, 36],
                    83: [2, 36],
                    84: [2, 36],
                    85: [2, 36]
                }, {
                    23: [2, 37],
                    33: [2, 37],
                    54: [2, 37],
                    65: [2, 37],
                    68: [2, 37],
                    72: [2, 37],
                    75: [2, 37],
                    80: [2, 37],
                    81: [2, 37],
                    82: [2, 37],
                    83: [2, 37],
                    84: [2, 37],
                    85: [2, 37]
                }, {
                    23: [2, 38],
                    33: [2, 38],
                    54: [2, 38],
                    65: [2, 38],
                    68: [2, 38],
                    72: [2, 38],
                    75: [2, 38],
                    80: [2, 38],
                    81: [2, 38],
                    82: [2, 38],
                    83: [2, 38],
                    84: [2, 38],
                    85: [2, 38]
                }, {
                    23: [2, 39],
                    33: [2, 39],
                    54: [2, 39],
                    65: [2, 39],
                    68: [2, 39],
                    72: [2, 39],
                    75: [2, 39],
                    80: [2, 39],
                    81: [2, 39],
                    82: [2, 39],
                    83: [2, 39],
                    84: [2, 39],
                    85: [2, 39]
                }, {
                    23: [2, 43],
                    33: [2, 43],
                    54: [2, 43],
                    65: [2, 43],
                    68: [2, 43],
                    72: [2, 43],
                    75: [2, 43],
                    80: [2, 43],
                    81: [2, 43],
                    82: [2, 43],
                    83: [2, 43],
                    84: [2, 43],
                    85: [2, 43],
                    87: [1, 51]
                }, {72: [1, 35], 86: 52}, {
                    23: [2, 45],
                    33: [2, 45],
                    54: [2, 45],
                    65: [2, 45],
                    68: [2, 45],
                    72: [2, 45],
                    75: [2, 45],
                    80: [2, 45],
                    81: [2, 45],
                    82: [2, 45],
                    83: [2, 45],
                    84: [2, 45],
                    85: [2, 45],
                    87: [2, 45]
                }, {
                    52: 53,
                    54: [2, 82],
                    65: [2, 82],
                    72: [2, 82],
                    80: [2, 82],
                    81: [2, 82],
                    82: [2, 82],
                    83: [2, 82],
                    84: [2, 82],
                    85: [2, 82]
                }, {25: 54, 38: 56, 39: [1, 58], 43: 57, 44: [1, 59], 45: 55, 47: [2, 54]}, {
                    28: 60,
                    43: 61,
                    44: [1, 59],
                    47: [2, 56]
                }, {13: 63, 15: [1, 20], 18: [1, 62]}, {15: [2, 48], 18: [2, 48]}, {
                    33: [2, 86],
                    57: 64,
                    65: [2, 86],
                    72: [2, 86],
                    80: [2, 86],
                    81: [2, 86],
                    82: [2, 86],
                    83: [2, 86],
                    84: [2, 86],
                    85: [2, 86]
                }, {
                    33: [2, 40],
                    65: [2, 40],
                    72: [2, 40],
                    80: [2, 40],
                    81: [2, 40],
                    82: [2, 40],
                    83: [2, 40],
                    84: [2, 40],
                    85: [2, 40]
                }, {
                    33: [2, 41],
                    65: [2, 41],
                    72: [2, 41],
                    80: [2, 41],
                    81: [2, 41],
                    82: [2, 41],
                    83: [2, 41],
                    84: [2, 41],
                    85: [2, 41]
                }, {
                    20: 65,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {26: 66, 47: [1, 67]}, {
                    30: 68,
                    33: [2, 58],
                    65: [2, 58],
                    72: [2, 58],
                    75: [2, 58],
                    80: [2, 58],
                    81: [2, 58],
                    82: [2, 58],
                    83: [2, 58],
                    84: [2, 58],
                    85: [2, 58]
                }, {
                    33: [2, 64],
                    35: 69,
                    65: [2, 64],
                    72: [2, 64],
                    75: [2, 64],
                    80: [2, 64],
                    81: [2, 64],
                    82: [2, 64],
                    83: [2, 64],
                    84: [2, 64],
                    85: [2, 64]
                }, {
                    21: 70,
                    23: [2, 50],
                    65: [2, 50],
                    72: [2, 50],
                    80: [2, 50],
                    81: [2, 50],
                    82: [2, 50],
                    83: [2, 50],
                    84: [2, 50],
                    85: [2, 50]
                }, {
                    33: [2, 90],
                    61: 71,
                    65: [2, 90],
                    72: [2, 90],
                    80: [2, 90],
                    81: [2, 90],
                    82: [2, 90],
                    83: [2, 90],
                    84: [2, 90],
                    85: [2, 90]
                }, {
                    20: 75,
                    33: [2, 80],
                    50: 72,
                    63: 73,
                    64: 76,
                    65: [1, 44],
                    69: 74,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {72: [1, 80]}, {
                    23: [2, 42],
                    33: [2, 42],
                    54: [2, 42],
                    65: [2, 42],
                    68: [2, 42],
                    72: [2, 42],
                    75: [2, 42],
                    80: [2, 42],
                    81: [2, 42],
                    82: [2, 42],
                    83: [2, 42],
                    84: [2, 42],
                    85: [2, 42],
                    87: [1, 51]
                }, {
                    20: 75,
                    53: 81,
                    54: [2, 84],
                    63: 82,
                    64: 76,
                    65: [1, 44],
                    69: 83,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {26: 84, 47: [1, 67]}, {47: [2, 55]}, {
                    4: 85,
                    6: 3,
                    14: [2, 46],
                    15: [2, 46],
                    19: [2, 46],
                    29: [2, 46],
                    34: [2, 46],
                    39: [2, 46],
                    44: [2, 46],
                    47: [2, 46],
                    48: [2, 46],
                    51: [2, 46],
                    55: [2, 46],
                    60: [2, 46]
                }, {47: [2, 20]}, {
                    20: 86,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    4: 87,
                    6: 3,
                    14: [2, 46],
                    15: [2, 46],
                    19: [2, 46],
                    29: [2, 46],
                    34: [2, 46],
                    47: [2, 46],
                    48: [2, 46],
                    51: [2, 46],
                    55: [2, 46],
                    60: [2, 46]
                }, {26: 88, 47: [1, 67]}, {47: [2, 57]}, {
                    5: [2, 11],
                    14: [2, 11],
                    15: [2, 11],
                    19: [2, 11],
                    29: [2, 11],
                    34: [2, 11],
                    39: [2, 11],
                    44: [2, 11],
                    47: [2, 11],
                    48: [2, 11],
                    51: [2, 11],
                    55: [2, 11],
                    60: [2, 11]
                }, {15: [2, 49], 18: [2, 49]}, {
                    20: 75,
                    33: [2, 88],
                    58: 89,
                    63: 90,
                    64: 76,
                    65: [1, 44],
                    69: 91,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    65: [2, 94],
                    66: 92,
                    68: [2, 94],
                    72: [2, 94],
                    80: [2, 94],
                    81: [2, 94],
                    82: [2, 94],
                    83: [2, 94],
                    84: [2, 94],
                    85: [2, 94]
                }, {
                    5: [2, 25],
                    14: [2, 25],
                    15: [2, 25],
                    19: [2, 25],
                    29: [2, 25],
                    34: [2, 25],
                    39: [2, 25],
                    44: [2, 25],
                    47: [2, 25],
                    48: [2, 25],
                    51: [2, 25],
                    55: [2, 25],
                    60: [2, 25]
                }, {
                    20: 93,
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 75,
                    31: 94,
                    33: [2, 60],
                    63: 95,
                    64: 76,
                    65: [1, 44],
                    69: 96,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    75: [2, 60],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 75,
                    33: [2, 66],
                    36: 97,
                    63: 98,
                    64: 76,
                    65: [1, 44],
                    69: 99,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    75: [2, 66],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 75,
                    22: 100,
                    23: [2, 52],
                    63: 101,
                    64: 76,
                    65: [1, 44],
                    69: 102,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    20: 75,
                    33: [2, 92],
                    62: 103,
                    63: 104,
                    64: 76,
                    65: [1, 44],
                    69: 105,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {33: [1, 106]}, {
                    33: [2, 79],
                    65: [2, 79],
                    72: [2, 79],
                    80: [2, 79],
                    81: [2, 79],
                    82: [2, 79],
                    83: [2, 79],
                    84: [2, 79],
                    85: [2, 79]
                }, {33: [2, 81]}, {
                    23: [2, 27],
                    33: [2, 27],
                    54: [2, 27],
                    65: [2, 27],
                    68: [2, 27],
                    72: [2, 27],
                    75: [2, 27],
                    80: [2, 27],
                    81: [2, 27],
                    82: [2, 27],
                    83: [2, 27],
                    84: [2, 27],
                    85: [2, 27]
                }, {
                    23: [2, 28],
                    33: [2, 28],
                    54: [2, 28],
                    65: [2, 28],
                    68: [2, 28],
                    72: [2, 28],
                    75: [2, 28],
                    80: [2, 28],
                    81: [2, 28],
                    82: [2, 28],
                    83: [2, 28],
                    84: [2, 28],
                    85: [2, 28]
                }, {
                    23: [2, 30],
                    33: [2, 30],
                    54: [2, 30],
                    68: [2, 30],
                    71: 107,
                    72: [1, 108],
                    75: [2, 30]
                }, {23: [2, 98], 33: [2, 98], 54: [2, 98], 68: [2, 98], 72: [2, 98], 75: [2, 98]}, {
                    23: [2, 45],
                    33: [2, 45],
                    54: [2, 45],
                    65: [2, 45],
                    68: [2, 45],
                    72: [2, 45],
                    73: [1, 109],
                    75: [2, 45],
                    80: [2, 45],
                    81: [2, 45],
                    82: [2, 45],
                    83: [2, 45],
                    84: [2, 45],
                    85: [2, 45],
                    87: [2, 45]
                }, {
                    23: [2, 44],
                    33: [2, 44],
                    54: [2, 44],
                    65: [2, 44],
                    68: [2, 44],
                    72: [2, 44],
                    75: [2, 44],
                    80: [2, 44],
                    81: [2, 44],
                    82: [2, 44],
                    83: [2, 44],
                    84: [2, 44],
                    85: [2, 44],
                    87: [2, 44]
                }, {54: [1, 110]}, {
                    54: [2, 83],
                    65: [2, 83],
                    72: [2, 83],
                    80: [2, 83],
                    81: [2, 83],
                    82: [2, 83],
                    83: [2, 83],
                    84: [2, 83],
                    85: [2, 83]
                }, {54: [2, 85]}, {
                    5: [2, 13],
                    14: [2, 13],
                    15: [2, 13],
                    19: [2, 13],
                    29: [2, 13],
                    34: [2, 13],
                    39: [2, 13],
                    44: [2, 13],
                    47: [2, 13],
                    48: [2, 13],
                    51: [2, 13],
                    55: [2, 13],
                    60: [2, 13]
                }, {38: 56, 39: [1, 58], 43: 57, 44: [1, 59], 45: 112, 46: 111, 47: [2, 76]}, {
                    33: [2, 70],
                    40: 113,
                    65: [2, 70],
                    72: [2, 70],
                    75: [2, 70],
                    80: [2, 70],
                    81: [2, 70],
                    82: [2, 70],
                    83: [2, 70],
                    84: [2, 70],
                    85: [2, 70]
                }, {47: [2, 18]}, {
                    5: [2, 14],
                    14: [2, 14],
                    15: [2, 14],
                    19: [2, 14],
                    29: [2, 14],
                    34: [2, 14],
                    39: [2, 14],
                    44: [2, 14],
                    47: [2, 14],
                    48: [2, 14],
                    51: [2, 14],
                    55: [2, 14],
                    60: [2, 14]
                }, {33: [1, 114]}, {
                    33: [2, 87],
                    65: [2, 87],
                    72: [2, 87],
                    80: [2, 87],
                    81: [2, 87],
                    82: [2, 87],
                    83: [2, 87],
                    84: [2, 87],
                    85: [2, 87]
                }, {33: [2, 89]}, {
                    20: 75,
                    63: 116,
                    64: 76,
                    65: [1, 44],
                    67: 115,
                    68: [2, 96],
                    69: 117,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {33: [1, 118]}, {32: 119, 33: [2, 62], 74: 120, 75: [1, 121]}, {
                    33: [2, 59],
                    65: [2, 59],
                    72: [2, 59],
                    75: [2, 59],
                    80: [2, 59],
                    81: [2, 59],
                    82: [2, 59],
                    83: [2, 59],
                    84: [2, 59],
                    85: [2, 59]
                }, {33: [2, 61], 75: [2, 61]}, {33: [2, 68], 37: 122, 74: 123, 75: [1, 121]}, {
                    33: [2, 65],
                    65: [2, 65],
                    72: [2, 65],
                    75: [2, 65],
                    80: [2, 65],
                    81: [2, 65],
                    82: [2, 65],
                    83: [2, 65],
                    84: [2, 65],
                    85: [2, 65]
                }, {33: [2, 67], 75: [2, 67]}, {23: [1, 124]}, {
                    23: [2, 51],
                    65: [2, 51],
                    72: [2, 51],
                    80: [2, 51],
                    81: [2, 51],
                    82: [2, 51],
                    83: [2, 51],
                    84: [2, 51],
                    85: [2, 51]
                }, {23: [2, 53]}, {33: [1, 125]}, {
                    33: [2, 91],
                    65: [2, 91],
                    72: [2, 91],
                    80: [2, 91],
                    81: [2, 91],
                    82: [2, 91],
                    83: [2, 91],
                    84: [2, 91],
                    85: [2, 91]
                }, {33: [2, 93]}, {
                    5: [2, 22],
                    14: [2, 22],
                    15: [2, 22],
                    19: [2, 22],
                    29: [2, 22],
                    34: [2, 22],
                    39: [2, 22],
                    44: [2, 22],
                    47: [2, 22],
                    48: [2, 22],
                    51: [2, 22],
                    55: [2, 22],
                    60: [2, 22]
                }, {
                    23: [2, 99],
                    33: [2, 99],
                    54: [2, 99],
                    68: [2, 99],
                    72: [2, 99],
                    75: [2, 99]
                }, {73: [1, 109]}, {
                    20: 75,
                    63: 126,
                    64: 76,
                    65: [1, 44],
                    72: [1, 35],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    5: [2, 23],
                    14: [2, 23],
                    15: [2, 23],
                    19: [2, 23],
                    29: [2, 23],
                    34: [2, 23],
                    39: [2, 23],
                    44: [2, 23],
                    47: [2, 23],
                    48: [2, 23],
                    51: [2, 23],
                    55: [2, 23],
                    60: [2, 23]
                }, {47: [2, 19]}, {47: [2, 77]}, {
                    20: 75,
                    33: [2, 72],
                    41: 127,
                    63: 128,
                    64: 76,
                    65: [1, 44],
                    69: 129,
                    70: 77,
                    71: 78,
                    72: [1, 79],
                    75: [2, 72],
                    78: 26,
                    79: 27,
                    80: [1, 28],
                    81: [1, 29],
                    82: [1, 30],
                    83: [1, 31],
                    84: [1, 32],
                    85: [1, 34],
                    86: 33
                }, {
                    5: [2, 24],
                    14: [2, 24],
                    15: [2, 24],
                    19: [2, 24],
                    29: [2, 24],
                    34: [2, 24],
                    39: [2, 24],
                    44: [2, 24],
                    47: [2, 24],
                    48: [2, 24],
                    51: [2, 24],
                    55: [2, 24],
                    60: [2, 24]
                }, {68: [1, 130]}, {
                    65: [2, 95],
                    68: [2, 95],
                    72: [2, 95],
                    80: [2, 95],
                    81: [2, 95],
                    82: [2, 95],
                    83: [2, 95],
                    84: [2, 95],
                    85: [2, 95]
                }, {68: [2, 97]}, {
                    5: [2, 21],
                    14: [2, 21],
                    15: [2, 21],
                    19: [2, 21],
                    29: [2, 21],
                    34: [2, 21],
                    39: [2, 21],
                    44: [2, 21],
                    47: [2, 21],
                    48: [2, 21],
                    51: [2, 21],
                    55: [2, 21],
                    60: [2, 21]
                }, {33: [1, 131]}, {33: [2, 63]}, {
                    72: [1, 133],
                    76: 132
                }, {33: [1, 134]}, {33: [2, 69]}, {15: [2, 12]}, {
                    14: [2, 26],
                    15: [2, 26],
                    19: [2, 26],
                    29: [2, 26],
                    34: [2, 26],
                    47: [2, 26],
                    48: [2, 26],
                    51: [2, 26],
                    55: [2, 26],
                    60: [2, 26]
                }, {23: [2, 31], 33: [2, 31], 54: [2, 31], 68: [2, 31], 72: [2, 31], 75: [2, 31]}, {
                    33: [2, 74],
                    42: 135,
                    74: 136,
                    75: [1, 121]
                }, {
                    33: [2, 71],
                    65: [2, 71],
                    72: [2, 71],
                    75: [2, 71],
                    80: [2, 71],
                    81: [2, 71],
                    82: [2, 71],
                    83: [2, 71],
                    84: [2, 71],
                    85: [2, 71]
                }, {33: [2, 73], 75: [2, 73]}, {
                    23: [2, 29],
                    33: [2, 29],
                    54: [2, 29],
                    65: [2, 29],
                    68: [2, 29],
                    72: [2, 29],
                    75: [2, 29],
                    80: [2, 29],
                    81: [2, 29],
                    82: [2, 29],
                    83: [2, 29],
                    84: [2, 29],
                    85: [2, 29]
                }, {
                    14: [2, 15],
                    15: [2, 15],
                    19: [2, 15],
                    29: [2, 15],
                    34: [2, 15],
                    39: [2, 15],
                    44: [2, 15],
                    47: [2, 15],
                    48: [2, 15],
                    51: [2, 15],
                    55: [2, 15],
                    60: [2, 15]
                }, {72: [1, 138], 77: [1, 137]}, {72: [2, 100], 77: [2, 100]}, {
                    14: [2, 16],
                    15: [2, 16],
                    19: [2, 16],
                    29: [2, 16],
                    34: [2, 16],
                    44: [2, 16],
                    47: [2, 16],
                    48: [2, 16],
                    51: [2, 16],
                    55: [2, 16],
                    60: [2, 16]
                }, {33: [1, 139]}, {33: [2, 75]}, {33: [2, 32]}, {72: [2, 101], 77: [2, 101]}, {
                    14: [2, 17],
                    15: [2, 17],
                    19: [2, 17],
                    29: [2, 17],
                    34: [2, 17],
                    39: [2, 17],
                    44: [2, 17],
                    47: [2, 17],
                    48: [2, 17],
                    51: [2, 17],
                    55: [2, 17],
                    60: [2, 17]
                }],
                defaultActions: {
                    4: [2, 1],
                    55: [2, 55],
                    57: [2, 20],
                    61: [2, 57],
                    74: [2, 81],
                    83: [2, 85],
                    87: [2, 18],
                    91: [2, 89],
                    102: [2, 53],
                    105: [2, 93],
                    111: [2, 19],
                    112: [2, 77],
                    117: [2, 97],
                    120: [2, 63],
                    123: [2, 69],
                    124: [2, 12],
                    136: [2, 75],
                    137: [2, 32]
                },
                parseError: function (t) {
                    throw new Error(t)
                },
                parse: function (t) {
                    function e() {
                        var t;
                        return t = n.lexer.lex() || 1, "number" != typeof t && (t = n.symbols_[t] || t), t
                    }

                    var n = this, r = [0], i = [null], a = [], o = this.table, s = "", l = 0, u = 0, c = 0;
                    this.lexer.setInput(t), this.lexer.yy = this.yy, this.yy.lexer = this.lexer, this.yy.parser = this, "undefined" == typeof this.lexer.yylloc && (this.lexer.yylloc = {});
                    var p = this.lexer.yylloc;
                    a.push(p);
                    var h = this.lexer.options && this.lexer.options.ranges;
                    "function" == typeof this.yy.parseError && (this.parseError = this.yy.parseError);
                    for (var f, d, m, g, v, y, b, k, _, x = {}; ;) {
                        if (m = r[r.length - 1], this.defaultActions[m] ? g = this.defaultActions[m] : ((null === f || "undefined" == typeof f) && (f = e()), g = o[m] && o[m][f]), "undefined" == typeof g || !g.length || !g[0]) {
                            var w = "";
                            if (!c) {
                                _ = [];
                                for (y in o[m])this.terminals_[y] && y > 2 && _.push("'" + this.terminals_[y] + "'");
                                w = this.lexer.showPosition ? "Parse error on line " + (l + 1) + ":\n" + this.lexer.showPosition() + "\nExpecting " + _.join(", ") + ", got '" + (this.terminals_[f] || f) + "'" : "Parse error on line " + (l + 1) + ": Unexpected " + (1 == f ? "end of input" : "'" + (this.terminals_[f] || f) + "'"), this.parseError(w, {
                                    text: this.lexer.match,
                                    token: this.terminals_[f] || f,
                                    line: this.lexer.yylineno,
                                    loc: p,
                                    expected: _
                                })
                            }
                        }
                        if (g[0]instanceof Array && g.length > 1)throw new Error("Parse Error: multiple actions possible at state: " + m + ", token: " + f);
                        switch (g[0]) {
                            case 1:
                                r.push(f), i.push(this.lexer.yytext), a.push(this.lexer.yylloc), r.push(g[1]), f = null, d ? (f = d, d = null) : (u = this.lexer.yyleng, s = this.lexer.yytext, l = this.lexer.yylineno, p = this.lexer.yylloc, c > 0 && c--);
                                break;
                            case 2:
                                if (b = this.productions_[g[1]][1], x.$ = i[i.length - b], x._$ = {
                                        first_line: a[a.length - (b || 1)].first_line,
                                        last_line: a[a.length - 1].last_line,
                                        first_column: a[a.length - (b || 1)].first_column,
                                        last_column: a[a.length - 1].last_column
                                    }, h && (x._$.range = [a[a.length - (b || 1)].range[0], a[a.length - 1].range[1]]), v = this.performAction.call(x, s, u, l, this.yy, g[1], i, a), "undefined" != typeof v)return v;
                                b && (r = r.slice(0, -1 * b * 2), i = i.slice(0, -1 * b), a = a.slice(0, -1 * b)), r.push(this.productions_[g[1]][0]), i.push(x.$), a.push(x._$), k = o[r[r.length - 2]][r[r.length - 1]], r.push(k);
                                break;
                            case 3:
                                return !0
                        }
                    }
                    return !0
                }
            }, n = function () {
                var t = {
                    EOF: 1, parseError: function (t, e) {
                        if (!this.yy.parser)throw new Error(t);
                        this.yy.parser.parseError(t, e)
                    }, setInput: function (t) {
                        return this._input = t, this._more = this._less = this.done = !1, this.yylineno = this.yyleng = 0, this.yytext = this.matched = this.match = "", this.conditionStack = ["INITIAL"], this.yylloc = {
                            first_line: 1,
                            first_column: 0,
                            last_line: 1,
                            last_column: 0
                        }, this.options.ranges && (this.yylloc.range = [0, 0]), this.offset = 0, this
                    }, input: function () {
                        var t = this._input[0];
                        this.yytext += t, this.yyleng++, this.offset++, this.match += t, this.matched += t;
                        var e = t.match(/(?:\r\n?|\n).*/g);
                        return e ? (this.yylineno++, this.yylloc.last_line++) : this.yylloc.last_column++, this.options.ranges && this.yylloc.range[1]++, this._input = this._input.slice(1), t
                    }, unput: function (t) {
                        var e = t.length, n = t.split(/(?:\r\n?|\n)/g);
                        this._input = t + this._input, this.yytext = this.yytext.substr(0, this.yytext.length - e - 1), this.offset -= e;
                        var r = this.match.split(/(?:\r\n?|\n)/g);
                        this.match = this.match.substr(0, this.match.length - 1), this.matched = this.matched.substr(0, this.matched.length - 1), n.length - 1 && (this.yylineno -= n.length - 1);
                        var i = this.yylloc.range;
                        return this.yylloc = {
                            first_line: this.yylloc.first_line,
                            last_line: this.yylineno + 1,
                            first_column: this.yylloc.first_column,
                            last_column: n ? (n.length === r.length ? this.yylloc.first_column : 0) + r[r.length - n.length].length - n[0].length : this.yylloc.first_column - e
                        }, this.options.ranges && (this.yylloc.range = [i[0], i[0] + this.yyleng - e]), this
                    }, more: function () {
                        return this._more = !0, this
                    }, less: function (t) {
                        this.unput(this.match.slice(t))
                    }, pastInput: function () {
                        var t = this.matched.substr(0, this.matched.length - this.match.length);
                        return (t.length > 20 ? "..." : "") + t.substr(-20).replace(/\n/g, "")
                    }, upcomingInput: function () {
                        var t = this.match;
                        return t.length < 20 && (t += this._input.substr(0, 20 - t.length)), (t.substr(0, 20) + (t.length > 20 ? "..." : "")).replace(/\n/g, "")
                    }, showPosition: function () {
                        var t = this.pastInput(), e = new Array(t.length + 1).join("-");
                        return t + this.upcomingInput() + "\n" + e + "^"
                    }, next: function () {
                        if (this.done)return this.EOF;
                        this._input || (this.done = !0);
                        var t, e, n, r, i;
                        this._more || (this.yytext = "", this.match = "");
                        for (var a = this._currentRules(), o = 0; o < a.length && (n = this._input.match(this.rules[a[o]]), !n || e && !(n[0].length > e[0].length) || (e = n, r = o, this.options.flex)); o++);
                        return e ? (i = e[0].match(/(?:\r\n?|\n).*/g), i && (this.yylineno += i.length), this.yylloc = {
                            first_line: this.yylloc.last_line,
                            last_line: this.yylineno + 1,
                            first_column: this.yylloc.last_column,
                            last_column: i ? i[i.length - 1].length - i[i.length - 1].match(/\r?\n?/)[0].length : this.yylloc.last_column + e[0].length
                        }, this.yytext += e[0], this.match += e[0], this.matches = e, this.yyleng = this.yytext.length, this.options.ranges && (this.yylloc.range = [this.offset, this.offset += this.yyleng]), this._more = !1, this._input = this._input.slice(e[0].length), this.matched += e[0], t = this.performAction.call(this, this.yy, this, a[r], this.conditionStack[this.conditionStack.length - 1]), this.done && this._input && (this.done = !1), t ? t : void 0) : "" === this._input ? this.EOF : this.parseError("Lexical error on line " + (this.yylineno + 1) + ". Unrecognized text.\n" + this.showPosition(), {
                            text: "",
                            token: null,
                            line: this.yylineno
                        })
                    }, lex: function () {
                        var t = this.next();
                        return "undefined" != typeof t ? t : this.lex()
                    }, begin: function (t) {
                        this.conditionStack.push(t)
                    }, popState: function () {
                        return this.conditionStack.pop()
                    }, _currentRules: function () {
                        return this.conditions[this.conditionStack[this.conditionStack.length - 1]].rules
                    }, topState: function () {
                        return this.conditionStack[this.conditionStack.length - 2]
                    }, pushState: function (t) {
                        this.begin(t)
                    }
                };
                return t.options = {}, t.performAction = function (t, e, n) {
                    function r(t, n) {
                        return e.yytext = e.yytext.substr(t, e.yyleng - n)
                    }

                    switch (n) {
                        case 0:
                            if ("\\\\" === e.yytext.slice(-2) ? (r(0, 1), this.begin("mu")) : "\\" === e.yytext.slice(-1) ? (r(0, 1), this.begin("emu")) : this.begin("mu"), e.yytext)return 15;
                            break;
                        case 1:
                            return 15;
                        case 2:
                            return this.popState(), 15;
                        case 3:
                            return this.begin("raw"), 15;
                        case 4:
                            return this.popState(), "raw" === this.conditionStack[this.conditionStack.length - 1] ? 15 : (e.yytext = e.yytext.substr(5, e.yyleng - 9), "END_RAW_BLOCK");
                        case 5:
                            return 15;
                        case 6:
                            return this.popState(), 14;
                        case 7:
                            return 65;
                        case 8:
                            return 68;
                        case 9:
                            return 19;
                        case 10:
                            return this.popState(), this.begin("raw"), 23;
                        case 11:
                            return 55;
                        case 12:
                            return 60;
                        case 13:
                            return 29;
                        case 14:
                            return 47;
                        case 15:
                            return this.popState(), 44;
                        case 16:
                            return this.popState(), 44;
                        case 17:
                            return 34;
                        case 18:
                            return 39;
                        case 19:
                            return 51;
                        case 20:
                            return 48;
                        case 21:
                            this.unput(e.yytext), this.popState(), this.begin("com");
                            break;
                        case 22:
                            return this.popState(), 14;
                        case 23:
                            return 48;
                        case 24:
                            return 73;
                        case 25:
                            return 72;
                        case 26:
                            return 72;
                        case 27:
                            return 87;
                        case 28:
                            break;
                        case 29:
                            return this.popState(), 54;
                        case 30:
                            return this.popState(), 33;
                        case 31:
                            return e.yytext = r(1, 2).replace(/\\"/g, '"'), 80;
                        case 32:
                            return e.yytext = r(1, 2).replace(/\\'/g, "'"), 80;
                        case 33:
                            return 85;
                        case 34:
                            return 82;
                        case 35:
                            return 82;
                        case 36:
                            return 83;
                        case 37:
                            return 84;
                        case 38:
                            return 81;
                        case 39:
                            return 75;
                        case 40:
                            return 77;
                        case 41:
                            return 72;
                        case 42:
                            return 72;
                        case 43:
                            return "INVALID";
                        case 44:
                            return 5
                    }
                }, t.rules = [/^(?:[^\x00]*?(?=(\{\{)))/, /^(?:[^\x00]+)/, /^(?:[^\x00]{2,}?(?=(\{\{|\\\{\{|\\\\\{\{|$)))/, /^(?:\{\{\{\{(?=[^/]))/, /^(?:\{\{\{\{\/[^\s!"#%-,\.\/;->@\[-\^`\{-~]+(?=[=}\s\/.])\}\}\}\})/, /^(?:[^\x00]*?(?=(\{\{\{\{)))/, /^(?:[\s\S]*?--(~)?\}\})/, /^(?:\()/, /^(?:\))/, /^(?:\{\{\{\{)/, /^(?:\}\}\}\})/, /^(?:\{\{(~)?>)/, /^(?:\{\{(~)?#>)/, /^(?:\{\{(~)?#\*?)/, /^(?:\{\{(~)?\/)/, /^(?:\{\{(~)?\^\s*(~)?\}\})/, /^(?:\{\{(~)?\s*else\s*(~)?\}\})/, /^(?:\{\{(~)?\^)/, /^(?:\{\{(~)?\s*else\b)/, /^(?:\{\{(~)?\{)/, /^(?:\{\{(~)?&)/, /^(?:\{\{(~)?!--)/, /^(?:\{\{(~)?![\s\S]*?\}\})/, /^(?:\{\{(~)?\*?)/, /^(?:=)/, /^(?:\.\.)/, /^(?:\.(?=([=~}\s\/.)|])))/, /^(?:[\/.])/, /^(?:\s+)/, /^(?:\}(~)?\}\})/, /^(?:(~)?\}\})/, /^(?:"(\\["]|[^"])*")/, /^(?:'(\\[']|[^'])*')/, /^(?:@)/, /^(?:true(?=([~}\s)])))/, /^(?:false(?=([~}\s)])))/, /^(?:undefined(?=([~}\s)])))/, /^(?:null(?=([~}\s)])))/, /^(?:-?[0-9]+(?:\.[0-9]+)?(?=([~}\s)])))/, /^(?:as\s+\|)/, /^(?:\|)/, /^(?:([^\s!"#%-,\.\/;->@\[-\^`\{-~]+(?=([=~}\s\/.)|]))))/, /^(?:\[[^\]]*\])/, /^(?:.)/, /^(?:$)/], t.conditions = {
                    mu: {
                        rules: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44],
                        inclusive: !1
                    },
                    emu: {rules: [2], inclusive: !1},
                    com: {rules: [6], inclusive: !1},
                    raw: {rules: [3, 4, 5], inclusive: !1},
                    INITIAL: {rules: [0, 1, 44], inclusive: !0}
                }, t
            }();
            return e.lexer = n, t.prototype = e, e.Parser = t, new t
        }();
        e.__esModule = !0, e["default"] = n
    }, function (t, e, n) {
        "use strict";
        function r() {
            var t = arguments.length <= 0 || void 0 === arguments[0] ? {} : arguments[0];
            this.options = t
        }

        function i(t, e, n) {
            void 0 === e && (e = t.length);
            var r = t[e - 1], i = t[e - 2];
            return r ? "ContentStatement" === r.type ? (i || !n ? /\r?\n\s*?$/ : /(^|\r?\n)\s*?$/).test(r.original) : void 0 : n
        }

        function a(t, e, n) {
            void 0 === e && (e = -1);
            var r = t[e + 1], i = t[e + 2];
            return r ? "ContentStatement" === r.type ? (i || !n ? /^\s*?\r?\n/ : /^\s*?(\r?\n|$)/).test(r.original) : void 0 : n
        }

        function o(t, e, n) {
            var r = t[null == e ? 0 : e + 1];
            if (r && "ContentStatement" === r.type && (n || !r.rightStripped)) {
                var i = r.value;
                r.value = r.value.replace(n ? /^\s+/ : /^[ \t]*\r?\n?/, ""), r.rightStripped = r.value !== i
            }
        }

        function s(t, e, n) {
            var r = t[null == e ? t.length - 1 : e - 1];
            if (r && "ContentStatement" === r.type && (n || !r.leftStripped)) {
                var i = r.value;
                return r.value = r.value.replace(n ? /\s+$/ : /[ \t]+$/, ""), r.leftStripped = r.value !== i, r.leftStripped
            }
        }

        var l = n(8)["default"];
        e.__esModule = !0;
        var u = n(6), c = l(u);
        r.prototype = new c["default"], r.prototype.Program = function (t) {
            var e = !this.options.ignoreStandalone, n = !this.isRootSeen;
            this.isRootSeen = !0;
            for (var r = t.body, l = 0, u = r.length; u > l; l++) {
                var c = r[l], p = this.accept(c);
                if (p) {
                    var h = i(r, l, n), f = a(r, l, n), d = p.openStandalone && h, m = p.closeStandalone && f, g = p.inlineStandalone && h && f;
                    p.close && o(r, l, !0), p.open && s(r, l, !0), e && g && (o(r, l), s(r, l) && "PartialStatement" === c.type && (c.indent = /([ \t]+$)/.exec(r[l - 1].original)[1])), e && d && (o((c.program || c.inverse).body), s(r, l)), e && m && (o(r, l), s((c.inverse || c.program).body))
                }
            }
            return t
        }, r.prototype.BlockStatement = r.prototype.DecoratorBlock = r.prototype.PartialBlockStatement = function (t) {
            this.accept(t.program), this.accept(t.inverse);
            var e = t.program || t.inverse, n = t.program && t.inverse, r = n, l = n;
            if (n && n.chained)for (r = n.body[0].program; l.chained;)l = l.body[l.body.length - 1].program;
            var u = {
                open: t.openStrip.open,
                close: t.closeStrip.close,
                openStandalone: a(e.body),
                closeStandalone: i((r || e).body)
            };
            if (t.openStrip.close && o(e.body, null, !0), n) {
                var c = t.inverseStrip;
                c.open && s(e.body, null, !0), c.close && o(r.body, null, !0), t.closeStrip.open && s(l.body, null, !0), !this.options.ignoreStandalone && i(e.body) && a(r.body) && (s(e.body), o(r.body))
            } else t.closeStrip.open && s(e.body, null, !0);
            return u
        }, r.prototype.Decorator = r.prototype.MustacheStatement = function (t) {
            return t.strip
        }, r.prototype.PartialStatement = r.prototype.CommentStatement = function (t) {
            var e = t.strip || {};
            return {inlineStandalone: !0, open: e.open, close: e.close}
        }, e["default"] = r, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        function r(t, e) {
            if (e = e.path ? e.path.original : e, t.path.original !== e) {
                var n = {loc: t.path.loc};
                throw new g["default"](t.path.original + " doesn't match " + e, n)
            }
        }

        function i(t, e) {
            this.source = t, this.start = {line: e.first_line, column: e.first_column}, this.end = {
                line: e.last_line,
                column: e.last_column
            }
        }

        function a(t) {
            return /^\[.*\]$/.test(t) ? t.substr(1, t.length - 2) : t
        }

        function o(t, e) {
            return {open: "~" === t.charAt(2), close: "~" === e.charAt(e.length - 3)}
        }

        function s(t) {
            return t.replace(/^\{\{~?\!-?-?/, "").replace(/-?-?~?\}\}$/, "")
        }

        function l(t, e, n) {
            n = this.locInfo(n);
            for (var r = t ? "@" : "", i = [], a = 0, o = "", s = 0, l = e.length; l > s; s++) {
                var u = e[s].part, c = e[s].original !== u;
                if (r += (e[s].separator || "") + u, c || ".." !== u && "." !== u && "this" !== u)i.push(u); else {
                    if (i.length > 0)throw new g["default"]("Invalid path: " + r, {loc: n});
                    ".." === u && (a++, o += "../")
                }
            }
            return {type: "PathExpression", data: t, depth: a, parts: i, original: r, loc: n}
        }

        function u(t, e, n, r, i, a) {
            var o = r.charAt(3) || r.charAt(2), s = "{" !== o && "&" !== o, l = /\*/.test(r);
            return {
                type: l ? "Decorator" : "MustacheStatement",
                path: t,
                params: e,
                hash: n,
                escaped: s,
                strip: i,
                loc: this.locInfo(a)
            }
        }

        function c(t, e, n, i) {
            r(t, n), i = this.locInfo(i);
            var a = {type: "Program", body: e, strip: {}, loc: i};
            return {
                type: "BlockStatement",
                path: t.path,
                params: t.params,
                hash: t.hash,
                program: a,
                openStrip: {},
                inverseStrip: {},
                closeStrip: {},
                loc: i
            }
        }

        function p(t, e, n, i, a, o) {
            i && i.path && r(t, i);
            var s = /\*/.test(t.open);
            e.blockParams = t.blockParams;
            var l = void 0, u = void 0;
            if (n) {
                if (s)throw new g["default"]("Unexpected inverse block on decorator", n);
                n.chain && (n.program.body[0].closeStrip = i.strip), u = n.strip, l = n.program
            }
            return a && (a = l, l = e, e = a), {
                type: s ? "DecoratorBlock" : "BlockStatement",
                path: t.path,
                params: t.params,
                hash: t.hash,
                program: e,
                inverse: l,
                openStrip: t.strip,
                inverseStrip: u,
                closeStrip: i && i.strip,
                loc: this.locInfo(o)
            }
        }

        function h(t, e) {
            if (!e && t.length) {
                var n = t[0].loc, r = t[t.length - 1].loc;
                n && r && (e = {
                    source: n.source,
                    start: {line: n.start.line, column: n.start.column},
                    end: {line: r.end.line, column: r.end.column}
                })
            }
            return {type: "Program", body: t, strip: {}, loc: e}
        }

        function f(t, e, n, i) {
            return r(t, n), {
                type: "PartialBlockStatement",
                name: t.path,
                params: t.params,
                hash: t.hash,
                program: e,
                openStrip: t.strip,
                closeStrip: n && n.strip,
                loc: this.locInfo(i)
            }
        }

        var d = n(8)["default"];
        e.__esModule = !0, e.SourceLocation = i, e.id = a, e.stripFlags = o, e.stripComment = s, e.preparePath = l, e.prepareMustache = u, e.prepareRawBlock = c, e.prepareBlock = p, e.prepareProgram = h, e.preparePartialBlock = f;
        var m = n(12), g = d(m)
    }, function (t, e, n) {
        "use strict";
        function r(t, e, n) {
            if (a.isArray(t)) {
                for (var r = [], i = 0, o = t.length; o > i; i++)r.push(e.wrap(t[i], n));
                return r
            }
            return "boolean" == typeof t || "number" == typeof t ? t + "" : t
        }

        function i(t) {
            this.srcFile = t, this.source = []
        }

        e.__esModule = !0;
        var a = n(13), o = void 0;
        try {
        } catch (s) {
        }
        o || (o = function (t, e, n, r) {
            this.src = "", r && this.add(r)
        }, o.prototype = {
            add: function (t) {
                a.isArray(t) && (t = t.join("")), this.src += t
            }, prepend: function (t) {
                a.isArray(t) && (t = t.join("")), this.src = t + this.src
            }, toStringWithSourceMap: function () {
                return {code: this.toString()}
            }, toString: function () {
                return this.src
            }
        }), i.prototype = {
            isEmpty: function () {
                return !this.source.length
            }, prepend: function (t, e) {
                this.source.unshift(this.wrap(t, e))
            }, push: function (t, e) {
                this.source.push(this.wrap(t, e))
            }, merge: function () {
                var t = this.empty();
                return this.each(function (e) {
                    t.add(["  ", e, "\n"])
                }), t
            }, each: function (t) {
                for (var e = 0, n = this.source.length; n > e; e++)t(this.source[e])
            }, empty: function () {
                var t = this.currentLocation || {start: {}};
                return new o(t.start.line, t.start.column, this.srcFile)
            }, wrap: function (t) {
                var e = arguments.length <= 1 || void 0 === arguments[1] ? this.currentLocation || {start: {}} : arguments[1];
                return t instanceof o ? t : (t = r(t, this, e), new o(e.start.line, e.start.column, this.srcFile, t))
            }, functionCall: function (t, e, n) {
                return n = this.generateList(n), this.wrap([t, e ? "." + e + "(" : "(", n, ")"])
            }, quotedString: function (t) {
                return '"' + (t + "").replace(/\\/g, "\\\\").replace(/"/g, '\\"').replace(/\n/g, "\\n").replace(/\r/g, "\\r").replace(/\u2028/g, "\\u2028").replace(/\u2029/g, "\\u2029") + '"'
            }, objectLiteral: function (t) {
                var e = [];
                for (var n in t)if (t.hasOwnProperty(n)) {
                    var i = r(t[n], this);
                    "undefined" !== i && e.push([this.quotedString(n), ":", i])
                }
                var a = this.generateList(e);
                return a.prepend("{"), a.add("}"), a
            }, generateList: function (t) {
                for (var e = this.empty(), n = 0, i = t.length; i > n; n++)n && e.add(","), e.add(r(t[n], this));
                return e
            }, generateArray: function (t) {
                var e = this.generateList(t);
                return e.prepend("["), e.add("]"), e
            }
        }, e["default"] = i, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        function r(t) {
            o["default"](t), l["default"](t), c["default"](t), h["default"](t), d["default"](t), g["default"](t), y["default"](t)
        }

        var i = n(8)["default"];
        e.__esModule = !0, e.registerDefaultHelpers = r;
        var a = n(22), o = i(a), s = n(23), l = i(s), u = n(24), c = i(u), p = n(25), h = i(p), f = n(26), d = i(f), m = n(27), g = i(m), v = n(28), y = i(v)
    }, function (t, e, n) {
        "use strict";
        function r(t) {
            o["default"](t)
        }

        var i = n(8)["default"];
        e.__esModule = !0, e.registerDefaultDecorators = r;
        var a = n(29), o = i(a)
    }, function (t, e) {
        "use strict";
        e.__esModule = !0;
        var n = {
            methodMap: ["debug", "info", "warn", "error"], level: "info", lookupLevel: function (t) {
                if ("string" == typeof t) {
                    var e = n.methodMap.indexOf(t.toLowerCase());
                    t = e >= 0 ? e : parseInt(t, 10)
                }
                return t
            }, log: function (t) {
                if (t = n.lookupLevel(t), "undefined" != typeof console && n.lookupLevel(n.level) <= t) {
                    var e = n.methodMap[t];
                    console[e] || (e = "log");
                    for (var r = arguments.length, i = Array(r > 1 ? r - 1 : 0), a = 1; r > a; a++)i[a - 1] = arguments[a];
                    console[e].apply(console, i)
                }
            }
        };
        e["default"] = n, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        e.__esModule = !0;
        var r = n(13);
        e["default"] = function (t) {
            t.registerHelper("blockHelperMissing", function (e, n) {
                var i = n.inverse, a = n.fn;
                if (e === !0)return a(this);
                if (e === !1 || null == e)return i(this);
                if (r.isArray(e))return e.length > 0 ? (n.ids && (n.ids = [n.name]), t.helpers.each(e, n)) : i(this);
                if (n.data && n.ids) {
                    var o = r.createFrame(n.data);
                    o.contextPath = r.appendContextPath(n.data.contextPath, n.name), n = {data: o}
                }
                return a(e, n)
            })
        }, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        var r = n(8)["default"];
        e.__esModule = !0;
        var i = n(13), a = n(12), o = r(a);
        e["default"] = function (t) {
            t.registerHelper("each", function (t, e) {
                function n(e, n, a) {
                    null != t[e] && (u && (u.key = e, u.index = n, u.first = 0 === n, u.last = !!a, c && (u.contextPath = c + e)), l += r(t[e], {
                        data: u,
                        blockParams: i.blockParams([t[e], e], [c + e, null])
                    }))
                }

                if (!e)throw new o["default"]("Must pass iterator to #each");
                var r = e.fn, a = e.inverse, s = 0, l = "", u = void 0, c = void 0;
                if (e.data && e.ids && (c = i.appendContextPath(e.data.contextPath, e.ids[0]) + "."), i.isFunction(t) && (t = t.call(this)), e.data && (u = i.createFrame(e.data)), t && "object" == typeof t)if (i.isArray(t))for (var p = t.length; p > s; s++)n(s, s, s === t.length - 1); else {
                    var h = void 0;
                    for (var f in t)t.hasOwnProperty(f) && (void 0 !== h && n(h, s - 1), h = f, s++);
                    void 0 !== h && n(h, s - 1, !0)
                }
                return 0 === s && (l = a(this)), l
            })
        }, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        var r = n(8)["default"];
        e.__esModule = !0;
        var i = n(12), a = r(i);
        e["default"] = function (t) {
            t.registerHelper("helperMissing", function () {
                if (1 === arguments.length)return void 0;
                throw new a["default"]('Missing helper: "' + arguments[arguments.length - 1].name + '"')
            })
        }, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        e.__esModule = !0;
        var r = n(13);
        e["default"] = function (t) {
            t.registerHelper("if", function (t, e) {
                return r.isFunction(t) && (t = t.call(this)), !e.hash.includeZero && !t || r.isEmpty(t) ? e.inverse(this) : e.fn(this)
            }), t.registerHelper("unless", function (e, n) {
                return t.helpers["if"].call(this, e, {fn: n.inverse, inverse: n.fn, hash: n.hash})
            })
        }, t.exports = e["default"]
    }, function (t, e) {
        "use strict";
        e.__esModule = !0, e["default"] = function (t) {
            t.registerHelper("log", function () {
                for (var e = [void 0], n = arguments[arguments.length - 1], r = 0; r < arguments.length - 1; r++)e.push(arguments[r]);
                var i = 1;
                null != n.hash.level ? i = n.hash.level : n.data && null != n.data.level && (i = n.data.level), e[0] = i, t.log.apply(t, e)
            })
        }, t.exports = e["default"]
    }, function (t, e) {
        "use strict";
        e.__esModule = !0, e["default"] = function (t) {
            t.registerHelper("lookup", function (t, e) {
                return t && t[e]
            })
        }, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        e.__esModule = !0;
        var r = n(13);
        e["default"] = function (t) {
            t.registerHelper("with", function (t, e) {
                r.isFunction(t) && (t = t.call(this));
                var n = e.fn;
                if (r.isEmpty(t))return e.inverse(this);
                var i = e.data;
                return e.data && e.ids && (i = r.createFrame(e.data), i.contextPath = r.appendContextPath(e.data.contextPath, e.ids[0])), n(t, {
                    data: i,
                    blockParams: r.blockParams([t], [i && i.contextPath])
                })
            })
        }, t.exports = e["default"]
    }, function (t, e, n) {
        "use strict";
        e.__esModule = !0;
        var r = n(13);
        e["default"] = function (t) {
            t.registerDecorator("inline", function (t, e, n, i) {
                var a = t;
                return e.partials || (e.partials = {}, a = function (i, a) {
                    var o = n.partials;
                    n.partials = r.extend({}, o, e.partials);
                    var s = t(i, a);
                    return n.partials = o, s
                }), e.partials[i.args[0]] = i.fn, a
            })
        }, t.exports = e["default"]
    }])
}), !function (t, e) {
    "object" == typeof exports && "object" == typeof module ? module.exports = e(require("jquery"), require("handlebars")) : "function" == typeof define && define.amd ? define("sf-tagpopup", ["jquery", "handlebars"], e) : "object" == typeof exports ? exports.tagpopup = e(require("jquery"), require("handlebars")) : t.tagpopup = e(t.jquery, t.handlebars)
}(this, function (t, e) {
    return function (t) {
        function e(r) {
            if (n[r])return n[r].exports;
            var i = n[r] = {exports: {}, id: r, loaded: !1};
            return t[r].call(i.exports, i, i.exports, e), i.loaded = !0, i.exports
        }

        var n = {};
        return e.m = t, e.c = n, e.p = "", e(0)
    }([function (t, e, n) {
        var r = n(1);
        t.exports = r
    }, function (t, e, n) {
        function r(t, e, n) {
            t.popover({
                template: '<div class="popover tag-popup" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
                content: "loading...",
                html: !0,
                placement: "top",
                trigger: "hover",
                container: "body",
                delay: {show: 1e3, hide: 1e3}
            }, n), t.one("show.bs.popover", function () {
                var n = $(this).data("bs.popover"), r = n.tip();
                r.on("mouseenter", function () {
                    var t = n.options.animation;
                    n.options.animation = !1, n.show.call(n), n.options.animation = t
                }), r.on("mouseleave", n.leave.bind(n)), new i($.extend({}, {
                    url: "/api/tag/" + $(this).data("id"),
                    template: '<p>{{excerpt}}</p> <div class="operation"> <a href="{{url}}">查看</a> <span class="text-muted">&middot;</span> <a href="{{editUrl}}">编辑</a> <span class="text-muted">&middot;</span> <a href="/feeds/tag/{{name}}">订阅</a> <div class="pull-right"> <span class="text-muted followers">{{followers}} 人</span> <button class="btn btn-default btn-xs tagfollow {{isFollowedClass}}" data-id="{{id}}">{{isFollowedBtn}}</button> </div> </div>',
                    locator: "data",
                    handleData: function (t) {
                        0 !== t.data.length && (t.data.excerpt || (t.data.excerpt = "目前还没有关于这个标签的解释"), t.data.isFollowed ? (t.data.isFollowedClass = "active", t.data.isFollowedBtn = "已关注") : (t.data.isFollowedClass = "", t.data.isFollowedBtn = "加关注"))
                    },
                    callback: function (t) {
                        n.options.content = t;
                        var e = n.options.animation;
                        n.options.animation = !1, n.show(), n.options.animation = e, $follow = r.find("button"), a({
                            element: $follow,
                            url: "/api/tag",
                            successFn: function (t) {
                                r.find(".followers").text(t.data + "人")
                            }
                        })
                    }
                }, e)), t.on("shown.bs.popover", function () {
                    var t = $(this).data("bs.popover").tip().find("button");
                    a({
                        element: t, url: "/api/tag", successFn: function (t) {
                            r.find(".followers").text(t.data + "人")
                        }
                    })
                })
            })
        }

        var i = n(2), a = n(3);
        $.fn.tagpopup = function (t, e) {
            r(this, t, e)
        }, t.exports = r
    }, function (t, e, n) {
        function r(t) {
            this.init(t), r.templates.push(this)
        }

        function i(t) {
            return "[object Function]" === Object.prototype.toString.call(t)
        }

        function a(t) {
            return "[object String]" === Object.prototype.toString.call(t)
        }

        function o(t, e) {
            if (t) {
                if (s.isFunction(t))return t.call(this, e);
                if (!s.isArray(e) && a(t)) {
                    for (var n = t.split("."), r = e; n.length;) {
                        var i = n.shift();
                        if (!r[i])break;
                        r = r[i]
                    }
                    return r
                }
            }
            return e
        }

        var s = n(4), l = n(5)["default"], u = [];
        r.DEFAULT = {
            url: null,
            template: null,
            model: null,
            locator: null,
            handleData: null,
            callback: null
        }, r.prototype = {
            constructor: r, templateHelpers: null, templatePartials: null, init: function (t) {
                this._initAttrs(t), this.get("model") ? this._getLocalModel() : this._getRemoteModel()
            }, _initAttrs: function (t) {
                this.attrs = s.extend({}, r.DEFAULT, t)
            }, _getRemoteModel: function () {
                var t = this, e = this.get("url");
                s.ajax({
                    url: e, async: !0, dataType: "json", success: function (e) {
                        t._handleData(e)
                    }
                })
            }, _getLocalModel: function () {
                var t = this.get("model");
                this._handleData(t)
            }, _setModel: function (t) {
                var e = this.get("locator");
                this.set("model", o(e, t))
            }, _generate: function () {
                var t = this.get("template"), e = this.get("model");
                return this.compile(t, e)
            }, _handleData: function (t) {
                var e = this.get("callback"), n = this.get("handleData");
                n && n(t), this._setModel(t), e && e(this._generate())
            }, compile: function (t, e) {
                if (e.toJSON && (e = e.toJSON()), i(t))return t(e, {
                    helpers: this.templateHelpers,
                    partials: precompile(this.templatePartials)
                });
                var n, r, a = this.templateHelpers, o = this.templatePartials;
                if (a)for (n in a)a.hasOwnProperty(n) && l.registerHelper(n, a[n]);
                if (o)for (r in o)o.hasOwnProperty(r) && l.registerPartial(r, o[r]);
                var s = u[t];
                s || (s = u[t] = l.compile(t));
                var c = s(e);
                if (a)for (n in a)a.hasOwnProperty(n) && delete l.helpers[n];
                if (o)for (r in o)o.hasOwnProperty(r) && delete l.partials[r];
                return c
            }, destroy: function () {
                for (var t in this)this.hasOwnProperty(t) && delete this[t]
            }, get: function (t) {
                return this.attrs[t]
            }, set: function (t, e) {
                return this.attrs[t] = e
            }
        }, t.exports = r, r.templates = [], r.destroy = function () {
            for (var t = 0, e = r.templates.length; e > t; t++)r.templates[t].destroy()
        }
    }, function (t, e, n) {
        function r(t) {
            var e, n;
            e = a.extend({
                element: null,
                event: "click",
                url: null,
                toggleClass: "active",
                loadText: "加载中",
                unActiveText: "加关注",
                activeText: "已关注",
                "do": "follow",
                successFn: function () {
                },
                failFn: function () {
                }
            }, t), n = t.element, i(n), n.on(e.event, function () {
                n.hasClass(e.toggleClass) ? n.trigger("unactive", function () {
                    n.text(e.unActiveText).removeClass(e.toggleClass).attr("disabled", "disabled"), a.post(e.url + "/" + e.element.data("id") + "/" + e["do"] + "/cancel", function (t) {
                        0 === t.status ? e.successFn.call(this, t) : (n.text(e.activeText), e.failFn.call(this, t)), n.removeAttr("disabled")
                    })
                }) : n.trigger("active", function () {
                    n.text(e.activeText).addClass(e.toggleClass).attr("disabled", "disabled"), a.post(e.url + "/" + e.element.data("id") + "/" + e["do"], function (t) {
                        0 === t.status ? e.successFn.call(this, t) : (n.text(e.unActiveText), e.failFn.call(this, t)), n.removeAttr("disabled")
                    })
                })
            })
        }

        function i(t) {
            t.data("toggle", "false"), t.data("toggle") && (t.on("active", function (e, n) {
                t.data("toggle", "true"), n.call(this)
            }), t.on("unactive", function (e, n) {
                t.data("toggle", "true"), n.call(this)
            }))
        }

        var a = n(4);
        t.exports = r
    }, function (e) {
        e.exports = t
    }, function (t) {
        t.exports = e
    }])
}), requirejs(["main", "jquery_tmpl", "sf-tagpopup"], function (t) {
    "use strict";
    $(".tagPopup .tag").tagpopup(), $("#goAsk").click(function (e) {
        t.userId || (e.preventDefault(), t.login())
    }), t.userId && !$("#interestTab").hasClass("active") && $.get("/api/tag/following", function (t) {
        var e;
        e = [], t.data.rows.forEach(function (t) {
            e.push(parseInt(t.id))
        }), $(".stream-list__item").each(function () {
            var t, n;
            n = [], t = $(this), $(this).find(".tag").each(function () {
                n.push($(this).data("id"))
            }), n.forEach(function (n) {
                -1 !== e.indexOf(n) && t.addClass("highlight")
            })
        })
    })
}), define("qa_index", function () {
});