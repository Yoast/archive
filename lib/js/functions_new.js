jQuery(document).ready(function ($) {
	$("#tabs li").click(function () {
		var what = $(this).attr('id') + '-content';
		$(".tab").hide();
		$("#tabs_bottom").attr('class', $(this).attr('id').replace('tab-', ''));
		$(".tab#" + what).show();
		$("#tabs li").removeClass("active");
		$(this).addClass("active");
	});

	if (window.location.hash != '') {
		$("#tabs li").removeClass('active');
		var active = '#tab-' + window.location.hash.replace('#', '');
		$(active).addClass('active');
	}
	var active = $("#tabs li.active").attr('id').replace('tab-', '');
	$("#tabs_bottom").attr('class', active);
	$(".tab").hide();
	$("#tab-" + active + "-content").show();

	$(".plugin_box .info").click(function () {
		var par = $(this).parent().parent().attr('id');
		$(".plugin_box li .description").hide();
		$("#" + par + " .description").toggle();
		return false;
	});
});