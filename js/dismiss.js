(function($) {
	$('a.ShowOnce-Dismiss').each(function(){
		var link = $(this);
		var showonce = link.parents('.ShowOnce');
		var post = link.attr('data-post');
		if( post ) {
			link.bind('click', function(){
				link.hide();
				jQuery.ajax({
					type: 'POST',
					url: URL_Ajax,
					data: 'action=showonce_dismiss&post='+post,
					success: function(msg) {
						if( msg=='ok' ) {
							showonce.fadeOut(500);
						} else {
							link.show();
						}
					}
				});
				return false;
			});
		}
	});
})(jQuery);