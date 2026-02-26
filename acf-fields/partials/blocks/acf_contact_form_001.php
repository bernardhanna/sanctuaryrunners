<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$contact_form_001 = new FieldsBuilder('contact_form_001', [
    'label' => 'Contact Form',
]);

$contact_form_001
    ->addTab('Content')
        ->addText('heading', [
            'label' => 'Form Heading',
            'default_value' => 'Get in touch'
        ])
        ->addSelect('heading_tag', [
            'label' => 'Heading Tag',
            'choices' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'p' => 'Paragraph',
                'span' => 'Span'
            ],
            'default_value' => 'h2',
        ])
        ->addTextarea('description', [
            'label' => 'Form Description',
            'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt.',
            'rows' => 3
        ])
        ->addWysiwyg('form_markup', [
            'label' => 'Form HTML (paste static form here)',
            'instructions' => 'Paste the static HTML form code here.',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'wrapper' => ['class' => 'wp_editor'],
        ])
        ->addUrl('privacy_policy_url', [
            'label' => 'Privacy Policy URL',
            'default_value' => '#'
        ])
        ->addUrl('terms_conditions_url', [
            'label' => 'Terms and Conditions URL',
            'default_value' => '#'
        ])

    ->addTab('Email')
        ->addText('form_name', [
            'label' => 'Internal Form Name',
            'instructions' => 'Saved with each entry & used in email subject.',
            'default_value' => 'Contact Form'
        ])
        ->addText('from_name', [
            'label' => 'From Name (override)',
            'instructions' => 'Optional. Leave empty to use Theme Options.',
        ])
        ->addEmail('from_email', [
            'label' => 'From Email (override)',
            'instructions' => 'Use an address on your domain. Leave empty to use Theme Options.',
        ])
        ->addText('email_to', [
            'label' => 'Send To',
            'instructions' => 'One or more addresses. Separate with commas or semicolons.',
            'placeholder' => 'name@domain.ie, other@domain.ie',
            'default_value' => get_option('admin_email'),
        ])
        ->addText('email_bcc', [
            'label' => 'BCC',
            'instructions' => 'Optional. Separate multiple with commas or semicolons.',
            'placeholder' => 'first@domain.ie; second@domain.ie',
        ])
        ->addText('email_subject', [
            'label' => 'Subject',
            'default_value' => 'Website contact form enquiry'
        ])
        ->addTrueFalse('save_entries_to_db', [
            'label' => 'Save to DB?',
            'ui' => 1,
            'default_value' => 1
        ])

    ->addTab('Autoresponder')
        ->addTrueFalse('enable_autoresponder', [
            'label' => 'Enable?',
            'ui' => 1
        ])
        ->addText('autoresponder_subject', [
            'label' => 'Autoresponder Subject',
            'conditional_logic' => [[['field' => 'enable_autoresponder', 'operator' => '==', 'value' => 1]]],
            'default_value' => 'Thank you for your message'
        ])
        ->addWysiwyg('autoresponder_message', [
            'label' => 'Autoresponder Message',
            'conditional_logic' => [[['field' => 'enable_autoresponder', 'operator' => '==', 'value' => 1]]],
            'wrapper' => ['class' => 'wp_editor'],
            'default_value' => '<p>Thank you for contacting us. We will get back to you as soon as possible.</p>'
        ])

    ->addTab('Design')
        ->addColorPicker('background_color', [
            'label' => 'Section Background Color',
            'default_value' => '#ffffff'
        ])
        ->addColorPicker('form_background_color', [
            'label' => 'Form Background Color',
            'default_value' => '#fef3c7'
        ])

    ->addTab('Layout')
        ->addRepeater('padding_settings', [
            'label' => 'Padding Settings',
            'instructions' => 'Customize padding for different screen sizes.',
            'button_label' => 'Add Padding',
        ])
            ->addSelect('screen_size', [
                'label' => 'Screen Size',
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
                'min' => 0,
                'max' => 20,
                'step' => 0.1,
                'append' => 'rem',
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom',
                'min' => 0,
                'max' => 20,
                'step' => 0.1,
                'append' => 'rem',
            ])
        ->endRepeater();

return $contact_form_001;
