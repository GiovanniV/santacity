(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {

			/** Set Header Top **/
			var admin_toolbar_outer_height = $('.toolbar-menu-administration .toolbar-menu').outerHeight();
			var toolbar_outer_height = $('#toolbar-bar').outerHeight();
			var header_height = admin_toolbar_outer_height + toolbar_outer_height;
			$('header').css('top', header_height + 'px');

			/** Set Page Heading **/
			var header_outer_height = $('header').outerHeight();
			var heading_height = admin_toolbar_outer_height + toolbar_outer_height + header_outer_height;
			// $('.region-page-heading').css('top', heading_height + 'px');

			$('.node-preview-container').css('margin-top', '50px');
			$(document).ready(function(){
				$(".nav li.expanded").hover(
					function(){
						$(this).addClass("open");
					},function(){
						$(this).removeClass("open");
					}
				);

				$('body').addClass(drupalSettings.tranlateClass);

				try {
					doGTranslate('en|' + drupalSettings.language);
					if(drupalSettings.language == 'en') {
						$('html').addClass('notranslate');
					}
				}
				catch(e) {
					//callUndefinedFunctionCatcher(e.arguments);
				}


				// Permit
				$('.xml-listing-form-container input').click(function() {
					$('.xml-listing-form-container input').not(this).attr("checked", false);
				});

				$('.gm-style img').mouseenter(function() {
					$(this).attr('src', '/themes/santacity/images/orange.png');
				});

				$('.gm-style img').mouseleave(function() {
					$(this).attr('src', '/themes/santacity/images/orange.png');
				});

				$('#block-mainnavigation li').click(function(){
					//$('#block-mainnavigation li').removeClass('open');
					if($(this).hasClass('dropdown-submenu')) {
						console.log($(this));
						$(this).addClass('open');
					}
				});
				setTimeout(function(){
					// $(".item-wrapper .city_box").css({'height':$(".item-wrapper .city_box").height()});
					$(".item-wrapper .city_text_body").css({'height':$(".item-wrapper .city_text_body").height()});
				}, 500);
				// setTimeout(function(){ $('.city_text a.more').css({"position": "absolute", "bottom": "20px"}); }, 500);
			});


		}
	};
})(jQuery, Drupal, drupalSettings);