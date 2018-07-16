(function ($, Drupal) {
	Drupal.behaviors.myBehavior = {
		attach: function (context, settings) {
			// $(window).load(function(){
				$('#carousel').flexslider({
					animation: "slide",
					controlNav: false,
					animationLoop: false,
					slideshow: false,
					itemWidth: 210,
					itemMargin: 5,
					asNavFor: '#slider'
				});

				$('#slider').flexslider({
					animation: "slide",
					controlNav: false,
					animationLoop: false,
					slideshow: false,
					sync: "#carousel",
					start: function(slider){
						$('body').removeClass('loading');
					}
				});
			//});
		}
	};
})(jQuery, Drupal);