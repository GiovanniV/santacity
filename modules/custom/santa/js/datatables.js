(function ($, Drupal) {
	Drupal.behaviors.santadatatables = {
		attach: function (context, settings) {
			// Setup - add a text input to each footer cell
      $('#xml-preview tfoot tr').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
      });

      // DataTable
      var otable = $('#xml-preview').DataTable();

      // Apply the search
      otable.columns().every( function () {

        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that.search(this.value).draw();
          }
        });
      });
		}
	};
})(jQuery, Drupal);