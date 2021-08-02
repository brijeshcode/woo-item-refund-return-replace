(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );


function addReasonRow(type ='refund') {
		index = jQuery('.'+type+ '-reason-index').val();
		add_to  = '.'+type+'-reasons';
		var pre = '<?= $pre; ?>';
		var js_html = '';
		js_html += '<tr class="'+type+'-'+index+'">';
		js_html += '<td>';
		js_html += '<input type="text" required class="w-100" placeholder="Tag Name" name="<?= $pre; ?>['+type+'][reason]['+index+'][tag]">';
		js_html += '</td>';
		js_html += '<td>';
		js_html += '<textarea  class="w-100" required placeholder=" Reason 1, Reason 2, Reason 3, .... " name="<?= $pre; ?>['+type+'][reason]['+index+'][reasons]"></textarea>';
		js_html += '</td>';
		js_html += '<td>';
		js_html += '<span class="my-btn btn-b-red" onclick="removeMe(\''+type+'\', \''+index+'\')">Delete</span>';
		js_html += '</td>';
		js_html += '</tr>';
		js_html += '';

		jQuery(add_to).append(js_html);
		jQuery('.'+type+ '-reason-index').val(++index);
	}

	function removeMe(type, index) {
		if (confirm('Delete confirm')) {
			jQuery('.'+type+'-'+index).remove();
		}
	}