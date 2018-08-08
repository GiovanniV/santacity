(function ($, Drupal) {
	Drupal.behaviors.myBehavior = {
		attach: function (context, settings) {
			alert(111);
			
		}
	};
})(jQuery, Drupal);