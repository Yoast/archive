(function (doc) {

	var addEvent = 'addEventListener',
			type = 'gesturestart',
			qsa = 'querySelectorAll',
			scales = [1, 1],
			meta = qsa in doc ? doc[qsa]('meta[name=viewport]') : [];

	function fix() {
		meta.content = 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1];
		doc.removeEventListener(type, fix, true);
	}

	if ((meta = meta[meta.length - 1]) && addEvent in doc) {
		fix();
		scales = [.25, 1.6];
		doc[addEvent](type, fix, true);
	}

}(document));

if (window.devicePixelRatio > 1.5) {
	jQuery(document).ready(function ($) {
		$('img.avatar-100').each(function () {
			$(this).attr('src', $(this).attr('src').replace('s=100', 's=200'));
		});
		$('img.avatar-60').each(function () {
			$(this).attr('src', $(this).attr('src').replace('s=60', 's=120'));
		});
		$('img.be_home_retina').each(function () {
			$(this).attr('src', $(this).attr('src').replace('203x137', '406x274'));
		});
		$('img.hires').each(function () {
			$(this).attr('src', $(this).attr('src').replace(/\.(png|jpg|gif)/, "_x2.$1"));
		});
	});
}