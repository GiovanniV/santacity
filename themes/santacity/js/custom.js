(function ($, Drupal) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {
			
			/** Set Header Top **/
			var admin_toolbar_outer_height = $('.toolbar-menu-administration .toolbar-menu').outerHeight();
			var toolbar_outer_height = $('#toolbar-bar').outerHeight();
			var header_height = admin_toolbar_height + toolbar_height;
			$('header').css('top', header_height + 'px');
			
			/** Set Page Heading **/
			var header_height = $('header').outerHeight();
			var heading_height = admin_toolbar_height + toolbar_height + header_height;
			$('.region-page-heading').css('top', heading_height + 'px');
			
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