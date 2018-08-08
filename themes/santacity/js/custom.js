(function ($, Drupal) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {
			
			$(document).ready(function(){
				alert(1111);
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