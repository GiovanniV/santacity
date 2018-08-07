(function ($, Drupal, drupalSettings) {
  /**
   * Drupal behavior for datatables.
   *
   * @type {{attach: Function}}
   */
  Drupal.behaviors.datatables = {
    attach: function (context) {
      $.each(drupalSettings.datatables || [], function (view_dom_id) {
        // @todo: clarify if we need to wrap this in a 'once'-call as the
        // factory checks accordingly.
        $('.js-view-dom-id-' + view_dom_id, context).once('datatables').each(function () {
          Drupal.dataTables.Datatable.createInstance('.js-view-dom-id-' + view_dom_id, drupalSettings.datatables[view_dom_id]);
        });
      });
    }
  };

  /**
   * Theme an expandable hidden row.
   *
   * @param object
   *   The datatable object.
   * @param array
   *   The row array for which the hidden row is being displayed.
   * @return
   *   The formatted text (html).
   */
  Drupal.theme.datatablesExpandableRow = function(settings, row) {
    var output = '<table style="padding-left: 50px">';

    $.each(row, function(index) {
      if (settings.aoColumns[index].bVisible == false) {
        output += '<tr><td>' + settings.aoColumnHeaders[index].content + '</td><td style="text-align: left">' + row[index] + '</td></tr>';
      }
    });

    output += '</table>';
    return output;
  };
})(jQuery, Drupal, drupalSettings);
