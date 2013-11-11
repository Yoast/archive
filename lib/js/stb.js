// jQuery Cookie 1.3.1
(function (e) {
	if (typeof define === "function" && define.amd) {
		define(["jquery"], e)
	} else {
		e(jQuery)
	}
})(function (e) {
	function n(e) {
		return e
	}

	function r(e) {
		return decodeURIComponent(e.replace(t, " "))
	}

	function i(e) {
		if (e.indexOf('"') === 0) {
			e = e.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\")
		}
		try {
			return s.json ? JSON.parse(e) : e
		} catch (t) {
		}
	}

	var t = /\+/g;
	var s = e.cookie = function (t, o, u) {
		if (o !== undefined) {
			u = e.extend({}, s.defaults, u);
			if (typeof u.expires === "number") {
				var a = u.expires, f = u.expires = new Date;
				f.setDate(f.getDate() + a)
			}
			o = s.json ? JSON.stringify(o) : String(o);
			return document.cookie = [s.raw ? t : encodeURIComponent(t), "=", s.raw ? o : encodeURIComponent(o), u.expires ? "; expires=" + u.expires.toUTCString() : "", u.path ? "; path=" + u.path : "", u.domain ? "; domain=" + u.domain : "", u.secure ? "; secure" : ""].join("")
		}
		var l = s.raw ? n : r;
		var c = document.cookie.split("; ");
		var h = t ? undefined : {};
		for (var p = 0, d = c.length; p < d; p++) {
			var v = c[p].split("=");
			var m = l(v.shift());
			var g = l(v.join("="));
			if (t && t === m) {
				h = i(g);
				break
			}
			if (!t) {
				h[m] = i(g)
			}
		}
		return h
	};
	s.defaults = {};
	e.removeCookie = function (t, n) {
		if (e.cookie(t) !== undefined) {
			e.cookie(t, "", e.extend({}, n, {expires: -1}));
			return true
		}
		return false
	}
});

jQuery(document).ready(function () {
	if (jQuery.cookie('nopopup') != 'true') {
		(function (stb_helpers) {
			stb_helpers.showBox = function (scrolled, triggerHeight, y) {
				if (stb.isMobile()) return false;
				if (stb.stbElement == '') {
					if (scrolled >= triggerHeight) {
						return true;
					}
				}
				else {
					if (stb.boxOffset < (stb.windowheight + y)) {
						return true;
					}
				}
				return false;
			};
			stb_helpers.isMobile = function () {
				if (navigator.userAgent.match(/Android/i)
						|| navigator.userAgent.match(/webOS/i)
						|| navigator.userAgent.match(/iPhone/i)
						|| navigator.userAgent.match(/iPod/i)
						|| navigator.userAgent.match(/BlackBerry/i)
						) {
					return true;
				}
				else return false;
			}
		})(stb);

		jQuery("#closebox").click(function () {
			jQuery('#scrolltriggered').stop(true, true).animate({ 'bottom': '-210px' }, 500, function () {
				jQuery('#scrolltriggered').hide();
				stb.hascolsed = true;
				jQuery.cookie('nopopup', 'true', { expires: stb.cookieLife, path: '/' });
			});
			return false;
		});

		stb.windowheight = jQuery(window).height();
		stb.totalheight = jQuery(document).height();
		stb.boxOffset = '';
		if (jQuery('#yst_related').length > 0) {
			stb.boxOffset = jQuery('#yst_related').offset().top;
		}
		jQuery(window).resize(function () {
			stb.windowheight = jQuery(window).height();
			stb.totalheight = jQuery(document).height();
		});

		jQuery(window).scroll(function () {
			stb.y = jQuery(window).scrollTop();
			stb.boxHeight = jQuery('#scrolltriggered').outerHeight();
			stb.scrolled = parseInt((stb.y + stb.windowheight) / stb.totalheight * 100);


			if (stb.showBox(stb.scrolled, stb.triggerHeight, stb.y) && jQuery('#scrolltriggered').is(":hidden") && stb.hascolsed != true) {
				jQuery('#scrolltriggered').show();
				jQuery('#scrolltriggered').stop(true, true).animate({ 'bottom': '10px' }, 500, function () {
				});
			}
			else if (!stb.showBox(stb.scrolled, stb.triggerHeight, stb.y) && jQuery('#scrolltriggered').is(":visible") && jQuery('#scrolltriggered:animated').length < 1) {
				jQuery('#scrolltriggered').stop(true, true).animate({ 'bottom': -stb.boxHeight }, 500, function () {
					jQuery('#scrolltriggered').hide();
				});
			}
		});
	} else {
		jQuery('#scrolltriggered').hide();
	}
});