/**
 * Progress bar js
 *
 */

(function($) {
  // Function for controlling form progress,
  function progress() {
    var progressWrapper = $('.js-progress');  // The progress bar wrapper.
    var progress = $('.js-progress-bar'); // The progress "bar"
    var circle = $('.js-progress-circle');  // The progress bar circle
    var page = $('.js-progress-page');  // The page wrapper
    var currentStep = 1;

    var form = $('#node-gs-order-form, #node-gs-order-edit-form').first();
    var steps = $('.form--page', form).length;  // The number of steps in form.

    var validationMessage = $('#validation-message');

    validationMessage.hide();

    var validator = form.validate({
      errorClass: 'form--error',
      errorElement: 'span',
      showErrors: function(errorMap, errorList) {
        if (!this.valid()) {
          validationMessage.show();
        }
      }
    });

    $('.js-form-submit').click(function(event) {
      validator.form();
      if (!validator.valid()) {
        event.preventDefault();
      }
    });

    $('.js-forward').click(function() {
      validator.form();
      if (!validator.valid()) {
        return;
      }

      $('#validation-message').hide();

      // Don't act if we are on last page.
      if (currentStep < steps) {
        // Toggle classes for progress bar.
        progressWrapper.toggleClass('is-step-' + currentStep);
        progress.toggleClass('is-step-' + currentStep);

        // Increase step.
        currentStep++;

        // Add the new step as class.
        progressWrapper.toggleClass('is-step-' + currentStep);
        progress.toggleClass('is-step-' + currentStep);

        // Set hidden classes for all pages.
        page.addClass('is-hidden');

        // Remove hidden class from active page.
        $('.form--page-' + currentStep).toggleClass('is-hidden');

        // Hide/Show buttons.
        $('.js-back').removeClass('is-hidden');
        $('.js-forward').removeClass('is-hidden');

        if (currentStep == 4) {
          $('.js-forward').addClass('is-hidden');
        }

        $('body').animate({
          scrollTop: $("#home").offset().top
        }, 50);
      }
    });

    $('.js-back').click(function() {
      // Don't act if we are on first page.
      if (currentStep > 1) {
        // Toggle classes for progress bar.
        progressWrapper.toggleClass('is-step-' + currentStep);
        progress.toggleClass('is-step-' + currentStep);

        // Decrease step.
        currentStep--;

        // Add the new step as class.
        progressWrapper.toggleClass('is-step-' + currentStep);
        progress.toggleClass('is-step-' + currentStep);

        // Set hidden classes for all pages.
        page.addClass('is-hidden');

        // Remove hidden class from active page.
        $('.form--page-' + currentStep).toggleClass('is-hidden');

        // Hide/Show buttons.
        $('.js-back').removeClass('is-hidden');
        $('.js-forward').removeClass('is-hidden');
        if (currentStep === 1) {
          $('.js-back').addClass('is-hidden');
        }

        $('body').animate({
          scrollTop: $("#home").offset().top
        }, 50);
      }
    });
  }

  /**
   * Disable and require some fields depending on something.
   */
  function updateUI(event) {
    var fieldIds = [
      'edit-field-gs-marketing-account-value',
      'edit-field-gs-ean-0-value',
      'edit-field-gs-debtor-0-value'
    ],
    target = event.target,
    required = target.id === 'edit-field-gs-marketing-account-value' ? !$(target).prop('checked') : !$(target).val();

    fieldIds.forEach(function (id) {
      if (id !== target.id) {
        $('#' + id).prop({
          disabled: !required,
          required: required
        });
      }
    });
  }

  // Start the show.
  $(document).ready(function () {
    $('#edit-field-gs-marketing-account-value').on('change', updateUI)
    $('#edit-field-gs-ean-0-value, #edit-field-gs-debtor-0-value').on('keyup', updateUI);
    updateUI({
      target: document.getElementById('edit-field-gs-marketing-account-value')
    });

    progress();
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

  // Fill in some test data.
  $(document).ready(function() {
    var data = {
      'field_gs_department': 'ITK',
      'field_gs_phone': '12345678',
      'field_gs_contact_person': 'Mikkel Ricky',
      'field_gs_email': 'rimi@aarhus.dk',
      'title': 'Test ' + (new Date()),
      'field_gs_order_lines': [ {
        'quantity': 87,
        'product_type': 'hat'
      }, {
        'quantity': 42,
        'product-type': 'briller',
      }],
      'field_gs_comments': 'Non nobis, Domine, non nobis, sed nomini tuo da gloriam.',
      // 'field_gs_ean': '12345678',
      'field_gs_delivery_department': 'ITK',
      'field_gs_delivery_address': 'Dokk1',
      'field_gs_delivery_zip_code': '8000',
      'field_gs_delivery_city': 'Aarhus C',
      'field_gs_delivery_date': new Date(2100, 01, 01),
      'field_gs_delivery_comments': 'Skynd jer!'
    };
    for (var name in data) {
      var value = data[name];
      var fieldId = 'edit-' + name.replace(/_/g, '-') + '-0-value';
      if (value instanceof Date) {
        fieldId += '-date';
        value = value.getFullYear() + '-' + (value.getMonth() < 10 ? '0' : '') + value.getMonth() + '-' + (value.getDate() < 10 ? '0' : '') + value.getDate();
      }
      $('#' + fieldId).val(value);
    }
    $('#edit-field-gs-order-lines-0-quantity').val(87);
    $('#edit-field-gs-order-lines-0-product-type').val('hat');
    $('#edit-field-gs-order-lines-1-quantity').val(42);
    $('#edit-field-gs-order-lines-1-product-type').val('briller');
  });

})(jQuery);
