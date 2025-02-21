<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div id="member-modal" style="display:none;">
    <div class="modal-content bg-white p-4 rounded shadow-sm">
        <form id="member-form">
            <input type="hidden" id="user-id" name="user_id">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('gym_members_nonce'); ?>">

            <div class="row mb-3">
                <label for="username" class="col-sm-4 col-form-label"><?= __('Username', 'wpgym') ?></label>
                <div class="col-sm-8">
                    <input type="text" id="username" name="username" class="form-control form-control-lg" required style="min-height: 40px;">
                </div>
            </div>

            <div class="row mb-3">
                <label for="email" class="col-sm-4 col-form-label"><?= __('Email', 'wpgym') ?></label>
                <div class="col-sm-8">
                    <input type="email" id="email" name="email" class="form-control form-control-lg" required style="min-height: 40px;">
                </div>
            </div>

            <div class="row mb-3">
                <label for="password" class="col-sm-4 col-form-label"><?= __('Password', 'wpgym') ?></label>
                <div class="col-sm-8">
                    <input type="password" id="password" name="password" class="form-control form-control-lg" style="min-height: 40px;">
                </div>
            </div>

            <div class="row mb-3">
                <label for="membership-type" class="col-sm-4 col-form-label"><?= __('Membership Type', 'wpgym') ?></label>
                <div class="col-sm-8">
                    <select id="membership-type" name="membership_type" class="form-select form-select-lg" required style="min-height: 40px;">
                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'memberships';
                        $results = $wpdb->get_results("SELECT id, type, price FROM $table_name", ARRAY_A);
                        foreach ($results as $type) {
                            echo '<option value="' . esc_attr($type['id']) . '" data-price="' . esc_attr($type['price']) . '">' . esc_html($type['type']) . ' - $' . esc_html($type['price']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="price" class="col-sm-4 col-form-label"><?= __('Price', 'wpgym') ?></label>
                <div class="col-sm-8">
                    <input type="text" id="price" name="price" class="form-control form-control-lg" readonly style="min-height: 40px;">
                </div>
            </div>

            <div class="row mb-3">
                <label for="status" class="col-sm-4 col-form-label"><?= __('Status', 'wpgym') ?></label>
                <div class="col-sm-8">
                    <select id="status" name="status" class="form-select form-select-lg" required style="min-height: 40px;">
                        <option value="active"><?= __('Active', 'wpgym') ?></option>
                        <option value="inactive"><?= __('Inactive', 'wpgym') ?></option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8 offset-sm-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100" style="min-height: 40px;">
                        <?= __('Save Member', 'wpgym') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    #member-modal {
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

    .modal-content {
        max-width: 600px;
        width: 100%;
        margin: 0 auto;
        padding: 20px;
        border: none;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        animation: slide-down 0.3s ease-out;
    }

    .form-control-lg,
    .form-select-lg {
        padding: 10px 15px;
        font-size: 1rem;
        border: 2px solid #ced4da;
        transition: border-color 0.3s ease-in-out;
        max-width: 100%;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .col-sm-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .col-sm-8 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>