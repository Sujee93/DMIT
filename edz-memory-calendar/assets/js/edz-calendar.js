/**
 * Month calendar widget. Fetches /month?month=N (matches every year on
 * record for that month) whenever FullCalendar changes month, and draws
 * a visible tag/badge under each day that has at least one event. The
 * side panel is intentionally NOT dependent on that month cache — a
 * click always fetches /day?month=&day= fresh, so the panel works even
 * if the month-level tag fetch is slow, fails, or hasn't run yet.
 */
( function () {
	'use strict';

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str == null ? '' : String( str );
		return div.innerHTML;
	}

	function ready( fn ) {
		if ( 'loading' !== document.readyState ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	function initCalendarWidget( root ) {
		if ( typeof FullCalendar === 'undefined' ) {
			root.querySelector( '.edz-mc-fc-root' ).innerHTML =
				'<p class="edz-mc-error">Calendar library failed to load. Check that assets/lib/fullcalendar/fullcalendar.min.js is reachable (not blocked by a caching/optimisation plugin).</p>';
			return;
		}
		if ( ! window.edzMC ) {
			return;
		}

		var fcRoot = root.querySelector( '.edz-mc-fc-root' );
		var dayPanel = root.querySelector( '.edz-mc-day-panel-inner' );
		var layout = root.querySelector( '.edz-mc-calendar-layout' );
		var listPane = root.querySelector( '.edz-mc-list-pane' );
		var toggleBtns = root.querySelectorAll( '.edz-mc-view-btn' );
		var dayMap = {};
		var selectedEl = null;

		root.style.setProperty( '--edz-mc-accent', window.edzMC.accentColor );
		root.style.setProperty( '--edz-mc-today', window.edzMC.todayColor );

		function fetchMonth( month ) {
			fetch( window.edzMC.restUrl + '/month?month=' + month )
				.then( function ( r ) {
					if ( ! r.ok ) {
						throw new Error( 'HTTP ' + r.status );
					}
					return r.json();
				} )
				.then( function ( json ) {
					dayMap = json.days || {};
					applyTags();
				} )
				.catch( function ( err ) {
					// eslint-disable-next-line no-console
					console.error( 'Edz Memory Calendar: could not load month data', err );
				} );
		}

		function applyTags() {
			fcRoot.querySelectorAll( '.edz-mc-day-cell' ).forEach( function ( cell ) {
				var existing = cell.querySelector( '.edz-mc-tag' );
				if ( existing ) {
					existing.remove();
				}
				if ( cell.classList.contains( 'edz-mc-other-month' ) ) {
					return;
				}
				var day = parseInt( cell.getAttribute( 'data-edz-day' ), 10 );
				var events = dayMap[ day ];
				if ( events && events.length ) {
					cell.classList.add( 'has-events' );
					var tag = document.createElement( 'span' );
					tag.className = 'edz-mc-tag';
					tag.textContent = events.length > 1 ? events.length : '●';
					tag.setAttribute( 'title', events.length + ( events.length === 1 ? ' event' : ' events' ) );
					cell.appendChild( tag );
				} else {
					cell.classList.remove( 'has-events' );
				}
			} );
		}

		function renderDayPanel( date ) {
			var label = new Intl.DateTimeFormat( undefined, { day: 'numeric', month: 'long' } ).format( date );
			dayPanel.innerHTML = '<div class="edz-mc-day-panel-date">' + escapeHtml( label ) + '</div>' +
				'<p class="edz-mc-day-panel-loading">' + escapeHtml( window.edzMC.i18n.loading ) + '</p>';

			var month = date.getMonth() + 1;
			var day = date.getDate();

			fetch( window.edzMC.restUrl + '/day?month=' + month + '&day=' + day )
				.then( function ( r ) {
					if ( ! r.ok ) {
						throw new Error( 'HTTP ' + r.status );
					}
					return r.json();
				} )
				.then( function ( events ) {
					renderEvents( label, Array.isArray( events ) ? events : [] );
				} )
				.catch( function ( err ) {
					// eslint-disable-next-line no-console
					console.error( 'Edz Memory Calendar: could not load day data', err );
					dayPanel.innerHTML = '<div class="edz-mc-day-panel-date">' + escapeHtml( label ) + '</div>' +
						'<p class="edz-mc-day-panel-empty">' + escapeHtml( window.edzMC.i18n.submitError ) + '</p>';
				} );
		}

		function renderEvents( label, events ) {
			var html = '<div class="edz-mc-day-panel-date">' + escapeHtml( label ) + '</div>';

			if ( ! events.length ) {
				html += '<p class="edz-mc-day-panel-empty">' + escapeHtml( window.edzMC.i18n.noEvents ) + '</p>';
				html += '<p class="edz-mc-day-panel-hint">' + escapeHtml( window.edzMC.i18n.pickAnother ) + '</p>';
			} else {
				html += '<ul class="edz-mc-day-events">';
				events
					.slice()
					.sort( function ( a, b ) { return ( a.year || 0 ) - ( b.year || 0 ); } )
					.forEach( function ( ev ) {
						html += '<li class="edz-mc-day-event">' +
							'<span class="edz-mc-event-year">' + escapeHtml( ev.year ) + '</span>' +
							'<h4 class="edz-mc-event-title">' + escapeHtml( ev.title ) + '</h4>' +
							( ev.excerpt ? '<p class="edz-mc-event-desc">' + escapeHtml( ev.excerpt ) + '</p>' : '' ) +
							( ev.permalink ? '<a class="edz-mc-event-link" href="' + ev.permalink + '">' + escapeHtml( window.edzMC.i18n.readMore ) + '</a>' : '' ) +
							'</li>';
					} );
				html += '</ul>';
			}
			dayPanel.innerHTML = html;
		}

		function selectDay( cell, date ) {
			if ( selectedEl ) {
				selectedEl.classList.remove( 'is-selected' );
			}
			cell.classList.add( 'is-selected' );
			selectedEl = cell;
			renderDayPanel( date );
		}

		var calendar = new FullCalendar.Calendar( fcRoot, {
			initialView: 'dayGridMonth',
			headerToolbar: { left: 'prev', center: 'title', right: 'today,next' },
			firstDay: 0,
			height: 'auto',
			dayCellDidMount: function ( info ) {
				info.el.classList.add( 'edz-mc-day-cell' );
				info.el.setAttribute( 'data-edz-day', info.date.getDate() );
				if ( info.isOther ) {
					info.el.classList.add( 'edz-mc-other-month' );
					return;
				}
				info.el.addEventListener( 'click', function () {
					selectDay( info.el, info.date );
				} );
			},
			datesSet: function ( info ) {
				var visibleMonth = new Date( info.view.currentStart.getFullYear(), info.view.currentStart.getMonth(), 15 );
				fetchMonth( visibleMonth.getMonth() + 1 );
				if ( selectedEl ) {
					selectedEl.classList.remove( 'is-selected' );
					selectedEl = null;
				}
				dayPanel.innerHTML = '<p class="edz-mc-day-panel-placeholder">' + escapeHtml( window.edzMC.i18n.pickAnother ) + '</p>';
			},
		} );

		calendar.render();

		toggleBtns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				toggleBtns.forEach( function ( b ) { b.classList.remove( 'is-active' ); } );
				btn.classList.add( 'is-active' );
				var view = btn.getAttribute( 'data-view' );
				if ( 'list' === view ) {
					layout.hidden = true;
					listPane.hidden = false;
				} else {
					layout.hidden = false;
					listPane.hidden = true;
					setTimeout( function () { calendar.updateSize(); }, 0 );
				}
			} );
		} );

		if ( 'list' === root.getAttribute( 'data-default-view' ) ) {
			var listBtn = root.querySelector( '.edz-mc-view-btn[data-view="list"]' );
			if ( listBtn ) {
				listBtn.click();
			}
		}
	}

	ready( function () {
		document.querySelectorAll( '.edz-mc-calendar-widget' ).forEach( initCalendarWidget );
	} );
} )();
