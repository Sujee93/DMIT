/**
 * Handles the public "submit a history event" form: optional reCAPTCHA v3
 * token fetch, then a multipart POST to admin-ajax so file uploads work.
 */
( function () {
	'use strict';

	function initForm( form ) {
		var statusEl = form.querySelector( '.dmit-mc-form-status' );
		var submitBtn = form.querySelector( '.dmit-mc-submit-btn' );
		var recaptchaTokenField = form.querySelector( '.dmit-mc-recaptcha-token' );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			setStatus( '', '' );
			submitBtn.disabled = true;

			getRecaptchaToken( recaptchaTokenField )
				.then( function () {
					var formData = new FormData( form );
					formData.append( 'action', 'dmit_mc_submit_event' );
					formData.append( 'nonce', window.dmitMC.submitNonce );

					return fetch( window.dmitMC.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						body: formData,
					} );
				} )
				.then( function ( res ) { return res.json(); } )
				.then( function ( json ) {
					if ( json && json.success ) {
						setStatus( ( json.data && json.data.message ) || window.dmitMC.i18n.submitSuccess, 'success' );
						form.reset();
					} else {
						setStatus( ( json && json.data && json.data.message ) || window.dmitMC.i18n.submitError, 'error' );
					}
				} )
				.catch( function () {
					setStatus( window.dmitMC.i18n.submitError, 'error' );
				} )
				.finally( function () {
					submitBtn.disabled = false;
				} );
		} );

		function setStatus( message, type ) {
			statusEl.textContent = message;
			statusEl.className = 'dmit-mc-form-status' + ( type ? ' is-' + type : '' );
		}
	}

	function getRecaptchaToken( tokenField ) {
		if ( ! tokenField || typeof grecaptcha === 'undefined' || ! window.dmitMC || ! window.dmitMC.recaptchaSiteKey ) {
			return Promise.resolve();
		}
		return new Promise( function ( resolve ) {
			grecaptcha.ready( function () {
				grecaptcha.execute( window.dmitMC.recaptchaSiteKey, { action: 'submit_event' } ).then( function ( token ) {
					tokenField.value = token;
					resolve();
				} );
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.dmit-mc-submit-form' ).forEach( initForm );
	} );
} )();
