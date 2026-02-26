<?php
// File: inc/acf/options/theme-options/footer.php

use StoutLogic\AcfBuilder\FieldsBuilder;

$fields = new FieldsBuilder('footer', [
    'title'     => 'Footer Settings',
    'menu_slug' => 'theme-footer-settings',
    'post_id'   => 'option',
]);

$fields

        // Branding
        ->addImage('footer_logo', [
            'label' => 'Footer Logo',
            'instructions' => 'Upload the footer logo (SVG/PNG).',
            'return_format' => 'id',
            'preview_size' => 'medium',
        ])

        ->addRepeater('footer_social_links', [
            'label' => 'Social Links',
            'instructions' => 'Adds circular social buttons. Icon is chosen from the list.',
            'button_label' => 'Add Social Link',
            'layout' => 'row',
            'min' => 0,
            'max' => 10,
            'collapsed' => 'label',
        ])
            ->addText('label', [
                'label' => 'Label',
                'instructions' => 'Used for accessibility (e.g. Facebook).',
                'default_value' => 'Facebook',
            ])
            ->addSelect('icon', [
                'label' => 'Icon',
                'choices' => [
                    'bluesky'  => 'Bluesky',
                    'facebook' => 'Facebook',
                    'linkedin' => 'LinkedIn',
                    'instagram'=> 'Instagram',
                ],
                'default_value' => 'facebook',
                'return_format' => 'value',
            ])
            ->addLink('link', [
                'label' => 'Profile Link',
                'return_format' => 'array',
                'required' => 1,
            ])
        ->endRepeater()

        ->addTextarea('footer_reg_text', [
            'label' => 'Registration / Legal Text',
            'instructions' => 'Small text under social icons.',
            'rows' => 3,
            'new_lines' => '',
            'default_value' => 'Registered Charity: CHY23117 | Charity Registration Number: 20206637 | Company Number: 703206 | Tax Registration: 3838051TH',
        ])

        // Column headings (menus come from WP menus)
        ->addText('footer_col1_heading', [
            'label' => 'Column 1 Heading',
            'default_value' => 'About us',
        ])
        ->addText('footer_col2_heading', [
            'label' => 'Column 2 Heading',
            'default_value' => 'Latest',
        ])
        ->addText('footer_col3_heading', [
            'label' => 'Column 3 Heading',
            'default_value' => 'Get involved',
        ])

        // Bottom bar
        ->addText('footer_copyright_left', [
            'label' => 'Bottom Left Text',
            'instructions' => 'Use {year} to insert current year.',
            'default_value' => 'Sanctuary Runners © {year}',
        ])

        ->addText('footer_credit_prefix', [
            'label' => 'Credit Prefix Text',
            'default_value' => 'All Rights Reserved - Designed & Developed by',
        ])
        ->addLink('footer_credit_link', [
            'label' => 'Credit Link',
            'instructions' => 'e.g. Matrix Internet',
            'return_format' => 'array',
        ])

        ->addColorPicker('footer_top_bar_color', [
            'label' => 'Top Bar Color',
            'default_value' => '#E6F4FB', // matches "bg-sr-blue-50" vibe
        ])
        ->addColorPicker('footer_main_bg', [
            'label' => 'Main Background Color',
            'default_value' => '#00263E', // sr-navy vibe
        ])
        ->addColorPicker('footer_bottom_bg', [
            'label' => 'Bottom Background Color',
            'default_value' => '#F0F9FF', // sr-blue-10 vibe
        ])

        ->addRepeater('padding_settings', [
            'label' => 'Padding Settings',
            'instructions' => 'Customize padding for different screen sizes.',
            'button_label' => 'Add Screen Size Padding',
            'layout' => 'table',
            'collapsed' => 'screen_size',
        ])
            ->addSelect('screen_size', [
                'label'   => 'Screen Size',
                'choices' => [
                    'xxs' => 'xxs',
                    'xs' => 'xs',
                    'mob' => 'mob',
                    'sm' => 'sm',
                    'md' => 'md',
                    'lg' => 'lg',
                    'xl' => 'xl',
                    'xxl' => 'xxl',
                    'ultrawide' => 'ultrawide',
                ],
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 3.5,
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'min' => 0, 'max' => 20, 'step' => 0.1, 'append' => 'rem',
                'default_value' => 3.5,
            ])
        ->endRepeater();

return $fields;