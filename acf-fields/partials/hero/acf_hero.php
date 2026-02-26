<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Flexi Layout: Hero
 * Matches template-parts/flexi/hero.php
 */

$hero = new FieldsBuilder('hero');

$hero
    ->addTab('Content')
        ->addText('kicker', [
            'label' => 'Kicker',
            'instructions' => 'Optional line above the title (e.g. "Running as one.")',
        ])
        ->addWysiwyg('title', [
          'label' => 'Title',
          'instructions' => 'Main heading text',
          'default_value' => 'Running as one.',
           'toolbar' => 'basic',
          'media_upload' => 0,
          'tabs' => 'all',
      ])
        ->addSelect('heading_tag', [
            'label' => 'Heading Tag',
            'choices' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
            ],
            'default_value' => 'h1',
            'ui' => 1,
            'return_format' => 'value',
        ])
        ->addWysiwyg('description', [
            'label' => 'Description',
            'instructions' => 'Supports formatting. Output is wp_kses_post().',
            'toolbar' => 'basic',
            'media_upload' => 0,
        ])
        ->addLink('primary_cta', [
            'label' => 'Primary CTA',
            'return_format' => 'array',
        ])
        ->addLink('secondary_cta', [
            'label' => 'Secondary CTA',
            'return_format' => 'array',
        ])

    ->addTab('Media')
        ->addImage('media_image', [
            'label' => 'Media Image',
            'return_format' => 'id', // your template expects an attachment ID
            'preview_size' => 'medium',
            'library' => 'all',
        ])
        ->addText('media_ratio', [
            'label' => 'Media Aspect Ratio Class',
            'instructions' => "Tailwind aspect class, e.g. aspect-[16/9], aspect-square, aspect-[4/3].",
            'default_value' => 'aspect-[16/9]',
        ])

    ->addTab('Background')
        ->addText('background_color', [
            'label' => 'Background Color',
            'instructions' => "CSS color string (supports rgba()) e.g. rgba(0,157,230,1)",
            'default_value' => 'rgba(0,157,230,1)',
        ])
        ->addImage('background_image', [
            'label' => 'Background Image',
            'return_format' => 'array', // your template handles array OR ID
            'preview_size' => 'medium',
            'library' => 'all',
        ])
        ->addTextarea('background_overlay', [
            'label' => 'Background Overlay (optional)',
            'instructions' => "CSS background-image value such as a gradient. Example: linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.35))",
            'rows' => 3,
        ])

    ->addTab('Spacing')
        ->addRepeater('padding_settings', [
            'label' => 'Padding Settings (optional)',
            'instructions' => 'If empty, template defaults to: pt/pb 2.5rem and md:pt 5rem md:pb 3rem.',
            'layout' => 'row',
            'min' => 0,
            'button_label' => 'Add breakpoint padding',
        ])
            ->addSelect('screen_size', [
                'label' => 'Screen Size Prefix',
                'choices' => [
                    'sm'  => 'sm',
                    'md'  => 'md',
                    'lg'  => 'lg',
                    'xl'  => 'xl',
                    '2xl' => '2xl',
                ],
                'ui' => 1,
                'return_format' => 'value',
            ])
            ->addNumber('padding_top', [
                'label' => 'Padding Top (rem)',
                'step' => 0.25,
                'min' => 0,
            ])
            ->addNumber('padding_bottom', [
                'label' => 'Padding Bottom (rem)',
                'step' => 0.25,
                'min' => 0,
            ])
        ->endRepeater();

return $hero;