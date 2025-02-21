<!-- templates/registration-form.php -->
<form id="gym-registration-form" class="bg-white p-4 rounded shadow-sm" style="max-width: 500px; margin: 0 auto;">
    <h2 class="text-center mb-4"><?= __('Register for Membership', 'wpgym') ?></h2>
    <input type="hidden" name="redirectURL" value="https://mogym.mu/contact">

    <div class="mb-3">
        <label for="username" class="form-label"><?= __('Username', 'wpgym') ?></label>
        <input type="text" id="username" name="username" class="form-control form-control-lg" required style="min-height: 40px;">
    </div>

    <div class="mb-3">
        <label for="email" class="form-label"><?= __('Email', 'wpgym') ?></label>
        <input type="email" id="email" name="email" class="form-control form-control-lg" required style="min-height: 40px;">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label"><?= __('Password', 'wpgym') ?></label>
        <input type="password" id="password" name="password" class="form-control form-control-lg" required style="min-height: 40px;">
    </div>

    <div class="mb-3">
        <label for="membership-type" class="form-label"><?= __('Membership Type', 'wpgym') ?></label>
        <select id="membership-type" name="membership_type" class="form-select form-select-lg" required style="min-height: 40px;">
            <?php
            $membership_types = $this->get_membership_types();
            foreach ($membership_types as $type) {
                echo '<option value="' . esc_attr($type['id']) . '" data-price="' . esc_attr($type['price']) . '">' . esc_html($type['type']) . ' - $' . esc_html($type['price']) . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label"><?= __('Price', 'wpgym') ?></label>
        <input type="text" id="price" name="price" class="form-control form-control-lg" value="10.00" readonly style="min-height: 40px;">
    </div>

    <div class="mb-3">
        <label for="payment-data" class="form-label"><?= __('Payment Information', 'wpgym') ?></label>
        <input type="text" id="payment-data" name="payment_data" class="form-control form-control-lg" placeholder="<?= __('Enter credit card number', 'wpgym') ?>" style="min-height: 40px;">
    </div>

    <button type="submit" class="btn btn-primary btn-lg w-100 btn_wpgym_register" style="min-height: 40px;">
        <?= __('Register', 'wpgym') ?>
    </button>
</form>
<div id="wpgym-formRespnse"></div>
<script>
    // js/frontend.js
    jQuery(document).ready(function($) {
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
</script>