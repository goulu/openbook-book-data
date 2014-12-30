//this file defines the HTML form and handles the form submit for the OpenBook visual editor button

function openbook_button_validations(booknumber, templatenumber, publisherurl, revisionnumber) {

	if (booknumber == "") {
		alert("A Book Number is required");
		document.getElementById('openbook-booknumber').focus();
		return false;
	}

	if (revisionnumber != "") {			
		if (isNaN(revisionnumber)) {
			alert("Revision must be blank or a number");
			document.getElementById('openbook-revisionnumber').focus();
			return false;
		}
	}
	return true;
}

//ajax handler calls server side OpenBook preview method
function openbook_button_preview(booknumber, templatenumber, publisherurl, revisionnumber) {

	var data = {
		action: 'openbook_action',
		booknumber: booknumber,
		templatenumber: templatenumber,
		publisherurl: publisherurl,
		revisionnumber: revisionnumber
	};

	document.getElementById('openbook-request').style.display = "none";
	document.getElementById('openbook-request').style.visibility = "hidden";
	document.getElementById('openbook-response').innerHTML = "... please wait ...";

	//ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: data,
		success: function(response) { 
			document.getElementById('openbook-response').innerHTML = response; 	
			document.getElementById('openbook-response-div').style.display = "block";
			document.getElementById('openbook-response-div').style.visibility = "visible";
			openBook_toggleButtons();
		},
		async: false,
		cache: false
	}); 	
}

//handles the click event of the reset button
function openbook_reset() {
	document.getElementById('openbook-booknumbertype').value="ISBN";
	document.getElementById('openbook-booknumber').value="";
	document.getElementById('openbook-templatenumber').value="1";
	document.getElementById('openbook-booknumber').value="";
	document.getElementById('openbook-publisherurl').value="";
	document.getElementById('openbook-revisionnumber').value="";
	document.getElementById('openbook-html').checked=true;

	document.getElementById('openbook-request').style.display = "block";
	document.getElementById('openbook-request').style.visibility = "visible";

	document.getElementById('openbook-response').innerHTML="";
	document.getElementById('openbook-response-div').style.display = "none";
	document.getElementById('openbook-response-div').style.visibility = "hidden";			
}

function openBook_toggleButtons() {
	
	if( jQuery( '#openbook-preview' ).is(':visible') ) {
		
		// Hide Preview and Reset
		jQuery( '#openbook-preview' ).hide();
		jQuery( '#openbook-reset' ).hide();
		
		jQuery( '#openbook-back' ).show();
		jQuery( '#openbook-insert' ).show();
		
	} else {
		
		// Hide Back and Insert
		jQuery( '#openbook-back' ).hide();
		jQuery( '#openbook-insert' ).hide();
		
		jQuery( '#openbook-preview' ).show();
		jQuery( '#openbook-reset' ).show();
		
	}
	
}

//handles the click event of the Preview button
function openBook_handlePreview( e ) {
	
	var booknumbertype = document.getElementById('openbook-booknumbertype').value;
	var booknumber = document.getElementById('openbook-booknumber').value;
	var revisionnumber = document.getElementById('openbook-revisionnumber').value;
	var booktypenumber = booknumbertype + ':' + booknumber;
	if (booknumbertype == 'OLID' && revisionnumber != '') { booktypenumber = booktypenumber + '@' + revisionnumber; }
	
	var templatenumber = document.getElementById('openbook-templatenumber').value;
	var shortcodechecked = document.getElementById('openbook-shortcode').checked;
	var publisherurl = document.getElementById('openbook-publisherurl').value;
	
	if (openbook_button_validations(booknumber, templatenumber, publisherurl, revisionnumber)) {				
		openbook_button_preview(booktypenumber, templatenumber, publisherurl, revisionnumber);
	}
	
}

// handles the click event of the back button
function openBook_handleBack( e ) {
	
	document.getElementById('openbook-request').style.display = "block";
	document.getElementById('openbook-request').style.visibility = "visible";
	document.getElementById('openbook-response').innerHTML="";
	document.getElementById('openbook-response-div').style.display = "none";
	document.getElementById('openbook-response-div').style.visibility = "hidden";			
	openBook_toggleButtons();
	
}

