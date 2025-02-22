<div class="gym-memberships-container">
    <div class="membership-toggle">
        <button class="toggle-btn active" data-duration="monthly">Monthly</button>
        <button class="toggle-btn" data-duration="yearly">Yearly</button>
    </div>

    <div class="memberships-list">
        <?php foreach ($memberships as $membership): ?>
            <div class="membership-item" data-duration="<?php echo strtolower($membership->type); ?>">
                <h3><?php echo esc_html($membership->name); ?></h3>
                <p class="price">$<?php echo number_format($membership->price, 2); ?></p>
                <p class="price"><?php echo ($membership->billing_type); ?></p>
                <p class="price"><?php echo ($membership->dashboard_access); ?></p>
                <p class="duration"><?php echo strtolower($membership->type); ?></p>
                <p class="duration"><?php echo $membership->benefits; ?></p>
                <button class="join-btn" data-price="<?php echo esc_attr($membership->price); ?>" data-id="<?php echo esc_attr($membership->id); ?>">Join Now</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<div id="registration-modal" style="display:none;">
    <!-- templates/registration-form.php -->
    <form id="gym-registration-form" class="bg-white p-4 rounded shadow-sm" style="max-width: 500px; margin: 0 auto;">
        <h2 class="text-center mb-4"><?= __('Register for Membership', 'wpgym') ?></h2>
        <input type="hidden" name="redirectURL" value="<?php echo get_permalink(); ?>">

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
            <input type="text" id="membership_type" class="form-control form-control-lg" name="membership_type" readonly>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label"><?= __('Price', 'wpgym') ?></label>
            <input type="text" id="price" name="price" class="form-control form-control-lg" readonly style="min-height: 40px;">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 btn_wpgym_register" style="min-height: 40px;">
            <?= __('Register', 'wpgym') ?>
        </button>
    </form>
    <div id="wpgym-formRespnse"></div>


</div>