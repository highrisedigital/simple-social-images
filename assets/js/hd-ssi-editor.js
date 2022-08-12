( function( $ ) {

	// hide the deploy spinner.
	$( '.hd-ssi-spinner' ).hide();

	// when the deploy button is clicked.
	$( document ).on( 'click', '.generate-ssi-image-button', function(e) {

		// prevent the button taking its normal action.
		e.preventDefault();

		// show the deploy spinner.
		$( '.hd-ssi-spinner' ).show();

		// get the deploy endpoint url.
		var generateEndpointUrl = $( this ).data( 'endpoint-url' );

		// do the ajax request for job search.
		$.ajax({

			type: 'get',
			url: generateEndpointUrl,

			// what happens on success.
			success: function( response, status, request ) {

				// if the response is a success.
				if ( false !== response.success ) {

					// set the image src to the response.
					$( '.ssi-image' ).attr( 'src', response.url );

					// change the media id in the delete button.
					$( '.delete-ssi-image-button' ).data( 'media-id', response.id );

					// remove the hidden class for the delete button.
					$( '.delete-ssi-image-button' ).removeClass( 'ssi-hidden' );

					// add the hidden class for the add button.
					$( '.generate-ssi-image-button' ).addClass( 'ssi-hidden' );

				} else {

					$( '.hd-ssi-error' ).remove();
					$( '.hd-ssi-spinner' ).before( '<p class="hd-ssi-error">' + response.error + '</p>' );

				}

				// hide the deploy spinner.
				$( '.hd-ssi-spinner' ).hide();

			},

			/* what happens on success */
			error: function( response ) {

				// hide the deploy spinner.
				$( '.hd-ssi-spinner' ).hide();

			}

		});

	});

	// when the deploy button is clicked.
	$( document ).on( 'click', '.delete-ssi-image-button', function(e) {

		// prevent the button taking its normal action.
		e.preventDefault();

		// show the deploy spinner.
		$( '.hd-ssi-spinner' ).show();

		// get the deploy endpoint url.
		var deleteEndpoint = $( this ).data( 'endpoint-url' );

		// get the attachment to delete.
		var deleteMediaId = $( this ).data( 'media-id' );

		// get the placeholder image url.
		var placeholderImag = $( this ).data( 'placeholder-img' );

		// do the ajax request for job search.
		$.ajax({

			url: deleteEndpoint + deleteMediaId + '/?force=true',
			method: 'DELETE',
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
			},

			// what happens on success.
			success: function( response, status, request ) {

				// set the image src to the response.
				$( '.ssi-image' ).attr( 'src', placeholderImag );

				// set the media id data.
				$( '.ssi-image' ).data( 'media-id', deleteMediaId );

				// remove the hidden class for the delete button.
				$( '.delete-ssi-image-button' ).addClass( 'ssi-hidden' );

				// remove the hidden class for the generate button.
				$( '.generate-ssi-image-button' ).removeClass( 'ssi-hidden' );

				// hide the deploy spinner.
				$( '.hd-ssi-spinner' ).hide();

			},

			/* what happens on success */
			error: function( response ) {

				// hide the deploy spinner.
				$( '.hd-ssi-spinner' ).hide();

			}

		});

	});

} )( jQuery );