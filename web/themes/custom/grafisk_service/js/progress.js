/**
 * Progress bar js
 *
 */

(function($) {
  /**
   * Disable and require some fields depending on something.
   */
  function updateAccountUI(event) {
    var marketingAccount = $('#edit-field-gs-marketing-account-value'),
        ean = $('#edit-field-gs-ean-0-value'),
        debtor = $('#edit-field-gs-debtor-0-value');

    ean.prop({
      disabled: false,
      required: true
    });
    debtor.prop({
      disabled: false,
      required: true
    });

    if (marketingAccount.prop('checked')) {
      ean.prop({
        disabled: true,
        required: false
      });
      debtor.prop({
        disabled: true,
        required: false
      });
    } else if (ean.val().trim()) {
      debtor.prop({
        disabled: true,
        required: false
      });
    } else if (debtor.val().trim()) {
      ean.prop({
        disabled: true,
        required: false
      });
    }
  }

  // Start the show.
  $(document).ready(function () {
    $('#edit-field-gs-marketing-account-value').on('change', updateAccountUI)
    $('#edit-field-gs-ean-0-value, #edit-field-gs-debtor-0-value').on('change keyup', updateAccountUI);
    updateAccountUI();

    $(document)
      .on('ajaxStart', function() {
        $('.js-back, .js-forward, .js-form-submit').prop({
          disabled: true
        });
        $('.buttons--container').addClass('file-upload-in-progress');
      })
      .on('ajaxStop', function() {
        $('.js-back, .js-forward, .js-form-submit').prop({
          disabled: false
        });
        $('.buttons--container').removeClass('file-upload-in-progress');
      });
  });

  // Hacks!
  // Clear placeholder for all but first input row in order lines.
  // Make first input row required.
  var inputNames = {};
  $('input[name^="field_gs_order_lines"]').each(function(index, el) {
    var name = el.name.replace(/\[[0-9]+\]/, '');
    if (typeof inputNames[name] === 'undefined') {
      inputNames[name] = el;
      $(el).attr('required', 'required');
    } else {
			$(el).attr('placeholder', '');
		}
	});
})(jQuery);
