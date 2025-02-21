<div class="wrap">
    <h1>Add New Membership</h1>
    <a href="?page=membership-manager" class="page-title-action">Back to Membership List</a>
    <form id="add-membership-form" method="post">
        <table class="form-table">
            <tr>
                <th><label for="name">Membership Name</label></th>
                <td><input type="text" id="name" name="name" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="photo_url">Photo</label></th>
                <td>
                    <input type="text" id="photo_url" name="photo_url" class="regular-text" readonly required>
                    <input type="button" id="upload_photo_button" class="button" value="Upload Photo">
                    <div id="photo_preview"></div>
                </td>
            </tr>
            <tr>
                <th><label for="type">Membership Type</label></th>
                <td>
                    <select id="type" name="type" class="regular-text" required>
                        <option value="Yearly">Yearly</option>
                        <option value="Monthly">Monthly</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="billing_type">Billing Type</label></th>
                <td>
                    <select id="billing_type" name="billing_type" class="regular-text" required>
                        <option value="No Recurring">No Recurring</option>
                        <option value="Recurring">Recurring</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="price">Price</label></th>
                <td><input type="text" id="price" name="price" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dashboard_access">Dashboard Access</label></th>
                <td><input type="text" id="dashboard_access" name="dashboard_access" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="access_hours">Access Hours</label></th>
                <td><input type="text" id="access_hours" name="access_hours" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="benefits">Benefits</label></th>
                <td><textarea id="benefits" name="benefits" class="large-text" rows="5" required></textarea></td>
            </tr>
        </table>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('membership-manager-nonce'); ?>">

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Add Membership">
        </p>
    </form>
</div>
