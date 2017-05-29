/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The "twigvar" plugin.
 *
 */

'use strict';

( function() {
	CKEDITOR.plugins.add( 'twigvar', {
		requires: 'widget,dialog',
		lang: 'af,ar,az,bg,ca,cs,cy,da,de,de-ch,el,en,en-gb,eo,es,et,eu,fa,fi,fr,fr-ca,gl,he,hr,hu,id,it,ja,km,ko,ku,lv,nb,nl,no,oc,pl,pt,pt-br,ru,si,sk,sl,sq,sv,th,tr,tt,ug,uk,vi,zh,zh-cn', // %REMOVE_LINE_CORE%
		icons: 'twigvar', // %REMOVE_LINE_CORE%
		hidpi: true, // %REMOVE_LINE_CORE%

		onLoad: function() {
			// Register styles for twigvar widget frame.
			CKEDITOR.addCss( '.cke_twigvar{background-color:#ff0}' );
		},

		init: function( editor ) {

			var lang = editor.lang.twigvar;

			// Register dialog.
			CKEDITOR.dialog.add( 'twigvar', this.path + 'dialogs/twigvar.js' );

			// Put ur init code here.
			editor.widgets.add( 'twigvar', {
				// Widget code.
				dialog: 'twigvar',
				pathName: lang.pathName,
				// We need to have wrapping element, otherwise there are issues in
				// add dialog.
				template: '<span class="cke_twigvar"></span>',

				downcast: function() {
					return new CKEDITOR.htmlParser.text( '{{' + this.data.name + '}}' );
				},

				init: function() {
					// Note that twigvar markup characters are stripped for the name.
					this.setData( 'name', this.element.getText().slice( 2, -2 ) );
				},

				data: function() {
					this.element.setText( '{{' + this.data.name + '}}' );
				},

				getLabel: function() {
					return this.editor.lang.widget.label.replace( /%1/, this.data.name + ' ' + this.pathName );
				}
			} );

			editor.ui.addButton && editor.ui.addButton( 'Createtwigvar', {
				label: lang.toolbar,
				command: 'twigvar',
				toolbar: 'insert,5',
				icon: 'twigvar'
			} );
		},

		afterInit: function( editor ) {
			var twigvarReplaceRegex = /\{\{.+\}\}/g;

			editor.dataProcessor.dataFilter.addRules( {
				text: function( text, node ) {
					var dtd = node.parent && CKEDITOR.dtd[ node.parent.name ];

					// Skip the case when twigvar is in elements like <title> or <textarea>
					// but upcast twigvar in custom elements (no DTD).
					if ( dtd && !dtd.span )
						return;

					return text.replace( twigvarReplaceRegex, function( match ) {
						// Creating widget code.
						var widgetWrapper = null,
							innerElement = new CKEDITOR.htmlParser.element( 'span', {
								'class': 'cke_twigvar'
							} );

						// Adds twigvar identifier as innertext.
						innerElement.add( new CKEDITOR.htmlParser.text( match ) );
						widgetWrapper = editor.widgets.wrapElement( innerElement, 'twigvar' );

						// Return outerhtml of widget wrapper so it will be placed
						// as replacement.
						return widgetWrapper.getOuterHtml();
					} );
				}
			} );
		}
	} );

} )();
