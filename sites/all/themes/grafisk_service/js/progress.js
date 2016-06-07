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

  // Start the show.
  $(document).ready(function () {
    progress();
  });

})(jQuery);
