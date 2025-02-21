<!-- templates/registration-form.php -->
<form id="gym-registration-form">
    <p>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </p>
    <p>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </p>
    <p>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </p>
    <p>
        <label for="membership-type">Membership Type:</label>
        <select id="membership-type" name="membership_type">
            <?php
            $membership_types = $this->get_membership_types();
            foreach ($membership_types as $type) {
                echo '<option value="' . esc_attr($type['id']) . '" data-price="' . esc_attr($type['price']) . '">' . esc_html($type['type']) . ' - $' . esc_html($type['price']) . '</option>';
            }
            ?>
        </select>
    </p>
    <p>
        <label for="price">Price:</label>
        <input type="text" id="price" name="price" readonly>
    </p>
    <p>
        <label for="payment-data">Payment Information:</label>
        <input type="text" id="payment-data" name="payment_data" placeholder="Enter credit card number">
    </p>
    <button type="submit">Register</button>
</form>
<script>
    // js/frontend.js
jQuery(document).ready(function($) {
    $('#gym-registration-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=register_gym_member&nonce=' + gymMembersFrontend.nonce;

        $.ajax({
            url: gymMembersFrontend.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Registration successful!');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});
</script>