<?php
// === Variables (always use get_sub_field) ===
$heading = get_sub_field('heading') ?: 'Get in touch';
$heading_tag = get_sub_field('heading_tag') ?: 'h2';
$description = get_sub_field('description');
$form_markup = get_sub_field('form_markup', false, false);
$enable_existing_member_form_switch = (bool) get_sub_field('enable_existing_member_form_switch');
$existing_member_form_markup = get_sub_field('existing_member_form_markup', false, false);
$existing_member_trigger_text = trim((string) get_sub_field('existing_member_trigger_text'));
$existing_member_form_name = get_sub_field('existing_member_form_name') ?: 'Existing Member Renewal Form';
$existing_member_email_subject = (string) get_sub_field('existing_member_email_subject');
$autoresponder_include_logo = (bool) get_sub_field('autoresponder_include_logo');
$autoresponder_logo = get_sub_field('autoresponder_logo');
$autoresponder_logo_url = is_array($autoresponder_logo) ? (string) ($autoresponder_logo['url'] ?? '') : '';
$autoresponder_footer_text = (string) get_sub_field('autoresponder_footer_text');
$autoresponder_name_field = (string) get_sub_field('autoresponder_name_field');
$autoresponder_reply_to_email = (string) get_sub_field('autoresponder_reply_to_email');
$location_fields_version = (string) get_sub_field('location_fields_version');
if ($location_fields_version === '') {
    $location_fields_version = 'none';
}
$location_country_options_raw = (string) get_sub_field('location_country_options');
$location_country_options = preg_split('/[\r\n,;]+/', $location_country_options_raw);
$location_country_options = array_values(array_unique(array_filter(array_map('trim', (array) $location_country_options))));
if (empty($location_country_options)) {
    $location_country_options = ['Ireland', 'United Kingdom', 'Australia', 'United States', 'Canada', 'New Zealand', 'Other'];
}
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

