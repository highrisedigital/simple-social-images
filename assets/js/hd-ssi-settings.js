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

	// when the page is fully loaded.
	window.addEventListener('load', (event) => {

		$( '.hd-ssi-input--color-picker' ).each( function(){
		
			$(this).iris({
	
				//hide: false,
				change: function(event, ui) {
					// event = standard jQuery event, produced by whichever control was changed.
					// ui = standard jQuery UI object, with a color member containing a Color.js object
	
					// get the custom property of the input element.
					var customProperty = $(event.target).data('custom-property');
	
					if ( customProperty ) {
					
						// update the custom property.
						document.querySelector(".ssi-template").style.setProperty(customProperty, ui.color.toString());
					
					}
	
					// update the color preview background color.
					$(this).parents('.wp-picker-container').find('.button.wp-color-result').css('background-color', ui.color.toString());
	
					// add the template class.
					document.querySelector(".ssi-template").classList.add('ssi-template--has-text-color');
	
				}
	
			});
	
			// get the 'Clear' button.
			var clearButton = $(this).parents('.wp-picker-input-wrap').find('.wp-picker-clear');

			// get the custom property of the input element.
			var customProperty = $(this).data('custom-property');
	
			// update customproperty on 'Clear' button press.
			clearButton.on('click', function(){
		
				// update the custom property.
				document.querySelector(".ssi-template").style.setProperty(customProperty, 'transparent');
	
			});
	
		});

	});


	// for each input
	$('.hd-ssi-input').each( function(){

		// if the custom property data attribute is present.
		if (this.hasAttribute("data-custom-property")) {

			// get the custom property.
			var thisCustomProperty = $(this).data('custom-property');
			
			// when this input changes...
			this.addEventListener("change", function() {

				// update the custom property.
				document.querySelector(".ssi-template").style.setProperty(thisCustomProperty, this.value);

			});

		}

		// if the modifier class data attribute is present.
		if (this.hasAttribute("data-modifier-class")) {

			// get the target element.
			var theTargetElementClass = $(this).data('target-class');

			// get the modifier class.
			var thisModifierClass = $(this).data('modifier-class');

			// when this input changes...
			this.addEventListener("change", function() {

				// remove all classes that start with the modifier.
				var prefix = thisModifierClass;
				var classes = $('.' + theTargetElementClass)[0].className.split(" ").filter(c => !c.startsWith(prefix));
				$('.' + theTargetElementClass)[0].className = classes.join(" ").trim();

				// add the class.
				$('.' + theTargetElementClass).addClass(thisModifierClass + this.value);

				if ( thisModifierClass == 'ssi--position--' ) {

					// get the parent element.
					var thisParent = $(this).parents('.hd-ssi-setting');

					// get the first sibling of the parent.
					var firstParentSibling = thisParent[0].nextElementSibling;

					// get the second sibling of the parent.
					var secondParentSibling = firstParentSibling.nextElementSibling;

					// get the X-axis input.
					var xAxisInput = firstParentSibling.querySelector('.hd-ssi-input--number');

					// reset the x-axis value to 0.
					xAxisInput.value = 0;

					// force the 'change' event to update the template.
					xAxisInput.dispatchEvent(new Event('change', { 'bubbles': true }));

					// get the Y-axis input.
					var yAxisInput = secondParentSibling.querySelector('.hd-ssi-input--number');

					// reset the y-axis value to 0.
					yAxisInput.value = 0;

					// force the 'change' event to update the template.
					yAxisInput.dispatchEvent(new Event('change', { 'bubbles': true }));

				}

			});

		};

	});


	// image and gallery field preview behaviour.

	// for each gallery or image input section.
	$('.hd-ssi-setting-type--image, .hd-ssi-setting-type--gallery').each( function(){

		/* Logo file */

		var sectionDiv = this;

		// Observe changes to the logo wrapper:
		observeDOM( sectionDiv, function(m){ 

			// get the first image in the list.
			var firstImage = sectionDiv.querySelector('.hd-ssi-image, .hd-ssi-gallery-image');

			if ( firstImage ) {

				imgSrc = $(firstImage).attr('src');
				fullImgSrc = imgSrc.replace("-150x150", "");

				// get the template element class.
				var targetElementClass = $(sectionDiv.querySelector('.hd-ssi-input')).data('target-class');

				$('.' + targetElementClass).attr('src', fullImgSrc);

			}

		});

	});


	// Update preview image when clicked.
	$(document).on('click', '.hd-ssi-gallery-image', function () {

		// get the image source.
		imgSrc = $(this).attr('src');
		fullImgSrc = imgSrc.replace("-150x150", "");

		// set template image source.
		$('.ssi-template__image').attr('src', fullImgSrc);

	});

	/* Title placeholder */
	$('.ssi-template__title__inner ').on('focusout', function () {

		// set the input value.
		$('#hd_ssi_placeholder_title').val($(this).text());

	});

	// tool tip work.
	$( '.hd-ssi-tooltip' ).each( function( i, obj ) {

		// if this tool tip has the checkbox class.
		//if ( ! $( this ).hasClass( 'hd-ssi-tooltip--checkbox' ) ) {
			
			// when the tooltip is clicked.
			$( this ).click( function() {

				console.log('clicked');

				// get the parent element.
				parent = $( this ).next();
				console.log(parent);

				parent.children( '.hd-ssi-input-description' ).toggle();

			});

		//}

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

		// if there are no images left, remove the logo from template.
		if ( 1 > document.getElementsByClassName('hd-ssi-image').length ) {

			$('.ssi-template__logo').attr('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

		}

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
		
		// if there are no images left, remove the image from template.
		if ( 1 > document.getElementsByClassName('hd-ssi-gallery-item').length ) {

			$('.ssi-template__image').attr('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

		}

	});

	/* Preview size toggle */

	var thePreviewElement = document.getElementsByClassName('hd-ssi-template-preview');
	thePreviewElement = thePreviewElement[0];

	var theTemplateElement = document.getElementsByClassName('ssi-template');
	theTemplateElement = theTemplateElement[0];	

	$(thePreviewElement).append('<button class="hd-ssi-template-preview__size-toggle"><span class="screen-reader-text">Toggle size</span>&#10529;</button>');
	
	$('body').on('click', '.hd-ssi-template-preview__size-toggle', function(event){

		event.stopPropagation();
		
		var templateWidth = getComputedStyle(theTemplateElement).getPropertyValue("--ssi--template--scale");

		// if the template width is set to 1
		if ( templateWidth === ' 1'  ) {

			// set width to .7
			theTemplateElement.style.setProperty("--ssi--template--scale", " .7");	
			

		} else {

			// set width to 1
			theTemplateElement.style.setProperty("--ssi--template--scale", " 1");	

		}

	});

	function showHideInputs(toggleElement){

		// get the value of the toggle.
		var toggleChecked = toggleElement.checked;

		// get the parent wrapper.
		var inputWrapper = $(toggleElement).parents('.hd-ssi-setting');

		// get the data section id.
		var dataSectionId = inputWrapper.data('section-id');

		$('[data-section-id="' + dataSectionId + '"]').not(':has([data-section-toggle])').each(function() {

			if ( toggleChecked == 1 ) {
				this.style.display = "block";
			} else {
				this.style.display = "none";
			}

		});

	}

	function showHideElements(toggleElement){

		// replace the logo src when logo is enabled.
		$('.hd-ssi-setting-type--image').each(function(){

			// get the src of the first image.
			imgSrc = $(this).find('img').attr('src');	
			
			if( imgSrc ) {

				fullImgSrc = imgSrc.replace("-150x150", "");

				// set template logo source.
				$('.ssi-template__logo').attr('src', fullImgSrc);
				
			}

		});

		// replace the img src when images are enabled.
		$('.hd-ssi-setting-type--gallery').each(function(){

			// get the src of the first image.
			imgSrc = $(this).find('img').attr('src');	
			
			if( imgSrc ) {

				fullImgSrc = imgSrc.replace("-150x150", "");

				// set template image source.
				$('.ssi-template__image').attr('src', fullImgSrc);

			}

		});

	}

	// when the page is fully loaded.
	window.addEventListener('load', (event) => {

		// for each input with an id that starts with hd_ssi_use_.
		document.querySelectorAll('[data-section-toggle]').forEach( function(element) {
			
			showHideInputs(element);
			showHideElements(element);

			element.addEventListener('change', (event) => {
		
				showHideInputs(element);
				showHideElements(element);

				// get the target class.
				var toggleTargetClass = $(element).data('toggle-target');

				// get the target element.
				var targetElements = document.getElementsByClassName(toggleTargetClass);
				var targetElement = targetElements[0];

				// toggle the hidden class.
				targetElement.classList.toggle("ssi-hidden");

			})

		});

	});


	// when the page is fully loaded.
	window.addEventListener('load', (event) => {

		// for each input with an id that starts with hd_ssi_use_.
		document.querySelectorAll('.hd-ssi-section-heading').forEach( function(element) {
			
			//showHideInputs(element);

			element.addEventListener('click', (event) => {

				console.log(element);
		
				//showHideInputs(element);

				// // get the target class.
				// var toggleTargetClass = $(element).data('toggle-target');

				// // get the target element.
				// var targetElements = document.getElementsByClassName(toggleTargetClass);
				// var targetElement = targetElements[0];

				// // toggle the hidden class.
				// targetElement.classList.toggle("ssi-hidden");

			})

		});

	});


})( jQuery );