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
jQuery(document).ready(function($) {
    // Toggle between monthly and yearly memberships
    $('.toggle-btn').on('click', function() {
        $('.toggle-btn').removeClass('active');
        $(this).addClass('active');
        var duration = $(this).data('duration');
        $('.membership-item').hide();
        $('.membership-item[data-duration="' + duration + '"]').show();
    });

    // Show only monthly memberships by default
    $('.membership-item[data-duration="monthly"]').show();
    $('.membership-item[data-duration="yearly"]').hide();

    // Open registration modal
    $('.join-btn').on('click', function() {
        var membershipId = $(this).data('id');
        var membershipType = $(this).siblings('h3').text();
        var price = $(this).data('price');

        $('#membership_id').val(membershipId);
        $('#membership_type').val(membershipType);
        $('#price').val(price);

        $('#registration-modal').show();
    });

    // Close registration modal
    $('#member-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });

    // Handle registration form submission
    $('#registration-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=register_gym_member';
        formData += '&nonce=' + gymManagementFrontend.nonce;

        $.ajax({
            url: gymManagementFrontend.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Registration successful!');
                    $('#registration-modal').hide();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});