/**
 * "On This Day" homepage timeline. Same minimal vertical-line style as
 * the reference design: a small dot per event, an orange year label, and
 * a scroll-triggered brighten effect. Data comes from the REST
 * /on-this-day endpoint instead of a hardcoded array.
 */
( function () {
	'use strict';

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str == null ? '' : String( str );
		return div.innerHTML;
	}

	function render( wrap, items, emptyMessage ) {
		if ( ! items.length ) {
			wrap.innerHTML = '<p class="rl-empty">' + escapeHtml( emptyMessage ) + '</p>';
			return;
		}

		wrap.innerHTML = items.map( function ( it ) {
			return '<div class="rl-item">' +
				'<p class="rl-year">' + escapeHtml( it.year ) + '</p>' +
				'<h3 class="rl-title">' + ( it.permalink ? '<a href="' + it.permalink + '">' + escapeHtml( it.title ) + '</a>' : escapeHtml( it.title ) ) + '</h3>' +
				'<p class="rl-desc">' + escapeHtml( it.excerpt || '' ) + '</p>' +
			'</div>';
		} ).join( '' );

		observe( wrap );
	}

	function observe( wrap ) {
		var items = wrap.querySelectorAll( '.rl-item' );
		if ( ! ( 'IntersectionObserver' in window ) ) {
			items.forEach( function ( el ) { el.classList.add( 'is-active' ); } );
			return;
		}
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				e.target.classList.toggle( 'is-active', e.isIntersecting );
			} );
		}, { threshold: 0.5, rootMargin: '-8% 0px -8% 0px' } );
		items.forEach( function ( el ) { io.observe( el ); } );
	}

	function initTimeline( wrap ) {
		var month = wrap.getAttribute( 'data-month' );
		var day = wrap.getAttribute( 'data-day' );
		var emptyMessage = wrap.getAttribute( 'data-empty-message' ) || ( window.edzMC ? window.edzMC.i18n.noEvents : '' );

		var url = new URL( window.edzMC.restUrl + '/on-this-day' );
		if ( month ) {
			url.searchParams.set( 'month', month );
		}
		if ( day ) {
			url.searchParams.set( 'day', day );
		}

		wrap.innerHTML = '<p class="rl-loading">' + escapeHtml( window.edzMC.i18n.loading ) + '</p>';

		fetch( url.toString() )
			.then( function ( r ) { return r.json(); } )
			.then( function ( json ) {
				render( wrap, json.events || [], emptyMessage );
			} )
			.catch( function () {
				render( wrap, [], emptyMessage );
			} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.rl-timeline' ).forEach( initTimeline );
	} );
} )();
