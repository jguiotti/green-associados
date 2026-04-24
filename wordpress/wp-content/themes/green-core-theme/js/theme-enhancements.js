( function () {
	const header = document.getElementById( 'green-site-header' );
	const root = document.documentElement;
	const menuToggle = document.getElementById( 'green-menu-toggle' );
	const primaryNav = document.getElementById( 'green-primary-nav' );

	function setScrollPadding() {
		if ( ! header ) {
			return;
		}
		const rect = header.getBoundingClientRect();
		const h = rect.height;
		root.style.setProperty( '--green-header-height', h + 'px' );
		const offset = Math.round( rect.bottom );
		root.style.setProperty( '--green-header-offset', offset + 'px' );
		root.style.scrollPaddingTop = offset + 'px';
	}

	function onScroll() {
		if ( ! header ) {
			return;
		}
		header.classList.toggle( 'is-scrolled', window.scrollY > 12 );
	}

	function initReveal() {
		const sections = document.querySelectorAll(
			'.green-section:not(.green-hero-block), .green-animate-on-scroll'
		);
		if ( ! sections.length || ! ( 'IntersectionObserver' in window ) ) {
			sections.forEach( function ( el ) {
				el.classList.add( 'green-in-view' );
			} );
			return;
		}
		const io = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'green-in-view' );
						io.unobserve( entry.target );
					}
				} );
			},
			{ root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);
		sections.forEach( function ( el ) {
			io.observe( el );
		} );
	}

	function initMobileNav() {
		if ( ! menuToggle || ! primaryNav || ! header ) {
			return;
		}
		menuToggle.addEventListener( 'click', function () {
			const open = document.body.classList.toggle( 'green-nav-is-open' );
			header.classList.toggle( 'green-nav-is-open', open );
			menuToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
		primaryNav.querySelectorAll( 'a' ).forEach( function ( link ) {
			link.addEventListener( 'click', function () {
				document.body.classList.remove( 'green-nav-is-open' );
				header.classList.remove( 'green-nav-is-open' );
				menuToggle.setAttribute( 'aria-expanded', 'false' );
			} );
		} );
	}

	setScrollPadding();
	onScroll();
	initReveal();
	initMobileNav();

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', function () {
		setScrollPadding();
		if ( window.innerWidth > 960 && header ) {
			document.body.classList.remove( 'green-nav-is-open' );
			header.classList.remove( 'green-nav-is-open' );
			if ( menuToggle ) {
				menuToggle.setAttribute( 'aria-expanded', 'false' );
			}
		}
	} );

	function isSiteFrontPage() {
		return document.body.classList.contains( 'home' );
	}

	function homeBaseUrl() {
		if ( typeof greenCoreTheme !== 'undefined' && greenCoreTheme.homeUrl ) {
			return String( greenCoreTheme.homeUrl ).replace( /\/?$/, '' );
		}
		return window.location.origin.replace( /\/?$/, '' );
	}

	function normalizeUrlForCompare( url ) {
		return String( url ).replace( /\/+$/, '' );
	}

	function getHashData( href ) {
		if ( ! href || href === '#' ) {
			return null;
		}
		if ( href.charAt( 0 ) === '#' ) {
			if ( href.length < 2 ) {
				return null;
			}
			return {
				hash: href,
				baseUrl: '',
			};
		}
		try {
			const parsed = new URL( href, window.location.href );
			if ( ! parsed.hash || parsed.hash === '#' ) {
				return null;
			}
			return {
				hash: parsed.hash,
				baseUrl: normalizeUrlForCompare( parsed.origin + parsed.pathname ),
			};
		} catch ( err ) {
			return null;
		}
	}

	function smoothScrollToHash( hash ) {
		if ( ! hash || hash === '#' ) {
			return false;
		}
		const anchorAliases = {
			'#areas-of-expertise': '#atuacao',
			'#ai': '#ia',
			'#security': '#seguranca',
			'#team': '#equipe',
			'#contact': '#contato',
			'#atuacao': '#areas-of-expertise',
			'#ia': '#ai',
			'#seguranca': '#security',
			'#equipe': '#team',
			'#contato': '#contact',
		};
		let target = document.querySelector( hash );
		if ( ! target && anchorAliases[ hash ] ) {
			target = document.querySelector( anchorAliases[ hash ] );
		}
		if ( ! target ) {
			return false;
		}
		target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
		return true;
	}

	function isHomeBase( baseUrl ) {
		if ( ! baseUrl ) {
			return false;
		}
		const currentBase = normalizeUrlForCompare(
			window.location.origin + window.location.pathname
		);
		const homeBase = normalizeUrlForCompare( homeBaseUrl() );
		return baseUrl === currentBase || baseUrl === homeBase;
	}

	document.querySelectorAll( 'a[href*="#"]' ).forEach( function ( anchor ) {
		const href = anchor.getAttribute( 'href' );
		const hashData = getHashData( href );
		if ( ! hashData ) {
			return;
		}
		anchor.addEventListener( 'click', function ( e ) {
			if ( e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
				return;
			}
			if ( hashData.baseUrl && ! isHomeBase( hashData.baseUrl ) ) {
				return;
			}
			if ( ! isSiteFrontPage() ) {
				e.preventDefault();
				window.location.href = homeBaseUrl() + hashData.hash;
				return;
			}
			if ( smoothScrollToHash( hashData.hash ) ) {
				e.preventDefault();
				if ( window.location.hash !== hashData.hash ) {
					history.replaceState( null, '', hashData.hash );
				}
			}
		} );
	} );

	if ( isSiteFrontPage() && window.location.hash && window.location.hash.length > 1 ) {
		window.setTimeout( function () {
			smoothScrollToHash( window.location.hash );
		}, 80 );
	}

	function initPolylangHashOnLangLinks() {
		var links = document.querySelectorAll(
			'.green-header-lang a.green-header-lang-link, .green-header-lang .menu a[href], .green-header-lang-list a[href]'
		);
		links.forEach( function ( a ) {
			a.addEventListener( 'click', function ( e ) {
				var h = window.location.hash;
				if ( ! h || h.length < 2 ) {
					return;
				}
				if ( e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
					return;
				}
				var raw = a.getAttribute( 'href' );
				if ( ! raw || raw === '#' || raw === '#0' ) {
					return;
				}
				e.preventDefault();
				try {
					var u = new URL( raw, window.location.href );
					u.hash = h;
					window.location.assign( u.toString() );
				} catch ( err2 ) {
					var clean = String( raw ).split( '#' )[ 0 ];
					window.location.assign( clean + h );
				}
			} );
		} );
	}

	/**
	 * Reveal ao scroll: .reveal recebe .active (CSS no tema; movimento suave).
	 */
	function initRevealClassActive() {
		var reduce =
			window.matchMedia &&
			window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var nodes = document.querySelectorAll( '.reveal' );
		if ( reduce ) {
			nodes.forEach( function ( el ) {
				el.classList.add( 'active' );
			} );
			return;
		}
		if ( ! nodes.length || ! ( 'IntersectionObserver' in window ) ) {
			nodes.forEach( function ( el ) {
				el.classList.add( 'active' );
			} );
			return;
		}
		var io = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'active' );
						io.unobserve( entry.target );
					}
				} );
			},
			{ root: null, rootMargin: '0px 0px -5% 0px', threshold: 0.08 }
		);
		nodes.forEach( function ( el ) {
			io.observe( el );
		} );
	}

	initPolylangHashOnLangLinks();
	initRevealClassActive();
} )();
