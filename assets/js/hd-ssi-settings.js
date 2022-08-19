(function( $ ) {

	// add mutation observer function
	var observeDOM = (function(){
		var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
		
		return function( obj, callback ){
			if( !obj || obj.nodeType !== 1 ) return; 
		
			if( MutationObserver ){
			// define a new observer
			var mutationObserver = new MutationObserver(callback)
		
			// have the observer observe foo for changes in children
			mutationObserver.observe( obj, { childList:true, subtree:true })
			return mutationObserver
			}
			
			// browser support fallback
			else if( window.addEventListener ){
			obj.addEventListener('DOMNodeInserted', callback, false)
			obj.addEventListener('DOMNodeRemoved', callback, false)
			}
		}
	})();

	// Add Color Picker to all inputs that have .hd-ssi-input--color-picker
	$( function() {
		$( '.hd-ssi-input--color-picker' ).wpColorPicker();
	});

	$('body').on('click', '.hd-ssi-image-button', function(e) {
        e.preventDefault();
		
		// get the previous input.
		var inputID = $( this ).prev();
		var parentWrapper = inputID.prev();

        hd_ssi_image_uploader = wp.media({
            title: 'Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {

			// remove current logo
			$( '.hd-ssi-image' ).remove();
			$( '.hd-ssi-image--remove' ).remove();

			var attachment = hd_ssi_image_uploader.state().get('selection').first().toJSON();
			$( inputID ).val(attachment.id);

			var logo = $('<img class="hd-ssi-image">');
			logo.attr( 'src', attachment.sizes.full.url );
			logo.attr( 'id', parentWrapper.data('input-id') );

			var removeSpan = $('<span class="dashicons dashicons-no hd-ssi-image--remove"></span>');
			removeSpan.attr( 'data-input-id', parentWrapper.data('input-id') );

			parentWrapper.prepend( logo );
			logo.after( removeSpan );

        })
        .open();
    });

	$('body').on('click', '.hd-ssi-image--remove', function(e) {

		// get the previous input.
		var img = $( this ).prev();
		
		$( img ).remove();

		// set the hidden input to have no value.
		$( '#' + $( this ).data( 'input-id' ) ).val('');

		// remove this button.
		$( this ).remove();

	});

	$('body').on('click', '.hd-ssi-gallery-button', function(e) {

        e.preventDefault();

		// get the current values.
		currentValue = $( this ).prev().val();

		// if current value is currently empty.
		if ( currentValue === '' ) {
			currentValues = [];
		} else {
			currentValues = currentValue.split( ',' );
		}

		// get the current gallery images stored.
		var galleryImageIds = $( this ).prev();

		// get the gallery wrapper element.
		var galleryWrapper = galleryImageIds.prev();

		hd_ssi_gallery_uploader = wp.media({
            title: 'Image',
            button: {
                text: 'Use this image'
            },
            multiple: true
        }).on('select', function() {
			
			var attachments = hd_ssi_gallery_uploader.state().get('selection').toJSON();

			// loop through each of the attachments selected.
			$.each( attachments , function( index, val ) {

				// if this attachment id is not already in the current values array.
				if ( currentValues.indexOf( val.id + '' ) !== 0 ) {

					// add attachment ID to the array.
					currentValues.push( val.id );

					// output a new figure and image element on the page.

					// create a new figure element.
					imageFigure = $( '<figure class="hd-ssi-gallery-item"></figure>' );
					
					// create the removal span.
					removeSpan = $( '<span class="dashicons dashicons-no hd-ssi-gallery--remove"></span>' );
					removeSpan.attr( 'data-image-id', val.id );
					
					// add the span for removing.
					$( imageFigure ).prepend( removeSpan );
					
					imageEl = $( '<img class="hd-ssi-gallery-image">' )
					imageEl.attr( 'src', val.sizes.thumbnail.url );

					// add a new image to the figure.
					$( imageFigure ).prepend( imageEl );

					// append to the gallery wrapper.
					$( galleryWrapper ).prepend( imageFigure );


				}

			});

			// convert the current values array to a new values string.
			newValues = currentValues.toString();

			// set the input to the new current values.
			$( galleryImageIds ).val( newValues );
			
        })
        .open();

	});

	$('body').on('click', '.hd-ssi-gallery--remove', function(e) {
		
		// get the previous input.
		var img = $( this ).prev();
		var figure = $( img ).parent();

		// remove this image.
		figure.remove();

		// get the current value of the background images input.
		// NEED TO FIND THIS DYNAMICALLY BASED ON WHERE WE ARE ON CLICK!
		var currentImages = $( '#hd_ssi_background_images-input').val();
		var currentImagesArray = currentImages.split( ',' );

		var imageID = $( this ).data( 'image-id' );

		// find the key of this image id in the current images array.
		var key = currentImagesArray.indexOf( imageID + '' );
		
		// remove the imageID from the current images array.
		currentImagesArray.splice( key, 1 );

		// NEED TO FIND THIS DYNAMICALLY BASED ON WHERE WE ARE ON CLICK!
		$( '.hd-ssi-input--gallery' ).val( currentImagesArray );		

	});

	/* Live preview settings */

	/* Template choice */
	var templateChoice = document.querySelector("#hd_ssi_template");

	if ( templateChoice ) {

		templateChoiceInitialValue = templateChoice.value;

		templateChoice.addEventListener("change", function() {

			templateChangeNotice = document.getElementById('ssi-template--template-change-notice');

			if( templateChangeNotice ) {
			 	
				templateChangeNotice.remove(); // Removes the div with the 'ssi-template--tmeplate-change-notice' id

			}

			if ( templateChoiceInitialValue !=  templateChoice.value ) {

				templateChoice.insertAdjacentHTML('afterend', '<span id="ssi-template--template-change-notice" class="ssi-template--template-change-notice"><strong><input type="submit" value="Save changes" class="button-secondary" /> to update the template.</strong></span>');

			}
			
		});

	}

	/* Template reverse */
	var templateReverse = document.querySelector("#hd_ssi_template_reversed");

	if ( templateReverse ) {

		templateReverse.addEventListener("change", function() {
		
			var template = document.querySelector(".ssi-template");

			if ( templateReverse.checked ) {
				template.classList.add('ssi-template--reverse');
			} else {
				template.classList.remove('ssi-template--reverse');
			}		
			
		});

	}

	/* Logo size */
	var logoSize = document.querySelector("#hd_ssi_logo_size");

	if ( logoSize ) {
		logoSize.addEventListener("change", function() {
			document.querySelector(".ssi-template").style.setProperty("--ssi--logo--height", this.value);
		});
	}
	
	/* Text color */
	$('#hd_ssi_text_color').iris({
		//hide: false,
		change: function(event, ui) {
			// event = standard jQuery event, produced by whichever control was changed.
			// ui = standard jQuery UI object, with a color member containing a Color.js object
	
			// update customproperty on 'Clear' button press.
			$('.wp-picker-clear').on('click', function(){
				
				// update the custom property.
				document.querySelector(".ssi-template").style.setProperty("--ssi--text--background-color", 'transparent');

			});

			// update the custom property.
			document.querySelector(".ssi-template").style.setProperty("--ssi--text--color", ui.color.toString());
			
			// update the color preview background color.
			$(this).parents('.wp-picker-container').find('.button.wp-color-result').css('background-color', ui.color.toString());

		}
	});

	/* Text background color */
	//var added_clearer = false;
	$('#hd_ssi_text_bg_color').iris({
		//hide: false,
		change: function(event, ui) {
			// event = standard jQuery event, produced by whichever control was changed.
			// ui = standard jQuery UI object, with a color member containing a Color.js object
	
			// update customproperty on 'Clear' button press.
			$('.wp-picker-clear').on('click', function(){
				
				// update the custom property.
				document.querySelector(".ssi-template").style.setProperty("--ssi--text--background-color", 'transparent');

			});
			
			// update the custom property.
			document.querySelector(".ssi-template").style.setProperty("--ssi--text--background-color", ui.color.toString());
			
			// update the color preview background color.
			$(this).parents('.wp-picker-container').find('.button.wp-color-result').css('background-color', ui.color.toString());

		}
	});

	/* Background color */
	$('#hd_ssi_bg_color').iris({
		//hide: false,
		change: function(event, ui) {
			// event = standard jQuery event, produced by whichever control was changed.
			// ui = standard jQuery UI object, with a color member containing a Color.js object
	
			// update customproperty on 'Clear' button press.
			$('.wp-picker-clear').on('click', function(){
				
				// update the custom property.
				document.querySelector(".ssi-template").style.setProperty("--ssi--text--background-color", 'transparent');

			});

			// update the custom property.
			document.querySelector(".ssi-template").style.setProperty("--ssi--background-color", ui.color.toString());

			// update the color preview background color.
			$(this).parents('.wp-picker-container').find('.button.wp-color-result').css('background-color', ui.color.toString());

		}
	});

	/* Font size */
	var fontSize = document.querySelector("#hd_ssi_font_size");

	if ( fontSize ) {
		fontSize.addEventListener("change", function() {
			document.querySelector(".ssi-template").style.setProperty("--ssi--font-size", this.value);
		});
	}

	/* Font weight */
	var fontWeight = document.querySelector("#hd_ssi_font_weight");

	if ( fontWeight ) {
		fontWeight.addEventListener("change", function() {
			document.querySelector(".ssi-template").style.setProperty("--ssi--font-weight", this.value);
		});
	}

	/* Font style */
	var fontStyle = document.querySelector("#hd_ssi_font_style");

	if ( fontStyle ) {
		fontStyle.addEventListener("change", function() {
			document.querySelector(".ssi-template").style.setProperty("--ssi--font-style", this.value);
		});
	}


	/* Text align */
	var textAlign = document.querySelector("#hd_ssi_text_align");

	if ( textAlign ) {

		textAlign.addEventListener("change", function() {

			textAlign.addEventListener("change", function() {
				document.querySelector(".ssi-template").style.setProperty("--ssi--text-align", this.value);
			});
		
			var template = document.querySelector(".ssi-template");
			
			template.classList.remove('ssi-template--text-align--left', 'ssi-template--text-align--center', 'ssi-template--text-align--right');
			
			template.classList.add('ssi-template--text-align--' + this.value);
			
		});

	}

	/* Logo file */

	var imageWrapper = document.querySelector("#hd-ssi-image-wrapper");

	// Observe a specific DOM element:
	observeDOM( imageWrapper, function(m){ 

		// get the first image in the list.
		var firstImage = imageWrapper.querySelector('.hd-ssi-image');

		imgSrc = $(firstImage).attr('src');
		fullImgSrc = imgSrc.replace("-150x150", "");
		$('.ssi-template__logo').attr('src', fullImgSrc);

	});

	/* Background images */

	var galleryWrapper = document.querySelector("#hd-ssi-gallery-wrapper");

	// Observe a specific DOM element:
	observeDOM( galleryWrapper, function(m){ 

		// get the first image in the list.
		var firstImage = galleryWrapper.querySelector('.hd-ssi-gallery-image');

		imgSrc = $(firstImage).attr('src');
		fullImgSrc = imgSrc.replace("-150x150", "");
		$('.ssi-template__image').attr('src', fullImgSrc);

	});

	$(document).on('click', '.hd-ssi-gallery-image', function () {

		// get the image source.
		imgSrc = $(this).attr('src');
		fullImgSrc = imgSrc.replace("-150x150", "");

		// set template image source.
		$('.ssi-template__image').attr('src', fullImgSrc);

	});

	/* Title placeholder */
	$('.ssi-template__title__inner ').on('focusout', function () {

		console.log($(this).text());

		// set the input value.
		$('#hd_ssi_placeholder_title').val($(this).text());

	});

})( jQuery );