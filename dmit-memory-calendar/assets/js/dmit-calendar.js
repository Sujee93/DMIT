/**
 * Month calendar widget. Fetches /month?month=N (matches every year on
 * record for that month) whenever FullCalendar changes month, draws a
 * dot under each day that has at least one event, and renders the
 * clicked day's events (possibly spanning many different years) into
 * the side panel. The "List" toggle swaps to the already-mounted list
 * widget instead of re-rendering anything.
 */
( function () {
	'use strict';

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str == null ? '' : String( str );
		return div.innerHTML;
	}

	function initCalendarWidget( root ) {
		if ( typeof FullCalendar === 'undefined' ) {
			return;
		}

		var fcRoot = root.querySelector( '.dmit-mc-fc-root' );
		var dayPanel = root.querySelector( '.dmit-mc-day-panel-inner' );
		var layout = root.querySelector( '.dmit-mc-calendar-layout' );
		var listPane = root.querySelector( '.dmit-mc-list-pane' );
		var toggleBtns = root.querySelectorAll( '.dmit-mc-view-btn' );
		var dayMap = {};
		var selectedEl = null;

		if ( window.dmitMC ) {
			root.style.setProperty( '--dmit-mc-accent', window.dmitMC.accentColor );
			root.style.setProperty( '--dmit-mc-today', window.dmitMC.todayColor );
		}

		function fetchMonth( month ) {
			fetch( window.dmitMC.restUrl + '/month?month=' + month )
				.then( function ( r ) { return r.json(); } )
				.then( function ( json ) {
					dayMap = json.days || {};
					applyDots();
				} )
				.catch( function () {} );
		}

		function applyDots() {
			fcRoot.querySelectorAll( '.dmit-mc-day-cell' ).forEach( function ( cell ) {
				var existing = cell.querySelector( '.dmit-mc-dots' );
				if ( existing ) {
					existing.remove();
				}
				if ( cell.classList.contains( 'dmit-mc-other-month' ) ) {
					return;
				}
				var day = parseInt( cell.getAttribute( 'data-dmit-day' ), 10 );
				var events = dayMap[ day ];
				if ( events && events.length ) {
					cell.classList.add( 'has-events' );
					var wrap = document.createElement( 'div' );
					wrap.className = 'dmit-mc-dots';
					var max = Math.min( events.length, 4 );
					for ( var i = 0; i < max; i++ ) {
						var dot = document.createElement( 'span' );
						dot.className = 'dmit-mc-dot';
						wrap.appendChild( dot );
					}
					cell.appendChild( wrap );
				} else {
					cell.classList.remove( 'has-events' );
				}
			} );
		}

		function renderDayPanel( date, day ) {
			var events = dayMap[ day ] || [];
			var label = new Intl.DateTimeFormat( undefined, { day: 'numeric', month: 'long' } ).format( date );

			var html = '<div class="dmit-mc-day-panel-date">' + escapeHtml( label ) + '</div>';

			if ( ! events.length ) {
				html += '<p class="dmit-mc-day-panel-empty">' + escapeHtml( window.dmitMC.i18n.noEvents ) + '</p>';
				html += '<p class="dmit-mc-day-panel-hint">' + escapeHtml( window.dmitMC.i18n.pickAnother ) + '</p>';
			} else {
				html += '<ul class="dmit-mc-day-events">';
				events
					.slice()
					.sort( function ( a, b ) { return ( a.year || 0 ) - ( b.year || 0 ); } )
					.forEach( function ( ev ) {
						html += '<li class="dmit-mc-day-event">' +
							'<span class="dmit-mc-event-year">' + escapeHtml( ev.year ) + '</span>' +
							'<h4 class="dmit-mc-event-title">' + escapeHtml( ev.title ) + '</h4>' +
							( ev.excerpt ? '<p class="dmit-mc-event-desc">' + escapeHtml( ev.excerpt ) + '</p>' : '' ) +
							( ev.permalink ? '<a class="dmit-mc-event-link" href="' + ev.permalink + '">' + escapeHtml( window.dmitMC.i18n.readMore ) + '</a>' : '' ) +
							'</li>';
					} );
				html += '</ul>';
			}
			dayPanel.innerHTML = html;
		}

		function selectDay( cell, date, day ) {
			if ( selectedEl ) {
				selectedEl.classList.remove( 'is-selected' );
			}
			cell.classList.add( 'is-selected' );
			selectedEl = cell;
			renderDayPanel( date, day );
		}

		var calendar = new FullCalendar.Calendar( fcRoot, {
			initialView: 'dayGridMonth',
			headerToolbar: { left: 'prev', center: 'title', right: 'today,next' },
			firstDay: 0,
			height: 'auto',
			dayCellDidMount: function ( info ) {
				info.el.classList.add( 'dmit-mc-day-cell' );
				info.el.setAttribute( 'data-dmit-day', info.date.getDate() );
				if ( info.isOther ) {
					info.el.classList.add( 'dmit-mc-other-month' );
					return;
				}
				info.el.addEventListener( 'click', function () {
					selectDay( info.el, info.date, info.date.getDate() );
				} );
			},
			datesSet: function ( info ) {
				var visibleMonth = new Date( info.view.currentStart.getFullYear(), info.view.currentStart.getMonth(), 15 );
				fetchMonth( visibleMonth.getMonth() + 1 );
				if ( selectedEl ) {
					selectedEl.classList.remove( 'is-selected' );
					selectedEl = null;
				}
				dayPanel.innerHTML = '<p class="dmit-mc-day-panel-placeholder">' + escapeHtml( window.dmitMC.i18n.pickAnother ) + '</p>';
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
			var listBtn = root.querySelector( '.dmit-mc-view-btn[data-view="list"]' );
			if ( listBtn ) {
				listBtn.click();
			}
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.dmit-mc-calendar-widget' ).forEach( initCalendarWidget );
	} );
} )();
