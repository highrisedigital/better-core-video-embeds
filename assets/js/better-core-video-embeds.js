// loop through each of the hd core embed wrapper elements on the page.
// these are the elements that should contain the thumbnail.
document.querySelectorAll( '.hd-bcve-wrapper' ).forEach(( el, i ) => {

	// get the youtube video id for the data attribute on the wrapper.
	var videoId = el.getAttribute( 'data-id' );

	// get the associated template element which holds the embed code.
	var template = document.querySelector( '#hd-bcve-embed-html-' + videoId );

	/**
	 * Add autoplay on the iframe as well as loading from youtube no cookie.
	 * Grabs the iframe from the embed template.
	 * Adds the autoplay and other attrs to the iframe src URL
	 * Replaces the standard youtube domain with the no cookie version.
	 */
	var iframe = template.content.children[0].querySelector( 'iframe' );
	var iframeSrc = iframe.getAttribute( 'src' ) + '&rel=0&showinfo=0&autoplay=1';
	iframeSrc = iframeSrc.replace( 'www.youtube.com', 'www.youtube-nocookie.com' );
	
	// set the new iframe src including autoplay true.
	iframe.setAttribute( 'src', iframeSrc );

	// get the text content of the embed element - this is the caption.
	var caption = template.content.textContent.trim();

	// if we have a caption.
	if ( '' !== caption ) {

		// create an element for the embed caption.
		var captionEl = document.createElement('figcaption');
		captionEl.classList.add( 'wp-element-caption' );
		captionEl.textContent = caption;
	
		// add the caption, after the thumbnail.
		el.append( captionEl );

	}

	// when the wrapper is clicked.
	el.addEventListener( 'click', () => {

		// clone the template element.
		var contentOuter = template.content.cloneNode( true );
		
		// grab just the first child of the template - this is the figure block element which wraps the iframe.
		var content = contentOuter.children[0];

		// add the iframe embed content before the embed wrapper.
		el.before( content );

		// remove the embed wrapper including thumbnail.
		el.remove();

		// remove the template item which holds the iframe.
		template.remove();

	});

});