<?php
$section_id = 'get-involved-form-' . wp_generate_uuid4();

$heading = get_sub_field('heading') ?: 'Join Sanctuary Runners';
$heading_tag = get_sub_field('heading_tag') ?: 'h2';
$description = get_sub_field('description') ?: 'Fill in the form below to join your nearest Sanctuary Runners group.';

$enable_existing_member_switch = (bool) get_sub_field('enable_existing_member_switch');
$existing_member_info_text = (string) get_sub_field('existing_member_info_text');
$existing_member_trigger_text = trim((string) get_sub_field('existing_member_trigger_text'));
$renewal_heading = get_sub_field('renewal_heading') ?: 'Renew your membership';

$primary_form_name = get_sub_field('primary_form_name') ?: 'Get Involved Form';
$renewal_form_name = get_sub_field('renewal_form_name') ?: 'Existing Member Renewal Form';

$location_fields_version = (string) get_sub_field('location_fields_version');
if ($location_fields_version === '') {
    $location_fields_version = 'ireland';
}

$group_options_raw = (string) get_sub_field('group_options');
$group_options = preg_split('/[\r\n]+/', $group_options_raw);
$group_options = array_values(array_filter(array_map('trim', (array) $group_options)));

$country_options_raw = (string) get_sub_field('country_options');
$country_options = preg_split('/[\r\n,;]+/', $country_options_raw);
$country_options = array_values(array_unique(array_filter(array_map('trim', (array) $country_options))));
if (empty($country_options)) {
    $country_options = ['Ireland', 'United Kingdom', 'Australia', 'United States', 'Canada', 'New Zealand', 'Other'];
}

$privacy_policy_url = get_sub_field('privacy_policy_url') ?: '#';
$terms_conditions_url = get_sub_field('terms_conditions_url') ?: '#';

$background_color = get_sub_field('background_color') ?: '#ffffff';
$form_background_color = get_sub_field('form_background_color') ?: '#fef3c7';

