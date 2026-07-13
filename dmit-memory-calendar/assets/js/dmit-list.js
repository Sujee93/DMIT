/**
 * Standalone/embedded list view: paginated, filterable by category and
 * free-text search (title or year). Used both as its own widget and as
 * the "List" pane inside the calendar widget.
 */
( function () {
	'use strict';

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str == null ? '' : String( str );
		return div.innerHTML;
	}

	function debounce( fn, wait ) {
		var t;
		return function () {
			clearTimeout( t );
			var args = arguments;
			t = setTimeout( function () { fn.apply( null, args ); }, wait );
		};
	}

	function initListWidget( root ) {
		var resultsEl = root.querySelector( '.dmit-mc-list-results' );
		var paginationEl = root.querySelector( '.dmit-mc-list-pagination' );
		var searchEl = root.querySelector( '.dmit-mc-list-search' );
		var categoryEl = root.querySelector( '.dmit-mc-list-category' );
		var perPage = parseInt( root.getAttribute( 'data-per-page' ), 10 ) || 12;
		var state = { page: 1, category: root.getAttribute( 'data-category' ) || '', search: '' };

		function load() {
			resultsEl.setAttribute( 'aria-busy', 'true' );
			resultsEl.innerHTML = '<p class="dmit-mc-list-loading">' + escapeHtml( window.dmitMC.i18n.loading ) + '</p>';

			var url = new URL( window.dmitMC.restUrl + '/list' );
			url.searchParams.set( 'page', state.page );
			url.searchParams.set( 'per_page', perPage );
			if ( state.category ) {
				url.searchParams.set( 'category', state.category );
			}
			if ( state.search ) {
				url.searchParams.set( 'search', state.search );
			}

			fetch( url.toString() )
				.then( function ( r ) { return r.json(); } )
				.then( renderResults )
				.catch( function () {
					resultsEl.innerHTML = '<p class="dmit-mc-list-error">' + escapeHtml( window.dmitMC.i18n.submitError ) + '</p>';
				} )
				.finally( function () {
					resultsEl.removeAttribute( 'aria-busy' );
				} );
		}

		function renderResults( json ) {
			var events = json.events || [];
			if ( ! events.length ) {
				resultsEl.innerHTML = '<p class="dmit-mc-list-empty">' + escapeHtml( window.dmitMC.i18n.noResults ) + '</p>';
				paginationEl.innerHTML = '';
				return;
			}

			var html = '<ul class="dmit-mc-list-items">';
			events.forEach( function ( ev ) {
				html += '<li class="dmit-mc-list-item">' +
					( ev.thumbnail ? '<a class="dmit-mc-list-thumb" href="' + ev.permalink + '"><img src="' + ev.thumbnail + '" alt="" loading="lazy" /></a>' : '' ) +
					'<div class="dmit-mc-list-item-body">' +
						'<span class="dmit-mc-list-item-date">' + escapeHtml( ev.year ) + ( window.dmitMC.monthNames && ev.month ? ' · ' + escapeHtml( window.dmitMC.monthNames[ ev.month - 1 ] ) + ' ' + escapeHtml( ev.day ) : '' ) + '</span>' +
						'<h3 class="dmit-mc-list-item-title"><a href="' + ev.permalink + '">' + escapeHtml( ev.title ) + '</a></h3>' +
						( ev.excerpt ? '<p class="dmit-mc-list-item-desc">' + escapeHtml( ev.excerpt ) + '</p>' : '' ) +
					'</div>' +
				'</li>';
			} );
			html += '</ul>';
			resultsEl.innerHTML = html;

			renderPagination( json.page, json.total_pages );
		}

		function renderPagination( page, totalPages ) {
			if ( totalPages <= 1 ) {
				paginationEl.innerHTML = '';
				return;
			}
			var html = '';
			html += '<button type="button" class="dmit-mc-page-btn" data-page="' + ( page - 1 ) + '" ' + ( page <= 1 ? 'disabled' : '' ) + '>' + escapeHtml( window.dmitMC.i18n.prev ) + '</button>';
			html += '<span class="dmit-mc-page-status">' + page + ' / ' + totalPages + '</span>';
			html += '<button type="button" class="dmit-mc-page-btn" data-page="' + ( page + 1 ) + '" ' + ( page >= totalPages ? 'disabled' : '' ) + '>' + escapeHtml( window.dmitMC.i18n.next ) + '</button>';
			paginationEl.innerHTML = html;

			paginationEl.querySelectorAll( '.dmit-mc-page-btn' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					state.page = parseInt( btn.getAttribute( 'data-page' ), 10 );
					load();
					root.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
				} );
			} );
		}

		if ( searchEl ) {
			searchEl.addEventListener( 'input', debounce( function () {
				state.search = searchEl.value.trim();
				state.page = 1;
				load();
			}, 350 ) );
		}

		if ( categoryEl ) {
			categoryEl.addEventListener( 'change', function () {
				state.category = categoryEl.value;
				state.page = 1;
				load();
			} );
		}

		load();
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.dmit-mc-list-widget' ).forEach( initListWidget );
	} );
} )();
