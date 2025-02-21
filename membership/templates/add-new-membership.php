<div class="wrap">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0"><?= __('Add New Membership', 'wpgym') ?></h1>
        <a href="?page=membership-manager" class="btn btn-outline-secondary btn-sm">
            <?= __('Back to Membership List', 'wpgym') ?>
        </a>
    </div>

    <form id="add-membership-form" method="post" class="bg-white p-4 rounded shadow-sm">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('membership-manager-nonce'); ?>">

        <div class="row mb-3">
            <label for="name" class="col-sm-3 col-form-label"><?= __('Membership Name', 'wpgym') ?></label>
            <div class="col-sm-9">
                <input type="text" id="name" name="name" class="form-control form-control-lg" required style="min-height: 40px;">
            </div>
        </div>

        <div class="row mb-3">
            <label for="photo_url" class="col-sm-3 col-form-label"><?= __('Photo', 'wpgym') ?></label>
            <div class="col-sm-9">
                <input type="text" id="photo_url" name="photo_url" class="form-control form-control-lg" readonly required style="min-height: 40px;">
                <input type="button" id="upload_photo_button" class="btn btn-secondary mt-2" value="<?= __('Upload Photo', 'wpgym') ?>" style="min-height: 40px;">
                <div id="photo_preview" class="mt-2"></div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="type" class="col-sm-3 col-form-label"><?= __('Membership Type', 'wpgym') ?></label>
            <div class="col-sm-9">
                <select id="type" name="type" class="form-select form-select-lg" required style="min-height: 40px;">
                    <option value="Yearly"><?= __('Yearly', 'wpgym') ?></option>
                    <option value="Monthly"><?= __('Monthly', 'wpgym') ?></option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="billing_type" class="col-sm-3 col-form-label"><?= __('Billing Type', 'wpgym') ?></label>
            <div class="col-sm-9">
                <select id="billing_type" name="billing_type" class="form-select form-select-lg" required style="min-height: 40px;">
                    <option value="No Recurring"><?= __('No Recurring', 'wpgym') ?></option>
                    <option value="Recurring"><?= __('Recurring', 'wpgym') ?></option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="price" class="col-sm-3 col-form-label"><?= __('Price', 'wpgym') ?></label>
            <div class="col-sm-9">
                <input type="text" id="price" name="price" class="form-control form-control-lg" required style="min-height: 40px;">
            </div>
        </div>

        <div class="row mb-3">
            <label for="dashboard_access" class="col-sm-3 col-form-label"><?= __('Dashboard Access', 'wpgym') ?></label>
            <div class="col-sm-9">
                <input type="text" id="dashboard_access" name="dashboard_access" class="form-control form-control-lg" required style="min-height: 40px;">
            </div>
        </div>

        <div class="row mb-3">
            <label for="access_hours" class="col-sm-3 col-form-label"><?= __('Access Hours', 'wpgym') ?></label>
            <div class="col-sm-9">
                <input type="text" id="access_hours" name="access_hours" class="form-control form-control-lg" required style="min-height: 40px;">
            </div>
        </div>

        <div class="row mb-3">
            <label for="benefits" class="col-sm-3 col-form-label"><?= __('Benefits', 'wpgym') ?></label>
            <div class="col-sm-9">
                <textarea id="benefits" name="benefits" class="form-control form-control-lg" rows="5" required></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary btn-lg w-100" style="min-height: 40px;">
                    <?= __('Add Membership', 'wpgym') ?>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .wrap {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-control-lg,
    .form-select-lg {
        padding: 10px 15px;
        font-size: 1rem;
        border: 2px solid #ced4da;
        transition: border-color 0.3s ease-in-out;
        max-width: 100% !important;
        min-height: 40px;
    }

    .form-control-lg:focus,
    .form-select-lg:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn-primary {
        background-color: #0d6efd;
        border: none;
        transition: background-color 0.3s ease-in-out;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    @media (max-width: 768px) {
        .col-sm-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .col-sm-9 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>