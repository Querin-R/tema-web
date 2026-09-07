/**
 * Turns any <textarea class="ren-codemirror"> into a real CodeMirror editor
 * — with line numbers that stay correct even when a long line wraps onto
 * several visual rows (a plain textarea + manual line-counting can't do
 * this reliably; CodeMirror handles it natively via lineWrapping).
 *
 * Usage in a Custom HTML block:
 *   <textarea id="my-input" class="ren-codemirror" data-mode="xml"></textarea>
 * Read the current content from JS with:
 *   document.getElementById('my-input').renCodeMirror.getValue()
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( typeof CodeMirror === 'undefined' ) {
			return;
		}

		var textareas = document.querySelectorAll( 'textarea.ren-codemirror' );

		textareas.forEach( function ( textarea ) {
			var mode = textarea.getAttribute( 'data-mode' ) || 'xml';

			var editor = CodeMirror.fromTextArea( textarea, {
				mode: mode,
				lineNumbers: true,
				lineWrapping: true,
				theme: 'default',
			} );

			// Keep the textarea's own value in sync so any existing code that
			// reads `textarea.value` (rather than the CodeMirror API) still works.
			editor.on( 'change', function () {
				editor.save();
			} );

			// Also expose the editor instance directly on the original
			// <textarea> element, for scripts that prefer to call the
			// CodeMirror API (editor.getValue()) instead of reading .value.
			textarea.renCodeMirror = editor;
		} );
	} );
} )();
