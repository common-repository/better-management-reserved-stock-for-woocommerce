(function( $ ) {
	'use strict';

	$( document ).ready(function() {
		
		var data = new FormData();
		data.append( 'action', 'prefix_ajax_uqp' );
		data.append( 'nonce', wp_ajax.nonce );
		data.append( 'product_id', wp_ajax.product_id );
		
		var qty_max = $( 'input[name="quantity"]' ).attr( 'max' );
		
		jQuery.ajax({
			url: wp_ajax.ajax_url,
			type: 'POST',
			data: data,
			cache: false,
			processData: false,
			contentType: false,
			success: function (response) {
				if ( response !== qty_max ) {
					$('input[name="quantity"]').attr( 'max', response );
				}
			},
			error: function () {
				console.log('error');
			}
		}); 

	});

})( jQuery );