// ===== Form plumbing: inject action, nonce, posted mail config, privacy/terms links =====
$prepare_form_markup = static function ($markup, $custom_form_name = '', $custom_subject = '') use (
    $privacy_policy_url,
    $terms_conditions_url,
    $autoresponder_include_logo,
    $autoresponder_logo_url,
    $autoresponder_footer_text,
    $autoresponder_name_field,
    $autoresponder_reply_to_email
) {
    if (!$markup) {
        return '';
    }

    $markup = preg_replace('#<br\s*/?>#i', '', (string) $markup);
    $markup = str_replace(
        '<form',
        sprintf(
            '<form action="%1$s" method="post" enctype="multipart/form-data" data-theme-form="%2$s"',
            esc_url(admin_url('admin-post.php')),
            esc_attr(get_row_index())
        ),
        $markup
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

    $base_form_name = $custom_form_name !== '' ? $custom_form_name : (get_sub_field('form_name') ?: '');
    if ($base_form_name !== '') {
        $hidden .= '<input type="hidden" name="_theme_form_name" value="' . esc_attr($base_form_name) . '">';
    }

    if (get_sub_field('save_entries_to_db')) {
        $hidden .= '<input type="hidden" name="_theme_save_to_db" value="1">';
    }

    $cfg_to = get_sub_field('email_to') ?: get_option('admin_email');
    $cfg_bcc = get_sub_field('email_bcc') ?: '';
    $cfg_subject = get_sub_field('email_subject') ?: '';
    $cfg_from_name = get_sub_field('from_name') ?: '';
    $cfg_from_email = get_sub_field('from_email') ?: '';

    $hidden_cfg = '';
    $hidden_cfg .= '<input type="hidden" name="_cfg_to" value="' . esc_attr($cfg_to) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_bcc" value="' . esc_attr($cfg_bcc) . '">';
    $effective_subject = $custom_subject !== '' ? $custom_subject : $cfg_subject;
    $hidden_cfg .= '<input type="hidden" name="_cfg_subject" value="' . esc_attr($effective_subject) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_from_name" value="' . esc_attr($cfg_from_name) . '">';
    $hidden_cfg .= '<input type="hidden" name="_cfg_from_email" value="' . esc_attr($cfg_from_email) . '">';

    if (get_sub_field('enable_autoresponder')) {
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_enabled" value="1">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_subject" value="' . esc_attr(get_sub_field('autoresponder_subject') ?: '') . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_message" value="' . esc_attr(get_sub_field('autoresponder_message') ?: '') . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_include_logo" value="' . ($autoresponder_include_logo ? '1' : '0') . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_logo_url" value="' . esc_attr($autoresponder_logo_url) . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_footer" value="' . esc_attr($autoresponder_footer_text) . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_name_field" value="' . esc_attr($autoresponder_name_field) . '">';
        $hidden_cfg .= '<input type="hidden" name="_cfg_auto_reply_to" value="' . esc_attr($autoresponder_reply_to_email) . '">';
    }

    $markup = str_replace('</form>', ($hidden . $hidden_cfg) . '</form>', $markup);
    $markup = str_replace('href="#"', 'href="' . esc_url($privacy_policy_url) . '"', $markup);

    if ($terms_conditions_url && $terms_conditions_url !== '#') {
        $privacy_href = 'href="' . esc_url($privacy_policy_url) . '"';
        $terms_href = 'href="' . esc_url($terms_conditions_url) . '"';
        $markup = preg_replace('/' . preg_quote($privacy_href, '/') . '/', $terms_href, $markup, 1);
    }

    return $markup;
};

$form_markup = $prepare_form_markup($form_markup);
$has_existing_member_form = $enable_existing_member_form_switch && !empty($existing_member_form_markup);
$existing_member_form_markup = $has_existing_member_form
    ? $prepare_form_markup($existing_member_form_markup, $existing_member_form_name, $existing_member_email_subject)
    : '';
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    data-location-fields-version="<?php echo esc_attr($location_fields_version); ?>"
>
    <div class="flex flex-col items-center pt-10 pb-14 mx-auto w-full max-w-[1024px] max-xl:px-5">
        <div
            class="px-20 py-6 w-full rounded-2xl lg:py-16 max-lg:px-5 max-lg:max-w-full"
            style="background-color: <?php echo esc_attr($form_background_color); ?>;"
        >
            <?php if ($heading): ?>
                <<?php echo esc_attr($heading_tag); ?>
                    id="form-heading-<?php echo esc_attr(get_row_index()); ?>"
                    class="text-3xl leading-none text-sky-800 max-lg:max-w-full"
                >
                    <?php echo esc_html($heading); ?>
                </<?php echo esc_attr($heading_tag); ?>>
            <?php endif; ?>

            <?php if ($description): ?>
                <p class="mt-4 text-base leading-none text-sky-950 max-lg:max-w-full">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>

            <?php if ($form_markup): ?>
                <?php
                $allowed_form_html = [
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
                    'h1' => ['class' => [], 'id' => [], 'style' => []],
                    'h2' => ['class' => [], 'id' => [], 'style' => []],
                    'h3' => ['class' => [], 'id' => [], 'style' => []],
                    'h4' => ['class' => [], 'id' => [], 'style' => []],
                    'h5' => ['class' => [], 'id' => [], 'style' => []],
                    'h6' => ['class' => [], 'id' => [], 'style' => []],
                    'label' => [
                        'for' => [],
                        'class' => [],
                        'id' => [],
                        'style' => [],
                    ],
                    'span' => ['class' => [], 'id' => [], 'style' => []],
                    'strong' => ['class' => [], 'id' => [], 'style' => []],
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
                        'data-renew-form-trigger' => [],
                    ],
                ];
                ?>
                <div class="mt-4" data-form-switcher>
                    <div data-form-view="primary">
                        <?php echo wp_kses($form_markup, $allowed_form_html); ?>
                    </div>

                    <?php if ($has_existing_member_form): ?>
                        <div class="hidden" data-form-view="renewal">
                            <button
                                type="button"
                                class="mb-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-bold text-[#00628F] transition-colors duration-200 hover:bg-[#CBF3F6]"
                                data-renew-form-back
                            >
                                Back to main form
                            </button>
                            <?php echo wp_kses($existing_member_form_markup, $allowed_form_html); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var section = document.getElementById('<?php echo esc_js($section_id); ?>');
    if (!section) return;
    var locationVersion = section.getAttribute('data-location-fields-version') || 'none';

    var irelandCounties = [
        'Antrim', 'Armagh', 'Carlow', 'Cavan', 'Clare', 'Cork', 'Derry', 'Donegal',
        'Down', 'Dublin', 'Fermanagh', 'Galway', 'Kerry', 'Kildare', 'Kilkenny',
        'Laois', 'Leitrim', 'Limerick', 'Longford', 'Louth', 'Mayo', 'Meath',
        'Monaghan', 'Offaly', 'Roscommon', 'Sligo', 'Tipperary', 'Tyrone',
        'Waterford', 'Westmeath', 'Wexford', 'Wicklow'
    ];
    var australiaStates = [
        'New South Wales', 'Victoria', 'Queensland', 'Western Australia',
        'South Australia', 'Tasmania', 'Australian Capital Territory', 'Northern Territory'
    ];
    var commonCountries = <?php echo wp_json_encode($location_country_options); ?>;

    function findFieldWrapFromLabel(labelEl) {
        if (!labelEl) return null;
        return labelEl.closest('div') || labelEl.parentElement;
    }

    function findByLabelText(formEl, textList) {
        var labels = Array.from(formEl.querySelectorAll('label'));
        var needles = Array.isArray(textList) ? textList : [textList];
        needles = needles.map(function (t) { return String(t).toLowerCase(); });
        return labels.find(function (label) {
            var hay = (label.textContent || '').toLowerCase();
            return needles.some(function (needle) {
                return hay.indexOf(needle) !== -1;
            });
        }) || null;
    }

    function getFieldByKeywords(formEl, keywords) {
        var label = findByLabelText(formEl, keywords);
        var control = null;
        if (label) {
            var forId = label.getAttribute('for');
            if (forId) {
                control = document.getElementById(forId);
            }
            if (!control) {
                var wrap = findFieldWrapFromLabel(label);
                control = wrap ? wrap.querySelector('select, input:not([type="hidden"]), textarea') : null;
            }
        }
        return { label: label, control: control };
    }

    function convertControlToInput(formEl, field, name, labelText, placeholder) {
        if (!field || !field.control) return null;
        var current = field.control;
        var input = document.createElement('input');
        input.type = 'text';
        input.name = name;
        input.id = current.id || (name + '-' + Math.random().toString(36).slice(2, 7));
        input.placeholder = placeholder || '';
        input.className = current.className || 'w-full p-4 border border-slate-600 rounded mt-0';
        if (current.required) {
            input.required = true;
            input.setAttribute('aria-required', 'true');
        }
        if (current.getAttribute('aria-describedby')) {
            input.setAttribute('aria-describedby', current.getAttribute('aria-describedby'));
        }
        current.replaceWith(input);
        if (field.label) {
            var requiredMark = (field.label.textContent || '').indexOf('*') !== -1 ? '*' : '';
            field.label.textContent = labelText + requiredMark;
            field.label.setAttribute('for', input.id);
        }
        return input;
    }

    function convertControlToSelect(formEl, field, name, labelText, options, placeholder) {
        if (!field || !field.control) return null;
        var current = field.control;
        var select = current.tagName && current.tagName.toLowerCase() === 'select'
            ? current
            : document.createElement('select');

        if (select !== current) {
            select.name = name;
            select.id = current.id || (name + '-' + Math.random().toString(36).slice(2, 7));
            select.className = current.className || 'w-full p-4 border border-slate-600 rounded mt-0';
            if (current.required) {
                select.required = true;
                select.setAttribute('aria-required', 'true');
            }
            current.replaceWith(select);
        } else {
            select.name = name;
        }

        select.innerHTML = '';
        var first = document.createElement('option');
        first.value = '';
        first.textContent = placeholder || 'Select';
        first.selected = true;
        first.disabled = false;
        select.appendChild(first);
        options.forEach(function (value) {
            var option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });

        if (field.label) {
            var requiredMark = (field.label.textContent || '').indexOf('*') !== -1 ? '*' : '';
            field.label.textContent = labelText + requiredMark;
            field.label.setAttribute('for', select.id);
        }
        return select;
    }

    function ensureCountrySelect(formEl, defaultCountry) {
        var field = getFieldByKeywords(formEl, ['country']);
        if (!field.control) return;

        var control = field.control;
        var select = control.tagName && control.tagName.toLowerCase() === 'select'
            ? control
            : convertControlToSelect(formEl, field, 'country', 'Country', commonCountries, 'Select country');
        if (!select) return;

        var existingOptions = Array.from(select.options).map(function (o) { return o.value || o.textContent; });
        if (existingOptions.length <= 1) {
            convertControlToSelect(formEl, { label: field.label, control: select }, 'country', 'Country', commonCountries, 'Select country');
            select = field.label && field.label.getAttribute('for')
                ? document.getElementById(field.label.getAttribute('for'))
                : formEl.querySelector('select[name="country"]');
        }
        if (!select) return;

        if (defaultCountry) {
            var target = Array.from(select.options).find(function (o) { return o.value === defaultCountry || o.textContent === defaultCountry; });
            if (target) {
                select.value = target.value;
            } else {
                var extra = document.createElement('option');
                extra.value = defaultCountry;
                extra.textContent = defaultCountry;
                select.appendChild(extra);
                select.value = defaultCountry;
            }
        }
    }

    function ensurePostalField(formEl, fieldName, labelText, placeholder) {
        var existing = formEl.querySelector('[name="' + fieldName + '"]');
        if (existing) return;

        var countryField = getFieldByKeywords(formEl, ['country']);
        if (!countryField.control) return;

        var countryWrap = findFieldWrapFromLabel(countryField.label) || countryField.control.closest('div');
        if (!countryWrap || !countryWrap.parentElement) return;

        var newWrap = countryWrap.cloneNode(false);
        newWrap.className = countryWrap.className;

        var label = document.createElement('label');
        label.className = countryField.label ? countryField.label.className : 'text-xs text-slate-900';
        label.textContent = labelText;

        var controlClass = countryField.control.className || 'w-full p-4 border border-slate-600 rounded mt-0';
        var input = document.createElement('input');
        input.type = 'text';
        input.name = fieldName;
        input.placeholder = placeholder;
        input.className = controlClass;
        input.id = fieldName + '-' + Math.random().toString(36).slice(2, 7);

        var controlHolder = document.createElement('div');
        controlHolder.className = 'mt-0';
        controlHolder.appendChild(input);
        label.setAttribute('for', input.id);

        newWrap.appendChild(label);
        newWrap.appendChild(controlHolder);
        countryWrap.parentElement.appendChild(newWrap);
    }

    function enhanceByVersion(formEl, version) {
        if (!formEl || version === 'none') return;

        var cityField = getFieldByKeywords(formEl, ['city']);
        if (!cityField.control) {
            cityField = getFieldByKeywords(formEl, ['county', 'state']);
        }

        if (version === 'ireland') {
            convertControlToSelect(formEl, cityField, 'county', 'County', irelandCounties, 'Select county');
            ensureCountrySelect(formEl, 'Ireland');
            ensurePostalField(formEl, 'eircode', 'Eircode', 'Eircode');
            return;
        }

        if (version === 'uk') {
            convertControlToInput(formEl, cityField, 'county', 'County', 'County');
            ensureCountrySelect(formEl, 'United Kingdom');
            ensurePostalField(formEl, 'postcode', 'Postcode', 'Postcode');
            return;
        }

        if (version === 'australia') {
            convertControlToSelect(formEl, cityField, 'state', 'State/Territory', australiaStates, 'Select state/territory');
            ensureCountrySelect(formEl, 'Australia');
            ensurePostalField(formEl, 'postcode', 'Postcode', 'Postcode');
            return;
        }

        if (version === 'global') {
            if (cityField.control) {
                if (cityField.control.tagName && cityField.control.tagName.toLowerCase() === 'select') {
                    cityField.control.name = 'city';
                } else {
                    convertControlToInput(formEl, cityField, 'city', 'City', 'City');
                }
                if (cityField.label) {
                    var req = (cityField.label.textContent || '').indexOf('*') !== -1 ? '*' : '';
                    cityField.label.textContent = 'City' + req;
                }
            }
            ensureCountrySelect(formEl, '');
        }
    }

    section.querySelectorAll('form[data-theme-form]').forEach(function (formEl) {
        enhanceByVersion(formEl, locationVersion);
    });

    var triggerNeedle = '<?php echo esc_js(strtolower($existing_member_trigger_text !== '' ? $existing_member_trigger_text : 'this form here')); ?>';
    section.querySelectorAll('[data-form-switcher]').forEach(function (switcher) {
        var primary = switcher.querySelector('[data-form-view="primary"]');
        var renewal = switcher.querySelector('[data-form-view="renewal"]');
        if (!primary || !renewal) return;

        var triggerLinks = Array.from(primary.querySelectorAll('a[data-renew-form-trigger]'));
        if (!triggerLinks.length) {
            triggerLinks = Array.from(primary.querySelectorAll('a')).filter(function (a) {
                return (a.textContent || '').trim().toLowerCase().indexOf(triggerNeedle) !== -1;
            });
        }

        var backBtn = renewal.querySelector('[data-renew-form-back]');

        function showRenewalForm() {
            primary.classList.add('hidden');
            renewal.classList.remove('hidden');
        }
        function showPrimaryForm() {
            renewal.classList.add('hidden');
            primary.classList.remove('hidden');
        }

        triggerLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                showRenewalForm();
            });
        });

        if (backBtn) {
            backBtn.addEventListener('click', function () {
                showPrimaryForm();
            });
        }
    });
});
</script>

