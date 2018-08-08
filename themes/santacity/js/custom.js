(function ($, Drupal) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {
			
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