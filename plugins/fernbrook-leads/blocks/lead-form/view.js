/**
 * Quote form behaviour (about 3 KB compressed, no libraries).
 *
 * - Turns the full form into one question at a time, with a progress bar and back button.
 * - Checks each step before moving on, with messages linked to the fields for screen readers.
 * - Choice cards move to the next step on their own, so most people only type on the last step.
 * - Sends where the visitor first came from (UTM tags, ad click IDs, recorded by assets/track.js) with the lead.
 * - Pushes analytics events to window.dataLayer (see includes/tracking.php for the list).
 */
( function () {
	'use strict';

	var dl = function ( o ) { ( window.dataLayer = window.dataLayer || [] ).push( o ); };
	// Where the visitor first came from. Recorded on every page by assets/track.js (loaded first).
	var source = window.fblAttribution || {};

	/* ---------- Messages ---------- */
	var MSG = {
		zip: 'Enter your 5-digit ZIP code.',
		program: 'Choose what you are interested in.',
		dog_age: 'Choose your dog\'s age.',
		name: 'Enter your first name.',
		phone: 'Enter a 10-digit phone number, like 555 010 0142.',
		email: 'Enter an email address, like name@example.com.'
	};

	function fieldValid( input, form ) {
		var v = ( input.value || '' ).trim();
		switch ( input.name ) {
			case 'zip': return /^\d{5}$/.test( v );
			case 'name': return v.length >= 2;
			case 'phone': var d = v.replace( /\D/g, '' ); if ( d.length === 11 && d[ 0 ] === '1' ) d = d.slice( 1 ); return d.length === 10;
			case 'email': return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( v );
			default: return !! form.querySelector( 'input[name="' + input.name + '"]:checked' );
		}
	}

	function showError( form, name, show ) {
		var input = form.querySelector( '[name="' + name + '"]' );
		if ( ! input ) return;
		var errId = ( input.getAttribute( 'aria-describedby' ) || '' ).split( ' ' )[ 0 ];
		var holder = input.type === 'radio' ? input.closest( 'fieldset' ) : input;
		if ( input.type === 'radio' ) errId = holder.getAttribute( 'aria-describedby' );
		var err = errId && document.getElementById( errId );
		if ( err ) { err.textContent = show ? MSG[ name ] : ''; err.hidden = ! show; }
		if ( show ) holder.setAttribute( 'aria-invalid', 'true' ); else holder.removeAttribute( 'aria-invalid' );
	}

	/* Returns the names of fields in a step that are not filled in correctly. */
	function checkStep( form, step ) {
		var bad = [];
		var seen = {};
		step.querySelectorAll( 'input[required]' ).forEach( function ( input ) {
			if ( seen[ input.name ] ) return;
			seen[ input.name ] = true;
			var ok = fieldValid( input, form );
			showError( form, input.name, ! ok );
			if ( ! ok ) bad.push( input.name );
		} );
		return bad;
	}

	function focusFirstInvalid( form, names ) {
		var el = form.querySelector( '[name="' + names[ 0 ] + '"]' );
		if ( el ) el.focus();
	}

	/* ---------- Short "start" form: ZIP only ---------- */
	document.querySelectorAll( '[data-fbl-start]' ).forEach( function ( form ) {
		var started = false;
		form.addEventListener( 'input', function () {
			if ( ! started ) { started = true; dl( { event: 'fbl_form_start', form: 'start' } ); }
		} );
		form.addEventListener( 'submit', function ( e ) {
			var bad = checkStep( form, form );
			if ( bad.length ) {
				e.preventDefault();
				dl( { event: 'fbl_form_error', step: 1, fields: bad.join( ',' ) } );
				focusFirstInvalid( form, bad );
				return;
			}
			dl( { event: 'fbl_form_step', step: 1, step_name: 'zip' } );
		} );
	} );

	/* ---------- Full form: one step at a time ---------- */
	document.querySelectorAll( '[data-fbl-steps]' ).forEach( function ( form ) {
		var steps = Array.prototype.slice.call( form.querySelectorAll( '.fbl-step' ) );
		var total = steps.length;
		var back = form.querySelector( '[data-fbl-back]' );
		var next = form.querySelector( '[data-fbl-next]' );
		var submit = form.querySelector( '[data-fbl-submit]' );
		var consent = form.querySelector( '[data-fbl-consent]' );
		var progress = form.querySelector( '[data-fbl-progress]' );
		var progressText = form.querySelector( '[data-fbl-progress-text]' );
		var bar = form.querySelector( '[data-fbl-bar]' );
		var current = 0;
		var started = false;

		// Fill the hidden attribution fields.
		form.querySelectorAll( '[data-fbl-track]' ).forEach( function ( input ) {
			input.value = source[ input.getAttribute( 'data-fbl-track' ) ] || '';
		} );

		form.classList.add( 'is-stepped' );
		progress.hidden = false;
		next.hidden = false;

		function show( index, moveFocus ) {
			current = index;
			steps.forEach( function ( s, i ) { s.hidden = i !== index; } );
			var last = index === total - 1;
			back.hidden = index === 0;
			next.hidden = last;
			submit.hidden = ! last;
			consent.hidden = ! last;
			progressText.textContent = 'Step ' + ( index + 1 ) + ' of ' + total;
			bar.style.width = ( ( index + 1 ) / total * 100 ) + '%';
			if ( moveFocus ) steps[ index ].focus( { preventScroll: true } );
			if ( moveFocus && form.getBoundingClientRect().top < 0 ) form.scrollIntoView( { behavior: 'smooth', block: 'start' } );
		}

		function goNext() {
			var bad = checkStep( form, steps[ current ] );
			if ( bad.length ) {
				dl( { event: 'fbl_form_error', step: current + 1, fields: bad.join( ',' ) } );
				focusFirstInvalid( form, bad );
				return;
			}
			dl( { event: 'fbl_form_step', step: current + 1, step_name: steps[ current ].getAttribute( 'data-name' ) } );
			show( Math.min( current + 1, total - 1 ), true );
		}

		next.addEventListener( 'click', goNext );
		back.addEventListener( 'click', function () { show( Math.max( current - 1, 0 ), true ); } );

		// Enter in any field goes to the next step instead of submitting early.
		form.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Enter' && e.target.tagName === 'INPUT' && current < total - 1 ) {
				e.preventDefault();
				goNext();
			}
		} );

		form.addEventListener( 'input', function ( e ) {
			if ( ! started ) { started = true; dl( { event: 'fbl_form_start', form: 'full' } ); }
			if ( e.target.getAttribute( 'aria-invalid' ) === 'true' && fieldValid( e.target, form ) ) showError( form, e.target.name, false );
		} );

		// Picking a choice card moves on after a short pause, so the choice is visible first.
		// Mouse and touch only: keyboard users move between choices with arrow keys, which also fires "change".
		var pointer = false;
		form.addEventListener( 'pointerdown', function () { pointer = true; } );
		form.addEventListener( 'keydown', function () { pointer = false; } );
		form.addEventListener( 'change', function ( e ) {
			if ( e.target.type !== 'radio' ) return;
			showError( form, e.target.name, false );
			if ( pointer ) setTimeout( goNext, 250 );
		} );

		form.addEventListener( 'submit', function ( e ) {
			for ( var i = 0; i < total; i++ ) {
				var bad = checkStep( form, steps[ i ] );
				if ( bad.length ) {
					e.preventDefault();
					dl( { event: 'fbl_form_error', step: i + 1, fields: bad.join( ',' ) } );
					show( i, false );
					focusFirstInvalid( form, bad );
					return;
				}
			}
			dl( { event: 'fbl_form_step', step: total, step_name: 'contact' } );
			submit.disabled = true;
			submit.textContent = 'Sending…';
		} );

		// Where to start: the server already worked it out (first step with an error, or step 2 after the short form).
		var start = ( parseInt( form.getAttribute( 'data-start' ), 10 ) || 1 ) - 1;
		var summary = form.querySelector( '[data-fbl-summary]' );
		if ( start === 1 && ! summary ) {
			dl( { event: 'fbl_form_start', form: 'full', from: 'start_form' } );
			started = true;
		}
		show( Math.max( start, 0 ), false );
		if ( summary ) {
			// Focus the error list so screen readers announce it. Again after load, because the #fbl-form jump can take focus away.
			summary.focus();
			window.addEventListener( 'load', function () { setTimeout( function () { summary.focus(); }, 0 ); } );
			// Links in the error summary open the step that holds that field.
			summary.addEventListener( 'click', function ( e ) {
				var a = e.target.closest( 'a' );
				var target = a && document.getElementById( a.getAttribute( 'href' ).slice( 1 ) );
				if ( ! target ) return;
				e.preventDefault();
				show( steps.indexOf( target.closest( '.fbl-step' ) ), false );
				( target.matches( 'fieldset' ) ? target.querySelector( 'input' ) : target ).focus();
			} );
		}

		// Coming back with the browser's Back button: make the submit button usable again.
		window.addEventListener( 'pageshow', function () {
			submit.disabled = false;
			submit.textContent = 'Send my enquiry';
		} );
	} );
} )();
