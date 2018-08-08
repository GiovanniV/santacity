(function ($, Drupal) {
	Drupal.behaviors.santadatatables = {
		attach: function (context, settings) {
			$(document).ready(function(){
				// Setup - add a text input to each footer cell
				$('#xml-preview tfoot td').each( function () {
					var title = $(this).text();
					alert(title);
					$(this).html( '<input type="text" placeholder="Filter '+title+'" />' );
				});

				// DataTable
				var table = $('#xml-preview').DataTable({
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"bDestroy": true,
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