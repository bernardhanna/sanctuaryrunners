<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$navigationFields = new FieldsBuilder('navigation_settings');

$navigationFields
    ->addGroup('navigation_settings_start', [
        'label' => 'Navigation Settings',
    ])
        ->addText('phone_number', [
            'label' => 'Phone Number',
            'instructions' => 'Enter the phone number to display in the header (e.g., +353 1 283 2967)',
            'placeholder' => '+353 1 283 2967',
        ])
        ->addLink('contact_button', [
            'label' => 'Contact Button',
            'instructions' => 'Set contact button link and text',
        ])
        ->addLink('join_us_button', [
            'label' => 'Join Us Button (Mobile Top Row)',
            'instructions' => 'Set mobile top-row Join Us button link and text. Falls back to Contact Button if empty.',
        ])
        ->addLink('donate_button', [
            'label' => 'Donate Button',
            'instructions' => 'Set donate button link and text. Leave empty to hide.',
        ])
        ->addImage('donate_icon', [
            'label' => 'Donate Button Icon',
            'instructions' => 'Optional. Upload a custom icon for the donate button. Recommended size: 16x16px or similar. Leave empty to use the default.',
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'all',
        ])
        ->addColorPicker('donate_button_bg_color', [
            'label' => 'Donate Button Background Color',
            'instructions' => 'Sets background color for the donate button in desktop and mobile nav.',
            'default_value' => '#FBEA5E',
        ])
        ->addColorPicker('donate_button_text_color', [
            'label' => 'Donate Button Text Color',
            'instructions' => 'Sets text/icon color for the donate button in desktop and mobile nav.',
            'default_value' => '#00628F',
        ])
        ->addTrueFalse('show_country_picker', [
            'label' => 'Show Country Picker',
            'instructions' => 'Disable to completely hide the country picker from the header.',
            'default_value' => 1,
            'ui' => 1,
        ])
        ->addRepeater('country_picker_options', [
            'label'        => 'Country Picker Options',
            'instructions' => 'Add custom options for the country picker dropdown in the header. If this has rows, these options are used instead of the default fallback list.',
            'layout'       => 'row',
            'button_label' => 'Add Country Option',
            'min'          => 0,
            'max'          => 20,
        ])
            ->conditional('show_country_picker', '==', '1')
            ->addText('value', [
                'label'        => 'Value',
                'instructions' => 'Unique value/slug used in the option data attribute (e.g., ie, uk, global).',
                'required'     => 1,
                'placeholder'  => 'ie',
            ])
            ->addText('label', [
                'label'        => 'Label',
                'instructions' => 'Visible country/region name.',
                'required'     => 1,
                'placeholder'  => 'Ireland',
            ])
            ->addLink('link', [
                'label'        => 'Link',
                'instructions' => 'Destination URL for this country option.',
            ])
            ->addImage('flag_icon', [
                'label'         => 'Flag Icon (SVG/PNG)',
                'instructions'  => 'Upload a flag icon. SVG is supported when enabled on the site.',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'library'       => 'all',
            ])
        ->endRepeater()

        /*
        |----------------------------------------------------------------------
        |  Dropdown Images - one row per dropdown you want to decorate with an image
        |----------------------------------------------------------------------
        */
        ->addRepeater('dropdown_images', [
            'label'        => 'Dropdown Images',
            'instructions' => 'Add images to display in megamenu dropdowns. Each image will be associated with a specific menu item.',
            'layout'       => 'row',
            'button_label' => 'Add Dropdown Image',
            'min'          => 0,
            'max'          => 10,
        ])
            ->addSelect('menu_item', [
                'label'       => 'Attach to menu item',
                'instructions' => 'Select which menu item this image should appear with in the dropdown',
                'choices'     => [],      // filled dynamically (see hook below)
                'ui'          => 1,
                'allow_null'  => 0,
                'required'    => 1,
            ])
            ->addImage('image', [
                'label'         => 'Dropdown Image',
                'instructions'  => 'Upload an image to display in the megamenu dropdown. Recommended size: 563x280px (aspect ratio 2:1)',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'required'      => 1,
            ])
        ->endRepeater()

    ->addAccordion('navigation_settings_end')->endpoint();

/**
 * Populate "Attach to menu item" with top-level primary menu items only (megamenu parents).
 * Runs at priority 20 so it runs after the default menu_item choices and overrides for this field.
 */
add_filter('acf/load_field/name=menu_item', function ($field) {
    $field['choices'] = [];
    $locations = get_nav_menu_locations();
    if (empty($locations['primary'])) {
        $field['choices'][''] = __('No primary menu found', 'matrix-starter');
        return $field;
    }
    $items = wp_get_nav_menu_items($locations['primary'], ['update_post_term_cache' => false]);
    if (!$items) {
        $field['choices'][''] = __('No menu items found', 'matrix-starter');
        return $field;
    }
    foreach ($items as $item) {
        if ((int) $item->menu_item_parent === 0) {
            $field['choices'][$item->ID] = $item->title;
        }
    }
    asort($field['choices']);
    return $field;
}, 20);

return $navigationFields;
