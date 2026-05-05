/**
 * Hero: fundo em carrossel (transição suave por opacidade).
 */
(function () {
	function initOne( root ) {
		var raw = root.getAttribute( 'data-slides' );
		if ( ! raw ) {
			return;
		}
		var slides;
		try {
			slides = JSON.parse( raw );
		} catch ( e ) {
			return;
		}
		if ( ! Array.isArray( slides ) || slides.length < 2 ) {
			return;
		}

		var intervalMs = parseInt( root.getAttribute( 'data-interval' ) || '7000', 10 );
		var fadeMs = parseInt( root.getAttribute( 'data-fade' ) || '1200', 10 );
		if ( intervalMs < 3500 ) {
			intervalMs = 3500;
		}
		if ( fadeMs < 400 ) {
			fadeMs = 400;
		}
		if ( fadeMs > intervalMs - 500 ) {
			fadeMs = Math.max( 400, intervalMs - 500 );
		}

		var a = root.querySelector( '.green-hero-slide--a' );
		var b = root.querySelector( '.green-hero-slide--b' );
		if ( ! a || ! b ) {
			return;
		}

		var idx = 0;
		var visible = a;
		var hidden = b;

		function setBg( el, url ) {
			if ( ! url ) {
				return;
			}
			el.style.backgroundImage = 'url("' + String( url ).replace( /\\/g, '\\\\' ).replace( /"/g, '\\"' ) + '")';
		}

		setBg( visible, slides[ 0 ] );
		setBg( hidden, slides.length > 1 ? slides[ 1 ] : slides[ 0 ] );
		visible.style.opacity = '1';
		hidden.style.opacity = '0';
		visible.style.transition = 'opacity ' + fadeMs + 'ms ease-in-out';
		hidden.style.transition = 'opacity ' + fadeMs + 'ms ease-in-out';

		function tick() {
			var nextIdx = ( idx + 1 ) % slides.length;
			setBg( hidden, slides[ nextIdx ] );
			hidden.style.opacity = '1';
			visible.style.opacity = '0';
			var swapVisible = visible;
			visible = hidden;
			hidden = swapVisible;
			idx = nextIdx;
		}

		window.setInterval( tick, intervalMs );
	}

	function init() {
		document.querySelectorAll( '.green-hero-bg--carousel' ).forEach( initOne );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
