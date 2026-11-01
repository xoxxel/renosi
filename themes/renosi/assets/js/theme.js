/**
 * Renosi theme script — drawers, dropdowns, quantity buttons, vehicle finder.
 * Vanilla JS, no dependencies (uses jQuery only to listen to WooCommerce events when present).
 */
( function () {
	'use strict';

	var doc = document;
	var body = doc.body;
	var overlay = doc.querySelector( '.drawer-overlay' );
	var activeDrawer = null;
	var lastTrigger = null;
	var DURATION = 320;

	/* ---------------------------------------------------------------------
	 * Drawers
	 * ------------------------------------------------------------------- */
	function focusable( el ) {
		return Array.prototype.filter.call(
			el.querySelectorAll( 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])' ),
			function ( node ) {
				return node.offsetParent !== null;
			}
		);
	}

	function isDesktopStatic( drawer ) {
		// The shop filter sidebar is a static column on large screens.
		return drawer.classList.contains( 'shop-sidebar' ) && window.matchMedia( '(min-width: 1024px)' ).matches;
	}

	function openDrawer( drawer, trigger, focusSelector ) {
		if ( ! drawer || isDesktopStatic( drawer ) ) {
			return false;
		}
		if ( activeDrawer && activeDrawer !== drawer ) {
			closeDrawer( true );
		}
		if ( drawer.dataset.wasHidden === undefined ) {
			drawer.dataset.wasHidden = drawer.hasAttribute( 'hidden' ) ? '1' : '0';
		}
		drawer.removeAttribute( 'hidden' );
		if ( overlay ) {
			overlay.removeAttribute( 'hidden' );
		}
		// Force layout so the transition runs.
		void drawer.offsetWidth;
		drawer.classList.add( 'is-open' );
		if ( overlay ) {
			overlay.classList.add( 'is-visible' );
		}
		body.classList.add( 'is-locked' );
		activeDrawer = drawer;
		lastTrigger = trigger || null;

		doc.querySelectorAll( '[data-drawer-open="' + drawer.id + '"]' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-expanded', 'true' );
		} );

		window.setTimeout( function () {
			var target = focusSelector ? drawer.querySelector( focusSelector ) : null;
			if ( target ) {
				if ( /^(INPUT|SELECT|TEXTAREA)$/.test( target.tagName ) ) {
					target.focus();
				} else {
					target.scrollIntoView( { block: 'start' } );
					drawer.focus( { preventScroll: true } );
				}
			} else {
				drawer.focus( { preventScroll: true } );
			}
		}, 60 );
		return true;
	}

	function closeDrawer( immediate ) {
		var drawer = activeDrawer;
		if ( ! drawer ) {
			return;
		}
		drawer.classList.remove( 'is-open' );
		if ( overlay ) {
			overlay.classList.remove( 'is-visible' );
		}
		body.classList.remove( 'is-locked' );
		doc.querySelectorAll( '[data-drawer-open="' + drawer.id + '"]' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-expanded', 'false' );
		} );
		activeDrawer = null;

		var finish = function () {
			if ( drawer.dataset.wasHidden === '1' && ! drawer.classList.contains( 'is-open' ) ) {
				drawer.setAttribute( 'hidden', '' );
			}
			if ( overlay && ! activeDrawer ) {
				overlay.setAttribute( 'hidden', '' );
			}
		};
		if ( immediate ) {
			finish();
		} else {
			window.setTimeout( finish, DURATION );
		}
		if ( lastTrigger && typeof lastTrigger.focus === 'function' && ! immediate ) {
			lastTrigger.focus( { preventScroll: true } );
		}
	}

	doc.addEventListener( 'click', function ( e ) {
		var opener = e.target.closest( '[data-drawer-open]' );
		if ( opener ) {
			var drawer = doc.getElementById( opener.getAttribute( 'data-drawer-open' ) );
			// The cart link should navigate normally on the cart & checkout pages.
			if ( drawer && drawer.id === 'cart-drawer' && ( body.classList.contains( 'woocommerce-cart' ) || body.classList.contains( 'woocommerce-checkout' ) ) ) {
				return;
			}
			if ( openDrawer( drawer, opener, opener.getAttribute( 'data-drawer-focus' ) ) ) {
				e.preventDefault();
			}
			return;
		}
		if ( e.target.closest( '[data-drawer-close]' ) ) {
			e.preventDefault();
			closeDrawer();
		}
	} );

	doc.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			if ( activeDrawer ) {
				closeDrawer();
			}
			closeDropdowns();
		}
		if ( e.key === 'Tab' && activeDrawer ) {
			var items = focusable( activeDrawer );
			if ( ! items.length ) {
				return;
			}
			var first = items[ 0 ];
			var last = items[ items.length - 1 ];
			if ( e.shiftKey && doc.activeElement === first ) {
				e.preventDefault();
				last.focus();
			} else if ( ! e.shiftKey && doc.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		}
	} );

	window.addEventListener( 'resize', function () {
		if ( activeDrawer && isDesktopStatic( activeDrawer ) ) {
			closeDrawer( true );
		}
	} );

	/* ---------------------------------------------------------------------
	 * Mobile menu: sub-menu toggles
	 * ------------------------------------------------------------------- */
	doc.querySelectorAll( '.mobile-menu .menu-item-has-children' ).forEach( function ( li ) {
		var btn = doc.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'submenu-toggle';
		btn.setAttribute( 'aria-expanded', 'false' );
		btn.setAttribute( 'aria-label', 'زیرمنو' );
		btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
		btn.addEventListener( 'click', function () {
			var open = li.classList.toggle( 'is-open' );
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
		var link = li.querySelector( ':scope > a' );
		if ( link ) {
			link.after( btn );
		}
		if ( li.classList.contains( 'current-menu-ancestor' ) ) {
			li.classList.add( 'is-open' );
			btn.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	/* ---------------------------------------------------------------------
	 * Dropdown (header categories)
	 * ------------------------------------------------------------------- */
	var dropdowns = doc.querySelectorAll( '[data-dropdown]' );

	function setDropdown( dd, open ) {
		dd.classList.toggle( 'is-open', open );
		var t = dd.querySelector( '[data-dropdown-toggle]' );
		if ( t ) {
			t.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
	}

	function closeDropdowns( except ) {
		dropdowns.forEach( function ( dd ) {
			if ( dd !== except ) {
				setDropdown( dd, false );
			}
		} );
	}

	dropdowns.forEach( function ( dd ) {
		var toggle = dd.querySelector( '[data-dropdown-toggle]' );
		var timer;
		if ( toggle ) {
			toggle.addEventListener( 'click', function () {
				var open = ! dd.classList.contains( 'is-open' );
				closeDropdowns( dd );
				setDropdown( dd, open );
			} );
		}
		if ( window.matchMedia( '(hover: hover)' ).matches ) {
			dd.addEventListener( 'mouseenter', function () {
				window.clearTimeout( timer );
				timer = window.setTimeout( function () {
					setDropdown( dd, true );
				}, 120 );
			} );
			dd.addEventListener( 'mouseleave', function () {
				window.clearTimeout( timer );
				timer = window.setTimeout( function () {
					setDropdown( dd, false );
				}, 180 );
			} );
		}
	} );

	doc.addEventListener( 'click', function ( e ) {
		if ( ! e.target.closest( '[data-dropdown]' ) ) {
			closeDropdowns();
		}
	} );

	/* ---------------------------------------------------------------------
	 * Header shadow on scroll
	 * ------------------------------------------------------------------- */
	var header = doc.querySelector( '.site-header' );
	if ( header && body.classList.contains( 'has-sticky-header' ) ) {
		var ticking = false;
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
			ticking = false;
		};
		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( onScroll );
				ticking = true;
			}
		}, { passive: true } );
		onScroll();
	}

	/* ---------------------------------------------------------------------
	 * Quantity +/- buttons
	 * ------------------------------------------------------------------- */
	doc.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '.qty-btn' );
		if ( ! btn ) {
			return;
		}
		var wrap = btn.closest( '.quantity' );
		var input = wrap ? wrap.querySelector( 'input.qty' ) : null;
		if ( ! input || input.readOnly || input.disabled ) {
			return;
		}
		var step = parseFloat( input.step ) || 1;
		var min = input.min !== '' ? parseFloat( input.min ) : 0;
		var max = input.max !== '' ? parseFloat( input.max ) : Infinity;
		var val = parseFloat( input.value ) || 0;
		var next = btn.classList.contains( 'qty-btn--plus' ) ? val + step : val - step;
		next = Math.max( min, Math.min( max, next ) );
		next = Math.round( next * 1000 ) / 1000;
		if ( next === val ) {
			return;
		}
		input.value = next;
		input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	} );

	/* ---------------------------------------------------------------------
	 * Vehicle finder: numbered dependent steps (brand → model → type)
	 * ------------------------------------------------------------------- */
	function option( value, label ) {
		var o = doc.createElement( 'option' );
		o.value = value;
		o.textContent = label;
		return o;
	}

	doc.querySelectorAll( '[data-finder][data-tree]' ).forEach( function ( form ) {
		var tree;
		try {
			tree = JSON.parse( form.getAttribute( 'data-tree' ) ) || [];
		} catch ( err ) {
			return;
		}
		var brand = form.querySelector( '[data-finder-brand]' );
		var model = form.querySelector( '[data-finder-model]' );
		var type = form.querySelector( '[data-finder-type]' );
		var modelPh = model.options[ 0 ].textContent;
		var typePh = type.options[ 0 ].textContent;

		function findBrand() {
			for ( var i = 0; i < tree.length; i++ ) {
				if ( tree[ i ].s === brand.value ) {
					return tree[ i ];
				}
			}
			return null;
		}
		function findModel( b ) {
			if ( ! b ) {
				return null;
			}
			for ( var i = 0; i < b.m.length; i++ ) {
				if ( b.m[ i ].s === model.value ) {
					return b.m[ i ];
				}
			}
			return null;
		}
		function paintSteps() {
			[ brand, model, type ].forEach( function ( sel ) {
				var step = sel.closest( '.finder__step' );
				step.classList.toggle( 'is-done', !! sel.value );
				step.classList.toggle( 'is-disabled', sel.disabled );
			} );
		}
		function fillTypes( keep ) {
			var m = findModel( findBrand() );
			type.innerHTML = '';
			type.appendChild( option( '', typePh ) );
			if ( m && m.t.length ) {
				m.t.forEach( function ( t ) {
					type.appendChild( option( t.s, t.n ) );
				} );
				type.disabled = false;
				if ( keep ) {
					type.value = keep;
				}
			} else {
				type.disabled = true;
			}
			paintSteps();
		}
		function fillModels( keep, keepType ) {
			var b = findBrand();
			model.innerHTML = '';
			model.appendChild( option( '', modelPh ) );
			if ( b ) {
				b.m.forEach( function ( m ) {
					model.appendChild( option( m.s, m.n ) );
				} );
				model.disabled = ! b.m.length;
				if ( keep ) {
					model.value = keep;
				}
			} else {
				model.disabled = true;
			}
			fillTypes( keepType );
		}

		brand.addEventListener( 'change', function () {
			fillModels();
			if ( ! model.disabled ) {
				model.focus();
			}
		} );
		model.addEventListener( 'change', function () {
			fillTypes();
		} );
		type.addEventListener( 'change', paintSteps );

		var path = [];
		try {
			path = JSON.parse( form.getAttribute( 'data-path' ) ) || [];
		} catch ( err ) {}
		if ( path[ 0 ] ) {
			brand.value = path[ 0 ];
		}
		fillModels( path[ 1 ], path[ 2 ] );

		var card = form.closest( '[data-finder-card]' );
		var reset = card ? card.querySelector( '[data-finder-reset]' ) : null;
		if ( reset ) {
			reset.addEventListener( 'click', function () {
				brand.value = '';
				fillModels();
				brand.focus();
			} );
		}
	} );

	/* ---------------------------------------------------------------------
	 * Banner slider
	 * ------------------------------------------------------------------- */
	doc.querySelectorAll( '[data-slider]' ).forEach( function ( slider ) {
		var track = slider.querySelector( '[data-slider-track]' );
		var slides = slider.querySelectorAll( '.slide' );
		var dots = slider.querySelectorAll( '[data-slider-dot]' );
		var count = slides.length;
		if ( count < 2 ) {
			return;
		}
		var rtl = getComputedStyle( slider ).direction === 'rtl';
		var index = 0;
		var delay = parseInt( slider.getAttribute( 'data-autoplay' ), 10 ) || 0;
		var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var timer = null;

		function go( i ) {
			index = ( i + count ) % count;
			track.style.transform = 'translateX(' + ( rtl ? 1 : -1 ) * index * 100 + '%)';
			slides.forEach( function ( s, k ) {
				var on = k === index;
				s.classList.toggle( 'is-active', on );
				if ( on ) {
					s.removeAttribute( 'aria-hidden' );
				} else {
					s.setAttribute( 'aria-hidden', 'true' );
				}
				s.querySelectorAll( 'a' ).forEach( function ( a ) {
					a.tabIndex = on ? 0 : -1;
				} );
			} );
			dots.forEach( function ( d, k ) {
				d.classList.toggle( 'is-active', k === index );
				d.setAttribute( 'aria-current', k === index ? 'true' : 'false' );
			} );
		}
		function stop() {
			window.clearInterval( timer );
			timer = null;
		}
		function start() {
			if ( ! delay || reduce || timer ) {
				return;
			}
			timer = window.setInterval( function () {
				go( index + 1 );
			}, delay );
		}

		var prev = slider.querySelector( '[data-slider-prev]' );
		var next = slider.querySelector( '[data-slider-next]' );
		if ( prev ) {
			prev.addEventListener( 'click', function () {
				go( index - 1 );
			} );
		}
		if ( next ) {
			next.addEventListener( 'click', function () {
				go( index + 1 );
			} );
		}
		dots.forEach( function ( d ) {
			d.addEventListener( 'click', function () {
				go( parseInt( d.getAttribute( 'data-slider-dot' ), 10 ) );
			} );
		} );

		slider.addEventListener( 'mouseenter', stop );
		slider.addEventListener( 'mouseleave', start );
		slider.addEventListener( 'focusin', stop );
		slider.addEventListener( 'focusout', start );
		doc.addEventListener( 'visibilitychange', function () {
			if ( doc.hidden ) {
				stop();
			} else {
				start();
			}
		} );

		// Swipe.
		var startX = null;
		var startY = null;
		slider.addEventListener( 'touchstart', function ( e ) {
			startX = e.touches[ 0 ].clientX;
			startY = e.touches[ 0 ].clientY;
			stop();
		}, { passive: true } );
		slider.addEventListener( 'touchend', function ( e ) {
			if ( startX === null ) {
				return;
			}
			var dx = e.changedTouches[ 0 ].clientX - startX;
			var dy = e.changedTouches[ 0 ].clientY - startY;
			if ( Math.abs( dx ) > 40 && Math.abs( dx ) > Math.abs( dy ) ) {
				var forward = rtl ? dx > 0 : dx < 0;
				go( index + ( forward ? 1 : -1 ) );
			}
			startX = null;
			start();
		} );

		slider.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowLeft' ) {
				go( index + ( rtl ? 1 : -1 ) );
			} else if ( e.key === 'ArrowRight' ) {
				go( index + ( rtl ? -1 : 1 ) );
			}
		} );

		go( 0 );
		start();
	} );

	/* ---------------------------------------------------------------------
	 * Tabs (hero vehicle panel)
	 * ------------------------------------------------------------------- */
	doc.querySelectorAll( '[data-tabs]' ).forEach( function ( root ) {
		var tabs = root.querySelectorAll( '[data-tab]' );
		function activate( tab, focus ) {
			tabs.forEach( function ( t ) {
				var on = t === tab;
				t.classList.toggle( 'is-active', on );
				t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				t.tabIndex = on ? 0 : -1;
				var panel = doc.getElementById( t.getAttribute( 'aria-controls' ) );
				if ( panel ) {
					panel.hidden = ! on;
				}
			} );
			if ( focus ) {
				tab.focus();
			}
		}
		tabs.forEach( function ( tab, i ) {
			tab.tabIndex = tab.classList.contains( 'is-active' ) ? 0 : -1;
			tab.addEventListener( 'click', function () {
				activate( tab );
			} );
			tab.addEventListener( 'keydown', function ( e ) {
				var rtl = doc.documentElement.dir === 'rtl';
				var next = null;
				if ( e.key === ( rtl ? 'ArrowLeft' : 'ArrowRight' ) || e.key === 'ArrowDown' ) {
					next = tabs[ ( i + 1 ) % tabs.length ];
				} else if ( e.key === ( rtl ? 'ArrowRight' : 'ArrowLeft' ) || e.key === 'ArrowUp' ) {
					next = tabs[ ( i - 1 + tabs.length ) % tabs.length ];
				}
				if ( next ) {
					e.preventDefault();
					activate( next, true );
				}
			} );
		} );
	} );

	/* ---------------------------------------------------------------------
	 * Auto-submit selects (vehicle filter)
	 * ------------------------------------------------------------------- */
	doc.querySelectorAll( '[data-autosubmit] select' ).forEach( function ( sel ) {
		sel.addEventListener( 'change', function () {
			var form = sel.form;
			if ( ! sel.value ) {
				sel.disabled = true; // Keep the clean URL when "all" is chosen.
			}
			form.submit();
		} );
	} );

	/* ---------------------------------------------------------------------
	 * Collapsible SEO text
	 * ------------------------------------------------------------------- */
	doc.querySelectorAll( '[data-collapse]' ).forEach( function ( box ) {
		var bodyEl = box.querySelector( '.seo-content__body' );
		var btn = box.querySelector( '[data-collapse-toggle]' );
		if ( ! bodyEl || ! btn || bodyEl.scrollHeight < 260 ) {
			return;
		}
		box.classList.add( 'is-collapsed' );
		btn.hidden = false;
		btn.addEventListener( 'click', function () {
			var collapsed = box.classList.toggle( 'is-collapsed' );
			btn.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
			btn.querySelector( '[data-more]' ).hidden = ! collapsed;
			btn.querySelector( '[data-less]' ).hidden = collapsed;
		} );
	} );

	/* ---------------------------------------------------------------------
	 * "My car" — remembered per visitor (localStorage)
	 * ------------------------------------------------------------------- */
	var MYCAR = 'renosi_mycar';
	function readCar() {
		try {
			return JSON.parse( window.localStorage.getItem( MYCAR ) || 'null' );
		} catch ( err ) {
			return null;
		}
	}
	function writeCar( car ) {
		try {
			if ( car ) {
				window.localStorage.setItem( MYCAR, JSON.stringify( car ) );
			} else {
				window.localStorage.removeItem( MYCAR );
			}
		} catch ( err ) {}
	}
	function paintCar() {
		var car = readCar();
		doc.querySelectorAll( '[data-mycar]' ).forEach( function ( el ) {
			if ( car && car.url ) {
				el.hidden = false;
				var link = el.querySelector( '[data-mycar-link]' );
				var name = el.querySelector( '[data-mycar-name]' );
				if ( link ) {
					link.href = car.url;
				}
				if ( name ) {
					name.textContent = car.name;
				}
			} else {
				el.hidden = true;
			}
		} );
	}
	doc.querySelectorAll( '[data-mycar-save]' ).forEach( function ( btn ) {
		var car = { name: btn.getAttribute( 'data-name' ), url: btn.getAttribute( 'data-url' ) };
		var current = readCar();
		if ( current && current.url === car.url ) {
			btn.classList.add( 'is-saved' );
		}
		btn.addEventListener( 'click', function () {
			writeCar( car );
			btn.classList.add( 'is-saved' );
			paintCar();
		} );
	} );
	doc.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-mycar-clear]' ) ) {
			e.preventDefault();
			writeCar( null );
			doc.querySelectorAll( '[data-mycar-save]' ).forEach( function ( b ) {
				b.classList.remove( 'is-saved' );
			} );
			paintCar();
		}
	} );
	paintCar();

	/* ---------------------------------------------------------------------
	 * Sticky buy bar (single product, small screens)
	 * ------------------------------------------------------------------- */
	var buyBar = doc.querySelector( '[data-buy-bar]' );
	var cartForm = doc.querySelector( '.summary form.cart' );
	if ( buyBar && cartForm ) {
		buyBar.hidden = false;
		var barTick = false;
		var checkBar = function () {
			buyBar.classList.toggle( 'is-visible', cartForm.getBoundingClientRect().bottom < 0 );
			barTick = false;
		};
		window.addEventListener( 'scroll', function () {
			if ( ! barTick ) {
				window.requestAnimationFrame( checkBar );
				barTick = true;
			}
		}, { passive: true } );
		checkBar();
		buyBar.querySelector( '[data-buy-bar-action]' ).addEventListener( 'click', function () {
			var btn = cartForm.querySelector( '.single_add_to_cart_button' );
			if ( cartForm.classList.contains( 'variations_form' ) || ! btn ) {
				cartForm.scrollIntoView( { behavior: 'smooth', block: 'center' } );
				return;
			}
			btn.click();
		} );
	}


	/* ---------------------------------------------------------------------
	 * Sticky offset: real height of the sticky header (for sticky tabs, sidebar…)
	 * ------------------------------------------------------------------- */
	var stickyHeader = doc.querySelector( '.has-sticky-header .site-header' );
	function setStickyOffset() {
		var offset = 0;
		if ( stickyHeader ) {
			offset = stickyHeader.offsetHeight + ( parseFloat( getComputedStyle( stickyHeader ).top ) || 0 );
		}
		doc.documentElement.style.setProperty( '--r-sticky', offset + 'px' );
	}
	setStickyOffset();
	window.addEventListener( 'resize', setStickyOffset );

	/* ---------------------------------------------------------------------
	 * Shop filters: collapsible cards + category tree
	 * ------------------------------------------------------------------- */
	var chevronSvg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
	doc.querySelectorAll( '.filter-card' ).forEach( function ( card, i ) {
		var head = card.querySelector( '.filter-card__head' );
		if ( ! head ) {
			return;
		}
		var bodyId = 'filter-card-body-' + i;
		var bodies = card.querySelectorAll( '.filter-card__body' );
		var body = bodies[ bodies.length - 1 ];
		body.id = bodyId;
		head.setAttribute( 'role', 'button' );
		head.setAttribute( 'tabindex', '0' );
		head.setAttribute( 'aria-controls', bodyId );
		head.setAttribute( 'aria-expanded', 'true' );
		head.insertAdjacentHTML( 'beforeend', '<span class="filter-card__chevron">' + chevronSvg + '</span>' );
		function toggle() {
			var collapsed = card.classList.toggle( 'is-collapsed' );
			head.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
		}
		head.addEventListener( 'click', toggle );
		head.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Enter' || e.key === ' ' ) {
				e.preventDefault();
				toggle();
			}
		} );
	} );

	doc.querySelectorAll( '.shop-sidebar .product-categories' ).forEach( function ( tree ) {
		tree.querySelectorAll( 'li' ).forEach( function ( li ) {
			var link = li.querySelector( ':scope > a' );
			if ( ! link ) {
				return;
			}
			var count = li.querySelector( ':scope > .count' );
			if ( count ) {
				count.textContent = count.textContent.replace( /[()]/g, '' ).trim();
				link.appendChild( count );
			}
			var row = doc.createElement( 'div' );
			row.className = 'cat-row';
			li.insertBefore( row, link );
			row.appendChild( link );
			var children = li.querySelector( ':scope > ul.children' );
			if ( children ) {
				var btn = doc.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'cat-toggle';
				btn.setAttribute( 'aria-label', 'زیرمجموعه‌ها' );
				btn.innerHTML = chevronSvg;
				var open = li.classList.contains( 'current-cat' ) || li.classList.contains( 'current-cat-parent' ) || li.classList.contains( 'current-cat-ancestor' );
				li.classList.toggle( 'is-open', open );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				btn.addEventListener( 'click', function () {
					var o = li.classList.toggle( 'is-open' );
					btn.setAttribute( 'aria-expanded', o ? 'true' : 'false' );
				} );
				row.appendChild( btn );
			}
		} );
	} );

	/* ---------------------------------------------------------------------
	 * WooCommerce: open the mini-cart after AJAX add-to-cart
	 * ------------------------------------------------------------------- */
	if ( window.jQuery ) {
		window.jQuery( doc.body ).on( 'added_to_cart', function () {
			if ( body.classList.contains( 'woocommerce-cart' ) || body.classList.contains( 'woocommerce-checkout' ) ) {
				return;
			}
			var drawer = doc.getElementById( 'cart-drawer' );
			if ( drawer ) {
				openDrawer( drawer, null );
			}
		} );
	}
}() );
