/**
 * Month calendar widget. Every historical event on the displayed month
 * becomes its own FullCalendar event (title = the event's title), so
 * each day shows solid, coloured, truncated title tags — stacked up to
 * `dayMaxEvents`, with FullCalendar's own "+N more" for the rest.
 * Clicking a day (or one of its tags) fetches that exact date's events
 * fresh from /day, so the side panel is always scoped to the single
 * date clicked, and includes each event's featured image.
 */
( function () {
	'use strict';

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str == null ? '' : String( str );
		return div.innerHTML;
	}

	function pad2( n ) {
		return n < 10 ? '0' + n : '' + n;
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
		var selectedEl = null;

		root.style.setProperty( '--edz-mc-accent', window.edzMC.accentColor );
		root.style.setProperty( '--edz-mc-today', window.edzMC.todayColor );

		function fetchMonth( year, month ) {
			fetch( window.edzMC.restUrl + '/month?month=' + month )
				.then( function ( r ) {
					if ( ! r.ok ) {
						throw new Error( 'HTTP ' + r.status );
					}
					return r.json();
				} )
				.then( function ( json ) {
					var days = json.days || {};
					var fcEvents = [];

					Object.keys( days ).forEach( function ( dayStr ) {
						var day = parseInt( dayStr, 10 );
						var dateStr = year + '-' + pad2( month ) + '-' + pad2( day );

						days[ dayStr ].forEach( function ( ev ) {
							fcEvents.push( {
								id: 'edz-' + ev.id,
								title: ev.title,
								start: dateStr,
								allDay: true,
								color: 'var(--edz-mc-accent)',
								textColor: '#fff',
								extendedProps: { count: days[ dayStr ].length },
							} );
						} );
					} );

					calendar.removeAllEvents();
					calendar.addEventSource( fcEvents );
				} )
				.catch( function ( err ) {
					// eslint-disable-next-line no-console
					console.error( 'Edz Memory Calendar: could not load month data', err );
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
							( ev.thumbnail ? '<span class="edz-mc-event-thumb"><img src="' + ev.thumbnail + '" alt="" loading="lazy" /></span>' : '' ) +
							'<div class="edz-mc-event-body">' +
								'<span class="edz-mc-event-year">' + escapeHtml( ev.year ) + '</span>' +
								'<h4 class="edz-mc-event-title">' + escapeHtml( ev.title ) + '</h4>' +
								( ev.excerpt ? '<p class="edz-mc-event-desc">' + escapeHtml( ev.excerpt ) + '</p>' : '' ) +
							'</div>' +
						'</li>';
					} );
				html += '</ul>';
			}
			dayPanel.innerHTML = html;
		}

		function selectDay( cellEl, date ) {
			if ( selectedEl ) {
				selectedEl.classList.remove( 'is-selected' );
			}
			if ( cellEl ) {
				cellEl.classList.add( 'is-selected' );
				selectedEl = cellEl;
			}
			renderDayPanel( date );
		}

		function cellForDate( date ) {
			var iso = date.getFullYear() + '-' + pad2( date.getMonth() + 1 ) + '-' + pad2( date.getDate() );
			return fcRoot.querySelector( '.fc-daygrid-day[data-date="' + iso + '"]' );
		}

		var calendar = new FullCalendar.Calendar( fcRoot, {
			initialView: 'dayGridMonth',
			headerToolbar: { left: 'prev', center: 'title', right: 'today,next' },
			firstDay: 0,
			height: 'auto',
			dayMaxEvents: 2,
			eventDisplay: 'block',
			eventDidMount: function ( info ) {
				var count = info.event.extendedProps.count || 1;
				info.el.setAttribute(
					'title',
					count <= 1 ? window.edzMC.i18n.oneEvent : window.edzMC.i18n.manyEvents.replace( '%d', count )
				);
			},
			dateClick: function ( info ) {
				selectDay( info.dayEl, info.date );
			},
			eventClick: function ( info ) {
				info.jsEvent.preventDefault();
				selectDay( cellForDate( info.event.start ), info.event.start );
			},
			datesSet: function ( info ) {
				var visibleMonth = new Date( info.view.currentStart.getFullYear(), info.view.currentStart.getMonth(), 15 );
				fetchMonth( visibleMonth.getFullYear(), visibleMonth.getMonth() + 1 );
				if ( selectedEl ) {
					selectedEl.classList.remove( 'is-selected' );
					selectedEl = null;
				}
				dayPanel.innerHTML = '<p class="edz-mc-day-panel-placeholder">' + escapeHtml( window.edzMC.i18n.pickAnother ) + '</p>';
			},
		} );

		calendar.render();

		// Pre-select today so the side panel opens with today's history
		// instead of sitting empty until the visitor clicks a date.
		selectDay( cellForDate( new Date() ), new Date() );

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