<style type="text/css">
#<?php echo esc_attr($section_id); ?> input:not([type="hidden"]),
#<?php echo esc_attr($section_id); ?> select,
#<?php echo esc_attr($section_id); ?> textarea {
    border-radius: 4px !important;
    border: 1px solid var(--Gray-600, #475467) !important;
}

#<?php echo esc_attr($section_id); ?> input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
#<?php echo esc_attr($section_id); ?> select {
    height: 52px !important;
    padding: 0 16px !important;
    font-size: 14px !important;
    line-height: 20px !important;
    box-sizing: border-box !important;
}

#<?php echo esc_attr($section_id); ?> textarea {
    padding: 12px 16px !important;
}

#<?php echo esc_attr($section_id); ?> select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 44px !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 14 14' fill='none'%3E%3Cpath d='M3 5.25L7 9.25L11 5.25' stroke='%23475467' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 14px 14px;
}

#<?php echo esc_attr($section_id); ?> input:focus,
#<?php echo esc_attr($section_id); ?> select:focus,
#<?php echo esc_attr($section_id); ?> textarea:focus {
    outline: none !important;
    border-color: #1C959B !important;
    box-shadow: 0 0 0 2px rgba(28, 149, 155, 0.2) !important;
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

#<?php echo esc_attr($section_id); ?> button[type="submit"] {
    min-height: 52px;
    border-radius: 9999px !important;
}

#<?php echo esc_attr($section_id); ?> a {
    text-underline-offset: 2px;
}
</style>