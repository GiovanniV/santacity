/**
 * @file
 * JavaScript behaviors for element help text (tooltip).
 */

(function ($, Drupal) {

  'use strict';

  // @see http://api.jqueryui.com/tooltip/
  Drupal.webform = Drupal.webform || {};
  Drupal.webform.elementHelpIcon = Drupal.webform.elementHelpIcon || {};
  Drupal.webform.elementHelpIcon.options = Drupal.webform.elementHelpIcon.options || {
    position: { my: 'left+5 top+5', at: 'left bottom', collision: 'flipfit'},
    tooltipClass: 'webform-element-help--tooltip',
    // @see https://stackoverflow.com/questions/18231315/jquery-ui-tooltip-html-with-links
    show: {delay: 100},
    close: function (event, ui) {
      ui.tooltip.hover(
        function () {
          $(this).stop(true).fadeTo(400, 1);
        },
        function () {
          $(this).fadeOut("400", function () {
            $(this).remove();
          })
        });
    }
  };

  /**
   * Element help icon.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformElementHelpIcon = {
    attach: function (context) {
      $(context).find('.webform-element-help').once('webform-element-help').each(function () {
        var $link = $(this);

        var options = $.extend({
            // Use 'data-webform-help' attribute which can include HTML markup.
            content: $link.attr('data-webform-help'),
            items: '[data-webform-help]'
          }, Drupal.webform.elementHelpIcon.options);

        $link.tooltip(options)
          .on('click', function (event) {
            // Prevent click from toggling <label>s wrapped around help.
            event.preventDefault();
          });
      });
    }
  };

})(jQuery, Drupal);
