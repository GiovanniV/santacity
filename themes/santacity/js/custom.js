(function ($, Drupal) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {
			
			/** Set Header Top **/
			var admin_toolbar_height = $('.toolbar-menu-administration .toolbar-menu').outerHeight();
			var toolbar_height = $('#toolbar-bar').outerHeight();
			var header_height = admin_toolbar_height + toolbar_height;
			$('header').attr('top', header_height + 'px');
			
			$(document).ready(function(){
				$(".nav li.expanded").hover(
					function(){
						$(this).addClass("open");
					},function(){
						$(this).removeClass("open");
					}
				);
			});
			
			
		
		}
	};
})(jQuery, Drupal);