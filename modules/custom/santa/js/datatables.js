(function ($, Drupal) {
	Drupal.behaviors.santadatatables = {
		attach: function (context, settings) {
			$(document).ready(function(){
				// Setup - add a text input to each footer cell
				$('#xml-preview-datatables tfoot td', context).once().each( function () {
					var title = $(this).text();
					var serach = $(this).html( '<input type="text" placeholder="Filter '+title+'" />' );
					$(serach).appendTo(this).keyup(function(){table.fnFilter($(this).val(),i)})
				});

				// DataTable
				var table = $('#xml-preview-datatables', context).once().DataTable({
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"bDestroy": true,
				});


				
			});
		
		}
	};
})(jQuery, Drupal);