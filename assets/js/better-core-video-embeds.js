// loop through each of the hd core embed wrapper elements on the page.
// these are the elements that should contain the thumbnail.
document.querySelectorAll( '.hd-bcve-wrapper' ).forEach(( el, i ) => {

	// when the wrapper is clicked.
	el.addEventListener( 'click', () => {

		// get the youtube video id for the data attribute on the wrapper.
		var videoId = el.getAttribute( 'data-id' );

		// get the associated template element which holds the embed code.
		var template = document.querySelector( '#hd-bcve-embed-html-' + videoId );

		// clone the template element.
		var contentOuter = template.content.cloneNode( true );

		// grab just the first child of the template - this is the figure block element which wraps the iframe.
		var content =contentOuter.children[0]

		// find the iframe in the embed content.
		var iframe = content.querySelector( 'iframe' );

		// add auto play true to the iframe src.
		// ensures the video plays when the thumbnail is clicked.
		var iframeSrc = iframe.getAttribute( 'src' ) + '&autoplay=true&rel=0&showinfo=0';
		var iframeSrcNew = iframeSrc.replace( 'www.youtube.com', 'www.youtube-nocookie.com' );
		
		// set the new iframe src including autoplay true.
		iframe.setAttribute( 'src', iframeSrcNew );
		

		// add the iframe embed content before the embed wrapper.
		el.before( content );

		// remove the embed wrapper including thumbnail.
		el.remove();

		// remove the template item which holds the iframe.
		template.remove();

	});

});