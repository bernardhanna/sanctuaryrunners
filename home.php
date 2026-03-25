<?php
/**
 * Posts index template (blog home).
 * Uses subhero + blog listing + newsletter to match News & Media structure.
 */

get_header();

$breadcrumbs = [
    [
        'title'      => 'Home',
        'url'        => home_url('/'),
        'is_current' => false,
    ],
    [
        'title'      => 'News & Media',
        'url'        => '',
        'is_current' => true,
    ],
];

get_template_part('template-parts/hero/subhero', null, [
    'heading'          => 'News & Media',
    'heading_tag'      => 'h1',
    'content'          => 'Latest updates, stories, and announcements from Sanctuary Runners.',
    'layout_option'    => 'layout_2',
    'background_color' => '#EEF6FC',
    'use_white_text'   => false,
    'custom_breadcrumbs' => true,
    'breadcrumbs'      => $breadcrumbs,
    'image'            => null,
    'primary_cta'      => null,
    'secondary_cta'    => null,
]);

get_template_part('template-parts/flexi/blog_listing');
get_template_part('template-parts/flexi/newsletter_001');

get_footer();

