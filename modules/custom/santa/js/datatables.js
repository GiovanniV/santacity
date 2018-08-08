(function ($, Drupal) {
	Drupal.behaviors.myBehavior = {
		attach: function (context, settings) {
			alert(111);
			$('#xml-datatable').DataTable();
			
			$('#xml-datatable tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
			});
	 
			// DataTable
			var table = $('#xml-datatable').DataTable();
	 
			// Apply the search
			table.columns().every( function () {
				var that = this;
				
				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that.search( this.value ).draw();
					}
				});
			});
		}
	};
})(jQuery, Drupal);