$padding_classes = [''];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = (string) get_sub_field('screen_size');
        $padding_top = (string) get_sub_field('padding_top');
        $padding_bottom = (string) get_sub_field('padding_bottom');
        if ($screen_size !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

$cfg_to = get_sub_field('email_to') ?: get_option('admin_email');
$cfg_bcc = get_sub_field('email_bcc') ?: '';
$cfg_subject = get_sub_field('email_subject') ?: '';
$cfg_from_name = get_sub_field('from_name') ?: '';
$cfg_from_email = get_sub_field('from_email') ?: '';
$enable_autoresponder = (bool) get_sub_field('enable_autoresponder');
$autoresponder_subject = get_sub_field('autoresponder_subject') ?: '';
$autoresponder_message = get_sub_field('autoresponder_message') ?: '';
$save_entries_to_db = (bool) get_sub_field('save_entries_to_db');

$build_hidden_fields = static function ($form_name) use (
    $cfg_to,
    $cfg_bcc,
    $cfg_subject,
    $cfg_from_name,
    $cfg_from_email,
    $enable_autoresponder,
    $autoresponder_subject,
    $autoresponder_message,
    $save_entries_to_db
) {
    $hidden = sprintf(
        '<input type="hidden" name="action" value="theme_form_submit">
        <input type="hidden" name="theme_form_nonce" value="%1$s">
        <input type="hidden" name="_theme_form_id" value="%2$s">
        <input type="hidden" name="_submission_uid" value="%3$s">
        <input type="hidden" name="_theme_form_name" value="%4$s">',
        esc_attr(wp_create_nonce('theme_form_submit')),
        esc_attr(get_row_index()),
        esc_attr(wp_generate_uuid4()),
        esc_attr($form_name)
    );

    if ($save_entries_to_db) {
        $hidden .= '<input type="hidden" name="_theme_save_to_db" value="1">';
    }

    $hidden .= '<input type="hidden" name="_cfg_to" value="' . esc_attr($cfg_to) . '">';
    $hidden .= '<input type="hidden" name="_cfg_bcc" value="' . esc_attr($cfg_bcc) . '">';
    $hidden .= '<input type="hidden" name="_cfg_subject" value="' . esc_attr($cfg_subject) . '">';
    $hidden .= '<input type="hidden" name="_cfg_from_name" value="' . esc_attr($cfg_from_name) . '">';
    $hidden .= '<input type="hidden" name="_cfg_from_email" value="' . esc_attr($cfg_from_email) . '">';

    if ($enable_autoresponder) {
        $hidden .= '<input type="hidden" name="_cfg_auto_enabled" value="1">';
        $hidden .= '<input type="hidden" name="_cfg_auto_subject" value="' . esc_attr($autoresponder_subject) . '">';
        $hidden .= '<input type="hidden" name="_cfg_auto_message" value="' . esc_attr($autoresponder_message) . '">';
    }

    return $hidden;
};

$ireland_counties = [
    'Antrim', 'Armagh', 'Carlow', 'Cavan', 'Clare', 'Cork', 'Derry', 'Donegal',
    'Down', 'Dublin', 'Fermanagh', 'Galway', 'Kerry', 'Kildare', 'Kilkenny', 'Laois',
    'Leitrim', 'Limerick', 'Longford', 'Louth', 'Mayo', 'Meath', 'Monaghan', 'Offaly',
    'Roscommon', 'Sligo', 'Tipperary', 'Tyrone', 'Waterford', 'Westmeath', 'Wexford', 'Wicklow',
];

$australia_states = [
    'New South Wales', 'Victoria', 'Queensland', 'Western Australia',
    'South Australia', 'Tasmania', 'Australian Capital Territory', 'Northern Territory',
];

$region_label = 'City';
$region_name = 'city';
$postal_label = '';
$postal_name = '';
$postal_placeholder = '';
$default_country = '';

if ($location_fields_version === 'ireland') {
    $region_label = 'County';
    $region_name = 'county';
    $postal_label = 'Eircode';
    $postal_name = 'eircode';
    $postal_placeholder = 'Eircode';
    $default_country = 'Ireland';
} elseif ($location_fields_version === 'uk') {
    $region_label = 'County';
    $region_name = 'county';
    $postal_label = 'Postcode';
    $postal_name = 'postcode';
    $postal_placeholder = 'Postcode';
    $default_country = 'United Kingdom';
} elseif ($location_fields_version === 'australia') {
    $region_label = 'State / Territory';
    $region_name = 'state';
    $postal_label = 'Postcode';
    $postal_name = 'postcode';
    $postal_placeholder = 'Postcode';
    $default_country = 'Australia';
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="flex flex-col items-center pt-10 pb-14 mx-auto w-full max-w-[1024px] max-xl:px-5">
        <div class="px-20 py-16 w-full rounded-2xl max-lg:px-5 max-lg:max-w-full" style="background-color: <?php echo esc_attr($form_background_color); ?>;">
            <<?php echo esc_attr($heading_tag); ?> class="text-3xl leading-none text-sky-800 max-lg:max-w-full">
                <?php echo esc_html($heading); ?>
            </<?php echo esc_attr($heading_tag); ?>>

            <?php if (!empty($description)) : ?>
                <p class="mt-4 text-base leading-none text-sky-950 max-lg:max-w-full"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <div class="mt-4" x-data="{ formView: 'main' }">
                <div x-show="formView === 'main'">
                    <form class="w-full" role="form" novalidate aria-labelledby="form-heading" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data" data-theme-form="<?php echo esc_attr(get_row_index()); ?>">
                        <?php echo $build_hidden_fields($primary_form_name); ?>

                        <?php if ($enable_existing_member_switch) : ?>
                            <div class="flex flex-wrap gap-4 items-start mt-0 w-full">
                                <div style="align-items: center; gap: 32px; width: 100%; margin-top: 8px; padding: 16px; border-radius: 8px; border: 1px solid #D2D8EA; background: #E8EBF4; display: flex;">
                                    <label style="font-size: 12px; color: #0f172a;">Are you an existing member of Sanctuary Runners?</label>
                                    <div style="display: flex; align-items: center; gap: 24px;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer;">
                                            <input style="width: 32px; height: 32px;" name="existing_member" type="radio" value="yes">Yes
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer;">
                                            <input style="width: 32px; height: 32px;" name="existing_member" type="radio" value="no" checked>No
                                        </label>
                                    </div>
                                </div>
                                <div style="width: 100%; margin-top: 12px; padding: 16px; border-radius: 8px; border: 1px solid #6FB1BD; background: #CBF3F6; font-size: 14px; line-height: 20px;">
                                    <?php
                                    $text = $existing_member_info_text !== '' ? $existing_member_info_text : 'For existing members complete this form here to renew your membership.';
                                    $needle = $existing_member_trigger_text !== '' ? $existing_member_trigger_text : 'this form here';
                                    $parts = explode($needle, $text, 2);
                                    ?>
                                    <?php if (count($parts) === 2) : ?>
                                        <?php echo esc_html($parts[0]); ?>
                                        <a href="#" class="text-sky-800 font-bold underline" @click.prevent="formView = 'renewal'"><?php echo esc_html($needle); ?></a>
                                        <?php echo esc_html($parts[1]); ?>
                                    <?php else : ?>
                                        <?php echo esc_html($text); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-4 w-full mt-4">
                            <div class="flex-1 min-w-60">
                                <label class="text-xs text-slate-900">Which Sanctuary Runners group would you like to join?*</label>
                                <div class="mt-0">
                                    <select class="w-full p-4 border border-slate-600 rounded bg-white" name="group" required aria-required="true">
                                        <option value="">Select group</option>
                                        <?php foreach ($group_options as $group_option) : ?>
                                            <option value="<?php echo esc_attr($group_option); ?>"><?php echo esc_html($group_option); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="flex-1 min-w-60">
                                <label class="text-xs text-slate-900">Phone number</label>
                                <div class="mt-0"><input class="w-full p-4 border border-slate-600 rounded" type="tel" name="phone" placeholder="+353-86 123 1234"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 mt-4 w-full">
                            <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">First name*</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="first_name" required aria-required="true"></div>
                            <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Last name*</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="last_name" required aria-required="true"></div>
                        </div>

                        <div class="flex flex-wrap gap-4 mt-4 w-full">
                            <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Email address*</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="email" name="email" required aria-required="true"></div>
                            <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Date of birth</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="date_of_birth" placeholder="DD/MM/YYYY"></div>
                        </div>

                        <div class="flex flex-wrap gap-4 mt-4 w-full">
                            <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Address line 1</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="address_line_1"></div>
                            <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Address line 2</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="address_line_2"></div>
                        </div>

                        <div class="flex flex-wrap gap-4 mt-4 w-full">
                            <div class="flex-1 min-w-60">
                                <label class="text-xs text-slate-900"><?php echo esc_html($region_label); ?>*</label>
                                <?php if ($location_fields_version === 'ireland') : ?>
                                    <select class="w-full p-4 border border-slate-600 rounded mt-0" name="<?php echo esc_attr($region_name); ?>" required aria-required="true">
                                        <option value="">Select county</option>
                                        <?php foreach ($ireland_counties as $county) : ?>
                                            <option value="<?php echo esc_attr($county); ?>"><?php echo esc_html($county); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif ($location_fields_version === 'australia') : ?>
                                    <select class="w-full p-4 border border-slate-600 rounded mt-0" name="<?php echo esc_attr($region_name); ?>" required aria-required="true">
                                        <option value="">Select state / territory</option>
                                        <?php foreach ($australia_states as $state) : ?>
                                            <option value="<?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="<?php echo esc_attr($region_name); ?>" placeholder="<?php echo esc_attr($region_label); ?>" required aria-required="true">
                                <?php endif; ?>
                            </div>

                            <div class="flex-1 min-w-60">
                                <label class="text-xs text-slate-900">Country*</label>
                                <select class="w-full p-4 border border-slate-600 rounded mt-0" name="country" required aria-required="true">
                                    <option value="">Select country</option>
                                    <?php foreach ($country_options as $country) : ?>
                                        <option value="<?php echo esc_attr($country); ?>" <?php selected($country, $default_country); ?>><?php echo esc_html($country); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($postal_name !== '') : ?>
                            <div class="flex flex-wrap gap-4 mt-4 w-full">
                                <div class="flex-1 min-w-60">
                                    <label class="text-xs text-slate-900"><?php echo esc_html($postal_label); ?></label>
                                    <input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="<?php echo esc_attr($postal_name); ?>" placeholder="<?php echo esc_attr($postal_placeholder); ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="flex gap-2 items-center mt-4">
                            <input class="w-6 h-6" type="checkbox" name="terms_conditions" required aria-required="true">
                            <label class="text-xs">
                                By submitting this form you agree with the
                                <a class="text-sky-800 font-bold" href="<?php echo esc_url($terms_conditions_url); ?>">Terms and Conditions</a>
                                and
                                <a class="text-sky-800 font-bold" href="<?php echo esc_url($privacy_policy_url); ?>">Privacy Policy</a>.
                            </label>
                        </div>

                        <div class="mt-4 w-full">
                            <h3 style="color: #00628f; font-family: 'Public Sans'; font-size: 18px; font-style: normal; font-weight: bold; line-height: 24px; margin: 0 0 4px 0;">Keeping in touch with you</h3>
                            <p class="text-sm leading-6 text-slate-700" style="margin: 0 0 6px 0;">Please tick the box if you would like to receive updates from Sanctuary Runners.</p>
                            <div class="flex gap-2 items-center mt-0">
                                <input class="w-6 h-6 shrink-0" type="checkbox" name="marketing_opt_in" value="yes">
                                <label class="text-xs">Please tick the box to tell us you are happy to receive emails.</label>
                            </div>
                        </div>

                        <div class="mt-4"><div class="cf-turnstile"></div></div>

                        <button class="mt-4 inline-flex gap-2 justify-center items-center px-6 py-3 text-sm font-bold text-white rounded-[100px] w-full border-[3px] border-[var(--Turquoise-500,#1C959B)] bg-[var(--Blue-SR-400,#008BCC)] hover:bg-[var(--Blue-SR-500,#00628F)] hover:text-white focus:outline-none focus-visible:ring-0 focus-visible:border-[3px] focus-visible:border-[var(--Turquoise-500,#1C959B)] focus-visible:bg-[var(--Blue-SR-500,#00628F)] focus-visible:text-white transition-colors duration-200 btn-primary" type="submit">
                            <span class="self-stretch my-auto">Submit</span>
                        </button>
                    </form>
                </div>

                <?php if ($enable_existing_member_switch) : ?>
                    <div x-show="formView === 'renewal'" x-cloak>
                        <button type="button" class="mb-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-bold text-[#00628F] transition-colors duration-200 hover:bg-[#CBF3F6]" @click="formView = 'main'">Back to main form</button>
                        <h3 class="mb-4 text-2xl font-bold leading-8 text-sky-800"><?php echo esc_html($renewal_heading); ?></h3>

                        <form class="w-full" role="form" novalidate aria-labelledby="form-heading" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data" data-theme-form="<?php echo esc_attr(get_row_index()); ?>">
                            <?php echo $build_hidden_fields($renewal_form_name); ?>
                            <div class="flex flex-wrap gap-4 mt-0 w-full">
                                <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">First name*</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="first_name" required aria-required="true"></div>
                                <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Last name*</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="last_name" required aria-required="true"></div>
                            </div>
                            <div class="flex flex-wrap gap-4 mt-4 w-full">
                                <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Email address*</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="email" name="email" required aria-required="true"></div>
                                <div class="flex-1 min-w-60"><label class="text-xs text-slate-900">Member ID (optional)</label><input class="w-full p-4 border border-slate-600 rounded mt-0" type="text" name="member_id"></div>
                            </div>
                            <div class="flex flex-wrap gap-4 w-full mt-4">
                                <div class="flex-1 min-w-60">
                                    <label class="text-xs text-slate-900">Which Sanctuary Runners group are you renewing with?*</label>
                                    <select class="w-full p-4 border border-slate-600 rounded bg-white" name="group" required aria-required="true">
                                        <option value="">Select group</option>
                                        <?php foreach ($group_options as $group_option) : ?>
                                            <option value="<?php echo esc_attr($group_option); ?>"><?php echo esc_html($group_option); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="flex gap-2 items-center mt-4">
                                <input class="w-6 h-6" type="checkbox" name="terms_conditions" required aria-required="true">
                                <label class="text-xs">
                                    By submitting this form you agree with the
                                    <a class="text-sky-800 font-bold" href="<?php echo esc_url($terms_conditions_url); ?>">Terms and Conditions</a>
                                    and
                                    <a class="text-sky-800 font-bold" href="<?php echo esc_url($privacy_policy_url); ?>">Privacy Policy</a>.
                                </label>
                            </div>
                            <div class="mt-4"><div class="cf-turnstile"></div></div>
                            <button class="mt-4 inline-flex gap-2 justify-center items-center px-6 py-3 text-sm font-bold text-white rounded-[100px] w-full border-[3px] border-[var(--Turquoise-500,#1C959B)] bg-[var(--Blue-SR-400,#008BCC)] hover:bg-[var(--Blue-SR-500,#00628F)] hover:text-white focus:outline-none focus-visible:ring-0 focus-visible:border-[3px] focus-visible:border-[var(--Turquoise-500,#1C959B)] focus-visible:bg-[var(--Blue-SR-500,#00628F)] focus-visible:text-white transition-colors duration-200 btn-primary" type="submit">
                                <span class="self-stretch my-auto">Submit renewal</span>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
#<?php echo esc_attr($section_id); ?> input:not([type="hidden"]),
#<?php echo esc_attr($section_id); ?> select,
#<?php echo esc_attr($section_id); ?> textarea {
    border-radius: 4px !important;
    border: 1px solid var(--Gray-600, #475467) !important;
}

#<?php echo esc_attr($section_id); ?> input:not([type="hidden"])::placeholder,
#<?php echo esc_attr($section_id); ?> textarea::placeholder {
    color: var(--Gray-600, #475467) !important;
    font-family: "Public Sans", sans-serif !important;
    font-size: 14px !important;
    font-style: normal !important;
    font-weight: 400 !important;
    line-height: 20px !important;
    opacity: 1 !important;
}

#<?php echo esc_attr($section_id); ?> label {
    color: var(--Gray-800, #001929) !important;
    font-family: "Public Sans", sans-serif !important;
    font-size: 12px !important;
    font-style: normal !important;
    font-weight: 400 !important;
    line-height: 18px !important;
}
</style>
