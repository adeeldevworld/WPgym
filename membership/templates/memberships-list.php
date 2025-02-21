<div class="wrap">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0"><?= __('All Memberships', 'wpgym') ?></h1>
        <a href="?page=add-new-membership" class="btn btn-primary btn-md">
            <?= __('Add New Membership', 'wpgym') ?>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col" class="text-center"><?= __('ID', 'wpgym') ?></th>
                    <th scope="col" class="text-center"><?= __('Photo', 'wpgym') ?></th>
                    <th scope="col"><?= __('Title', 'wpgym') ?></th>
                    <th scope="col"><?= __('Type', 'wpgym') ?></th>
                    <th scope="col" class="text-end"><?= __('Price', 'wpgym') ?></th>
                    <!-- <th scope="col" class="text-end"><?= __('Signup Fee', 'wpgym') ?></th> -->
                    <th scope="col"><?= __('Billing Type', 'wpgym') ?></th>
                    <th scope="col"><?= __('Dashboard Access', 'wpgym') ?></th>
                    <th scope="col"><?= __('Access Hours', 'wpgym') ?></th>
                    <th scope="col"><?= __('Benefits', 'wpgym') ?></th>
                    <th scope="col" class="text-center"><?= __('Actions', 'wpgym') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($memberships as $membership): ?>
                    <tr>
                        <td class="text-center"><?= esc_html($membership->id) ?></td>
                        <td class="text-center">
                            <img src="<?= esc_url($membership->photo_url) ?>" width="50" height="50" class="rounded-circle" alt="<?= __('Membership Photo', 'wpgym') ?>">
                        </td>
                        <td><?= esc_html($membership->name) ?></td>
                        <td><?= esc_html($membership->type) ?></td>
                        <td class="text-end"><?= esc_html($membership->price) ?></td>
                        <!-- <td class="text-end"><?= esc_html($membership->signup_fee) ?></td> -->
                        <td><?= esc_html($membership->billing_type) ?></td>
                        <td><?= esc_html($membership->dashboard_access) ?></td>
                        <td><?= esc_html($membership->access_hours) ?></td>
                        <td><?= esc_html($membership->benefits) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-2 edit-membership" data-id="<?= esc_attr($membership->id) ?>"><?= __('Edit', 'wpgym') ?></button>
                            <button class="btn btn-sm btn-danger delete-membership" data-id="<?= esc_attr($membership->id) ?>"><?= __('Delete', 'wpgym') ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="edit-membership-modal" style="display: none;">
    <div class="edit-membership-content">
        <div class="modal-overlay">
            <div class="modal-dialog modal-dialog-centered" style=" width:100% ;max-width: 800px; margin: 50px auto;">
                <div class="modal-content shadow" style="background-color: #ffffff; border: none;">
                    <div class="modal-header border-bottom-0">
                        <h2 class="modal-title"><?= __('Edit Membership', 'wpgym') ?></h2>
                        <button type="button" class="btn-close btn-close-dark" aria-label="Close" id="edit-cancel"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <form id="edit-membership-form">
                            <input type="hidden" id="edit-membership-id" name="id">
                            <div class="row mb-3">
                                <label for="edit-name" class="col-sm-4 col-form-label"><?= __('Membership Name', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" id="edit-name" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-photo_url" class="col-sm-4 col-form-label"><?= __('Photo', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" id="edit-photo_url" name="photo_url" class="form-control" readonly required>
                                    <input type="button" id="edit-upload_photo_button" class="btn btn-secondary mt-2" value="<?= __('Upload Photo', 'wpgym') ?>">
                                    <div id="edit-photo_preview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-type" class="col-sm-4 col-form-label"><?= __('Membership Type', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <select id="edit-type" name="type" class="form-select" required>
                                        <option value="Yearly"><?= __('Yearly', 'wpgym') ?></option>
                                        <option value="Monthly"><?= __('Monthly', 'wpgym') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-billing_type" class="col-sm-4 col-form-label"><?= __('Billing Type', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <select id="edit-billing_type" name="billing_type" class="form-select" required>
                                        <option value="No Recurring"><?= __('No Recurring', 'wpgym') ?></option>
                                        <option value="Recurring"><?= __('Recurring', 'wpgym') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-price" class="col-sm-4 col-form-label"><?= __('Price', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" id="edit-price" name="price" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-dashboard_access" class="col-sm-4 col-form-label"><?= __('Dashboard Access', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" id="edit-dashboard_access" name="dashboard_access" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-access_hours" class="col-sm-4 col-form-label"><?= __('Access Hours', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" id="edit-access_hours" name="access_hours" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="edit-benefits" class="col-sm-4 col-form-label"><?= __('Benefits', 'wpgym') ?></label>
                                <div class="col-sm-8">
                                    <textarea id="edit-benefits" name="benefits" class="form-control" rows="5" required></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="submit" form="edit-membership-form" class="btn btn-primary w-100"><?= __('Update Membership', 'wpgym') ?></button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .modal-header {
                padding: 20px;
            }

            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .modal-dialog {
                box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                animation: slide-down 0.3s ease-out;
            }

            .btn-close {
                font-size: 1.25rem;
                color: #000;
                opacity: 1;
                transition: transform 0.2s ease-in-out;
            }

            .btn-close:hover {
                transform: rotate(90deg);
            }

            .modal-body {
                max-height: 70vh;
                overflow-y: auto;
                padding: 20px;
            }

            .modal-body select ,.modal-body input[type="text"]{
                max-width: 100%;
                min-height: 40px;
            }
             @keyframes slide-down {
                from {
                    transform: translateY(-50px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        </style>
    </div>
</div>