//handles the click event of the Insert button
function openBook_handleInsert( e ) {
	
	var booknumbertype = document.getElementById('openbook-booknumbertype').value;
	var booknumber = document.getElementById('openbook-booknumber').value;
	var revisionnumber = document.getElementById('openbook-revisionnumber').value;
	var booktypenumber = booknumbertype + ':' + booknumber;
	if (booknumbertype == 'OLID' && revisionnumber != '') { booktypenumber = booktypenumber + '@' + revisionnumber; }
	
	var templatenumber = document.getElementById('openbook-templatenumber').value;
	var shortcodechecked = document.getElementById('openbook-shortcode').checked;
	var publisherurl = document.getElementById('openbook-publisherurl').value;
	
	if (openbook_button_validations(booknumber, templatenumber, publisherurl, revisionnumber)) {

		var display = '';
		if (shortcodechecked == true) {

			var shortcode = '[openbook';
			shortcode += ' booknumber="' + booktypenumber + '"';
			shortcode += ' templatenumber="' + templatenumber + '"';
			if (publisherurl != '') shortcode += ' publisherurl="' + publisherurl + '"';
			shortcode += ']';	

			display = shortcode;
		}
		else {	
			if (document.getElementById('openbook-response').innerHTML=="") {
				openbook_button_preview(booktypenumber, templatenumber, publisherurl, revisionnumber);	
			}
			display = document.getElementById('openbook-response').innerHTML;
		}

		openbook_reset();
		
		tinyMCE.activeEditor.execCommand('mceInsertContent', 0, display);
		tb_remove(); //closes form
	}
	
}

function openBook_getModalContents() {
	// creates a form to be displayed everytime the button is clicked
	var load_form = jQuery.post(
		ajaxurl,
		{
			action: 'openbook_load_form'
		},
		function( response ) {
			
			var form = jQuery.parseHTML( response );
			form = jQuery( form );
			
			jQuery('#openbook-dialog-body').empty().append( form );
			jQuery( '#openbook-back' ).hide();
			jQuery( '#openbook-insert' ).hide();
			
			var table = form.find('table');

			// handles the change event of the book number dropdown
			form.find('#openbook-booknumbertype').change(function(){
				var booknumbertype = document.getElementById('openbook-booknumbertype').value;
				if (booknumbertype == 'OLID') {
					document.getElementById('openbook-revisionnumber').disabled = false;
				}
				else {
					document.getElementById('openbook-revisionnumber').disabled = true;
					document.getElementById('openbook-revisionnumber').value = '';
				}
			});

		}
	);
	
	return load_form;
	
}


(function() {

	tinymce.PluginManager.add('my_mce_button_openbook', function( editor, url ) {
		editor.addButton('my_mce_button_openbook', {
			title: 'OpenBook',
			image: '../wp-content/plugins/openbook-book-data/libraries/openbook_button.gif',  // path to the button's image
			cmd: 'open_modal'
		});

		editor.addCommand( 'open_modal', function() {
			
			editor.windowManager.open({
				title: 'OpenBook Book Data',
				width: jQuery( window ).width() * 0.7,
				// minus head and foot of dialog box
				height: (jQuery( window ).height() - 36 - 50) * 0.7,
				inline: 1,
				id: 'openbook-dialog',
				buttons: [{
					text: 'Preview',
					id: 'openbook-preview',
					class: 'mce-primary',
					onclick: openBook_handlePreview
				},
				{
					text: 'Reset',
					id: 'openbook-reset',
					class: '',
					onclick: openbook_reset
				},
				{
					text: '<< Back',
					id: 'openbook-back',
					class: '',
					onclick: openBook_handleBack
				},
				{
					text: 'Insert',
					id: 'openbook-insert',
					class: 'mce-primary',
					onclick: function() {
						openBook_handleInsert();
						editor.windowManager.close();
					}
				}]
			});
			
			openBook_getModalContents();
			
		});

	});
	
//	});	
})();
