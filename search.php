<?php
/**
 * Search results template.
 * Uses subhero + blog listing + newsletter to match News & Media structure.
 */

get_header();

$query = trim((string) get_search_query());
$heading = $query !== '' ? sprintf('Search results for "%s"', $query) : 'Search results';
$description = $query !== ''
    ? 'Browse posts matching your search term.'
    : 'Use the filters and search to find relevant posts.';

$posts_page_id = (int) get_option('page_for_posts');
$news_media_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/news-and-media/');

$breadcrumbs = [
    [
        'title'      => 'Home',
        'url'        => home_url('/'),
        'is_current' => false,
    ],
    [
        'title'      => 'News & Media',
        'url'        => $news_media_url,
        'is_current' => false,
    ],
    [
        'title'      => $heading,
        'url'        => '',
        'is_current' => true,
    ],
];

get_template_part('template-parts/hero/subhero', null, [
    'heading'            => $heading,
    'heading_tag'        => 'h1',
    'content'            => $description,
    'layout_option'      => 'layout_2',
    'background_color'   => '#EEF6FC',
    'use_white_text'     => false,
    'custom_breadcrumbs' => true,
    'breadcrumbs'        => $breadcrumbs,
    'image'              => null,
    'primary_cta'        => null,
    'secondary_cta'      => null,
]);

get_template_part('template-parts/flexi/blog_listing');
get_template_part('template-parts/flexi/newsletter_001');

get_footer();

