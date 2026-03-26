<?php
/**
 * Archive template.
 * Uses subhero + blog listing + newsletter to match News & Media structure.
 */

get_header();

$archive_title = is_category()
    ? single_cat_title('', false)
    : wp_strip_all_tags(get_the_archive_title());
$archive_title = preg_replace('/^\s*Archives?:\s*/i', '', (string) $archive_title);
$archive_desc  = wp_strip_all_tags(get_the_archive_description());
if ($archive_desc === '') {
    $archive_desc = 'Browse updates, stories, and announcements from Sanctuary Runners.';
}
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
        'title'      => $archive_title !== '' ? $archive_title : 'Archive',
        'url'        => '',
        'is_current' => true,
    ],
];

get_template_part('template-parts/hero/subhero', null, [
    'heading'            => $archive_title !== '' ? $archive_title : 'Archive',
    'heading_tag'        => 'h1',
    'content'            => $archive_desc,
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

