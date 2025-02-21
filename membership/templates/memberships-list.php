<div class="wrap">
    <h1>All Memberships</h1>
    <a href="?page=add-new-membership" class="page-title-action">Add New Membership</a>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Title</th>
                <th>Type</th>
                <th>Price</th>
                <th>Signup Fee</th>
                <th>Billing Type</th>
                <th>Dashboard access</th>
                <th>Access hours</th>
                <th>Benefits</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($memberships as $membership): ?>
                <tr>
                    <td><?php echo $membership->id; ?></td>
                    <td><img src="<?php echo esc_url($membership->photo_url); ?>" width="50" height="50" alt="Membership Photo"></td>
                    <td><?php echo esc_html($membership->name); ?></td>
                    <td><?php echo esc_html($membership->type); ?></td>
                    <td><?php echo esc_html($membership->price); ?></td>
                    <td><?php echo esc_html($membership->signup_fee); ?></td>
                    <td><?php echo esc_html($membership->billing_type); ?></td>
                    <td><?php echo esc_html($membership->dashboard_access); ?></td>
                    <td><?php echo esc_html($membership->access_hours); ?></td>
                    <td><?php echo esc_html($membership->benefits); ?></td>
                    <td>
                        <button class="button edit-membership" data-id="<?php echo $membership->id; ?>">Edit</button>
                        <button class="button delete-membership" data-id="<?php echo $membership->id; ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="edit-membership-modal" style="display: none;">
    <div class="edit-membership-content">
        <h2>Edit Membership</h2>
        <form id="edit-membership-form">
    <input type="hidden" id="edit-membership-id" name="id">
    <table class="form-table">
        <tr>
            <th><label for="edit-name">Membership Name</label></th>
            <td><input type="text" id="edit-name" name="name" class="regular-text" required></td>
        </tr>
        <tr>
                    <th><label for="edit-photo_url">Photo</label></th>
                    <td>
                        <input type="text" id="edit-photo_url" name="photo_url" class="regular-text" readonly required>
                        <input type="button" id="edit-upload_photo_button" class="button" value="Upload Photo">
                        <div id="edit-photo_preview"></div>
                    </td>
                </tr>
        <tr>
            <th><label for="edit-type">Membership Type</label></th>
            <td>
                <select id="edit-type" name="type" class="regular-text" required>
                    <option value="Yearly">Yearly</option>
                    <option value="Monthly">Monthly</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="edit-billing_type">Billing Type</label></th>
            <td>
                <select id="edit-billing_type" name="billing_type" class="regular-text" required>
                    <option value="No Recurring">No Recurring</option>
                    <option value="Recurring">Recurring</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="edit-price">Price</label></th>
            <td><input type="text" id="edit-price" name="price" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="edit-dashboard_access">Dashboard Access</label></th>
            <td><input type="text" id="edit-dashboard_access" name="dashboard_access" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="edit-access_hours">Access Hours</label></th>
            <td><input type="text" id="edit-access_hours" name="access_hours" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="edit-benefits">Benefits</label></th>
            <td><textarea id="edit-benefits" name="benefits" class="large-text" rows="5" required></textarea></td>
        </tr>
    </table>
    <p class="submit">
        <input type="submit" name="submit" id="edit-submit" class="button button-primary" value="Update Membership">
        <button type="button" id="edit-cancel" class="button">Cancel</button>
    </p>
</form>

    </div>
</div>