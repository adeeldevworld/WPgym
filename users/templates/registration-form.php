<!-- templates/registration-form.php -->
<form id="gym-registration-form" class="bg-white p-4 rounded shadow-sm" style="max-width: 500px; margin: 0 auto;">
    <h2 class="text-center mb-4"><?= __('Register for Membership', 'wpgym') ?></h2>
    <input type="hidden" name="redirectURL" value="<?php echo get_permalink();?>">

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
        <option value=""><?= __(' Select Membership Type', 'wpgym') ?></option>
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
        <input type="text" id="price" name="price" class="form-control form-control-lg"  readonly style="min-height: 40px;">
    </div>
   
    <button type="submit" class="btn btn-primary btn-lg w-100 btn_wpgym_register" style="min-height: 40px;">
        <?= __('Register', 'wpgym') ?>
    </button>
</form>
<div id="wpgym-formRespnse"></div>
