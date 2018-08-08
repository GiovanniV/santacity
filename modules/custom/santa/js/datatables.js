(function ($, Drupal) {
	Drupal.behaviors.santadatatables = {
		attach: function (context, settings) {
			$(document).ready(function(){
				
				// DataTable
				var table = $('#xml-preview-datatables').DataTable({
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"bDestroy": true,
				});
				
				// Setup - add a text input to each footer cell
				$('#xml-preview-datatables tfoot td', context).once().each( function (i) {
					var title = $(this).text();
					var serach = $(this).html( '<input type="text" placeholder="Filter '+title+'" />' );
					$(serach).appendTo(this).keyup(function(){table.fnFilter($(this).val(),i)})
				});


				
			});
		
		}
	};
})(jQuery, Drupal);