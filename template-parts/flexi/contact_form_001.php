<?php
// === Variables (always use get_sub_field) ===
$heading = get_sub_field('heading') ?: 'Get in touch';
$heading_tag = get_sub_field('heading_tag') ?: 'h2';
$description = get_sub_field('description') ?: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt.';
$form_markup = get_sub_field('form_markup', false, false);
$privacy_policy_url = get_sub_field('privacy_policy_url') ?: '#';
$terms_conditions_url = get_sub_field('terms_conditions_url') ?: '#';

// Background styling
$background_color = get_sub_field('background_color') ?: '#ffffff';
$form_background_color = get_sub_field('form_background_color') ?: '#fef3c7';

// Padding classes
$padding_classes = [''];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = (string) get_sub_field('padding_top');
        $padding_bottom = (string) get_sub_field('padding_bottom');

        if ($screen_size !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// Unique section id
$section_id = 'contact-form-' . esc_attr(wp_generate_uuid4());

// ===== Form plumbing: inject action, nonce, posted mail config, privacy link =====
if ($form_markup) {
    // Keep paragraph and heading tags intact so custom content formatting is preserved
    $form_markup = preg_replace('#<br\s*/?>#i', '', $form_markup);

    $form_markup = str_replace(
        '<form',
        sprintf(
            '<form action="%1$s" method="post" enctype="multipart/form-data" data-theme-form="%2$s"',
            esc_url(admin_url('admin-post.php')),
            esc_attr(get_row_index())
        ),
        $form_markup
    );

    $hidden = sprintf(
        '<input type="hidden" name="action" value="theme_form_submit">
        <input type="hidden" name="theme_form_nonce" value="%1$s">
        <input type="hidden" name="_theme_form_id" value="%2$s">
        <input type="hidden" name="_submission_uid" value="%3$s">',
        esc_attr(wp_create_nonce('theme_form_submit')),
        esc_attr(get_row_index()),
        esc_attr(wp_generate_uuid4())
    );

    if ($name = get_sub_field('form_name')) {
        $hidden .= '<input type="hidden" name="_theme_form_name" value="' . esc_attr($name) . '">';
    }

    if (get_sub_field('save_entries_to_db')) {
        $hidden .= '<input type="hidden" name="_theme_save_to_db" value="1">';
    }

    // Mail config (posted)
    $cfg_to = get_sub_field('email_to') ?: get_option('admin_email');
    $cfg_bcc = get_sub_field('email_bcc') ?: '';
    $cfg_subject = get_sub_field('email_subject') ?: '';
    $cfg_from_name = get_sub_field('from_name') ?: '';
    $cfg_from_email = get_sub_field('from_email') ?: '';

    $hidden_cfg = '';
    $hidden_cfg .= '<input type="hidden" name="_cfg_to" value="' . esc_attr($cfg_to) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_bcc" value="' . esc_attr($cfg_bcc) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_subject" value="' . esc_attr($cfg_subject) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_from_name" value="' . esc_attr($cfg_from_name) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_from_email" value="' . esc_attr($cfg_from_email) . '">';

    if (get_sub_field('enable_autoresponder')) {
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_enabled" value="1">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_subject" value="' . esc_attr(get_sub_field('autoresponder_subject') ?: '') . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_message" value="' . esc_attr(get_sub_field('autoresponder_message') ?: '') . '">';
    }

    $form_markup = str_replace('</form>', ($hidden . $hidden_cfg) . '</form>', $form_markup);

    // Replace privacy policy and terms links
    $form_markup = str_replace('href="#"', 'href="' . esc_url($privacy_policy_url) . '"', $form_markup);

    if ($terms_conditions_url && $terms_conditions_url !== '#') {
        $privacy_href = 'href="' . esc_url($privacy_policy_url) . '"';
        $terms_href = 'href="' . esc_url($terms_conditions_url) . '"';
        $form_markup = preg_replace('/' . preg_quote($privacy_href, '/') . '/', $terms_href, $form_markup, 1);
    }
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="flex flex-col items-center pt-10 pb-14 mx-auto w-full max-w-container max-lg:px-5">
        <div
            class="px-20 py-14 w-full rounded-2xl max-md:px-5 max-md:max-w-full"
            style="background-color: <?php echo esc_attr($form_background_color); ?>;"
        >
            <?php if ($heading): ?>
                <<?php echo esc_attr($heading_tag); ?>
                    id="form-heading-<?php echo esc_attr(get_row_index()); ?>"
                    class="text-3xl leading-none text-sky-800 max-md:max-w-full"
                >
                    <?php echo esc_html($heading); ?>
                </<?php echo esc_attr($heading_tag); ?>>
            <?php endif; ?>

            <?php if ($description): ?>
                <p class="mt-4 text-base leading-none text-sky-950 max-md:max-w-full">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>

            <?php if ($form_markup): ?>
                <div class="mt-4">
                    <?php
                    echo wp_kses(
                        $form_markup,
                        [
                            'form' => [
                                'class' => [],
                                'role' => [],
                                'aria-labelledby' => [],
                                'novalidate' => [],
                                'action' => [],
                                'method' => [],
                                'enctype' => [],
                                'data-theme-form' => [],
                                'style' => [],
                            ],
                            'div' => [
                                'class' => [],
                                'id' => [],
                                'role' => [],
                                'aria-live' => [],
                                'aria-describedby' => [],
                                'style' => [],
                                'data-lastpass-icon-root' => [],
                            ],
                            'p' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'h1' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'h2' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'h3' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'h4' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'h5' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'h6' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'label' => [
                                'for' => [],
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'span' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'strong' => [
                                'class' => [],
                                'id' => [],
                                'style' => [],
                            ],
                            'input' => [
                                'type' => [],
                                'id' => [],
                                'name' => [],
                                'placeholder' => [],
                                'required' => [],
                                'aria-required' => [],
                                'aria-describedby' => [],
                                'autocomplete' => [],
                                'class' => [],
                                'value' => [],
                                'checked' => [],
                                'style' => [],
                            ],
                            'select' => [
                                'id' => [],
                                'name' => [],
                                'required' => [],
                                'aria-required' => [],
                                'aria-describedby' => [],
                                'class' => [],
                                'style' => [],
                            ],
                            'option' => [
                                'value' => [],
                                'selected' => [],
                                'disabled' => [],
                            ],
                            'textarea' => [
                                'id' => [],
                                'name' => [],
                                'placeholder' => [],
                                'required' => [],
                                'aria-required' => [],
                                'aria-describedby' => [],
                                'rows' => [],
                                'class' => [],
                                'style' => [],
                            ],
                            'button' => [
                                'type' => [],
                                'class' => [],
                                'aria-describedby' => [],
                                'style' => [],
                            ],
                            'svg' => [
                                'class' => [],
                                'fill' => [],
                                'stroke' => [],
                                'viewBox' => [],
                                'xmlns' => [],
                                'aria-hidden' => [],
                                'width' => [],
                                'height' => [],
                                'style' => [],
                            ],
                            'path' => [
                                'stroke-linecap' => [],
                                'stroke-linejoin' => [],
                                'stroke-width' => [],
                                'd' => [],
                                'stroke' => [],
                                'style' => [],
                            ],
                            'a' => [
                                'href' => [],
                                'class' => [],
                                'target' => [],
                                'aria-label' => [],
                                'style' => [],
                            ],
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style type="text/css">
    
input,
select,
textarea{
    border: 1px solid #475467 !important;
}

</style>