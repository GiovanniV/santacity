(function ($, Drupal) {
	Drupal.behaviors.santadatatables = {
		attach: function (context, settings) {
			$(document).ready(function(){
				// Setup - add a text input to each footer cell
				$('#xml-preview-datatables tfoot td', context).once().each( function () {
					var title = $(this).text();
					$(this).html( '<input type="text" placeholder="Filter '+title+'" />' );
				});

				// DataTable
				var table = $('#xml-preview-datatables').DataTable({
					"dom": '<"top">t<"bottom"p><"clear">',
					"bLengthChange": false,
					"bInfo": false,
					"bDestroy": true,
					"orderCellsTop": true,
				});
				
				// Apply the search
				table.columns().every( function () {
					var that = this;
					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that.search(this.value).draw();
						}
					});
				});

			});
		
		}
	};
})(jQuery, Drupal);