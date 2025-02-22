// js/frontend.js
jQuery(document).ready(function($) {
    $(document).on('change', '#membership-type', function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price');
        $('#price').val(price);
    });
    $('#gym-registration-form').on('submit', function(e) {
        e.preventDefault();
        $('.btn_wpgym_register').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...')
        var formData = $(this).serialize();
        formData += '&action=register_gym_member&nonce=' + gymMembersFrontend.nonce;

        $.ajax({
            url: gymMembersFrontend.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        // Generic success message
                        $('#wpgym-formRespnse').html(
                            `<div class="alert alert-success" role="alert">${errorMessage}</div>`
                        );
                    }
                } else {
                    let errorMessage = response.data.message || 'An unknown error occurred.';
                    $('#wpgym-formRespnse').html(
                        `<div class="alert alert-danger" role="alert">${errorMessage}</div>`
                    );
                }
            },
            error: function() {
                // Handle AJAX error
                $('#wpgym-formRespnse').html(
                    `<div class="alert alert-danger" role="alert">An unexpected error occurred. Please try again later.</div>`
                );
            },
            complete: function() {

            }
        });
    });

    // Payment Verfication
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const resourcePath = urlParams.get('resourcePath');

    if (resourcePath && id) {
        $.ajax({
            url: gymMembersFrontend.ajax_url,
            type: 'POST',
            data: {
                action: 'verify_payment_and_create_user',
                id: id,
                resourcePath: resourcePath
            },
            success: function(response) {
                if (response.success) {
                    $('#wpgym-formRespnse').html(
                        `<div class="alert alert-success" role="alert">${response.data.message}</div>`
                    ).fadeIn();
                } else {
                    let errorMessage = response.data.message || 'An unknown error occurred.';
                    $('#wpgym-formRespnse').html(
                        `<div class="alert alert-danger" role="alert">${errorMessage}</div>`
                    ).fadeIn();
                }
            },
            error: function(response) {
                $('#wpgym-formRespnse').html(
                    `<div class="alert alert-danger" role="alert">An unexpected error occurred. Please try again later.</div>`
                );
            },
            complete: function() {

            }
        });
    }
});