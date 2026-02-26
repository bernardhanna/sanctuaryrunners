<?php
/**
 * Flexi Block: Hero
 * template-parts/flexi/hero.php
 */

if (!defined('ABSPATH')) exit;

// -------------------------------
// IDs
// -------------------------------
$section_id = 'hero-' . wp_generate_uuid4();
$title_id   = $section_id . '-title';
$desc_id    = $section_id . '-desc';

// -------------------------------
// Content (ACF)
// -------------------------------
$kicker        = get_sub_field('kicker');
$title         = get_sub_field('title');               // may contain HTML (WYSIWYG)
$heading_tag   = get_sub_field('heading_tag') ?: 'h1';
$description   = get_sub_field('description');
$primary_cta   = get_sub_field('primary_cta');
$secondary_cta = get_sub_field('secondary_cta');

// -------------------------------
// Media (ACF)
// -------------------------------
$media_type        = get_sub_field('media_type') ?: 'image'; // image | video_local | video_youtube
$media_image       = get_sub_field('media_image');           // image ID
$media_ratio_class = get_sub_field('media_ratio') ?: 'aspect-[16/9]';

// Local video
$video_file        = get_sub_field('video_file');            // file array (preferred) OR url string
$video_autoplay    = (bool) get_sub_field('video_autoplay');
$video_loop        = (bool) get_sub_field('video_loop');
$video_muted       = (bool) get_sub_field('video_muted');
$video_controls    = (bool) get_sub_field('video_controls');
$video_playsinline = (bool) get_sub_field('video_playsinline');

// YouTube video
$youtube_url       = (string) get_sub_field('youtube_url');

// Poster
$poster_image      = get_sub_field('video_poster');          // image ID (optional)

// Alt
$media_image_alt   = $media_image ? (get_post_meta($media_image, '_wp_attachment_image_alt', true) ?: 'Hero image') : '';

// Resolve local video URL
$video_url = '';
$video_mime = '';
if (is_array($video_file)) {
    $video_url  = (string) ($video_file['url'] ?? '');
    $video_mime = (string) ($video_file['mime_type'] ?? '');
} elseif (is_string($video_file)) {
    $video_url = $video_file;
}

// Resolve poster URL
$poster_url = '';
if ($poster_image) {
    $poster_url = wp_get_attachment_image_url($poster_image, 'full') ?: '';
}

// -------------------------------
// Background (ACF)
// -------------------------------
$bg_color     = get_sub_field('background_color') ?: 'rgba(0,157,230,1)';
$bg_image     = get_sub_field('background_image'); // image ID or array
$bg_image_url = is_array($bg_image)
    ? ($bg_image['url'] ?? '')
    : (is_numeric($bg_image) ? wp_get_attachment_image_url($bg_image, 'full') : '');
$bg_overlay   = get_sub_field('background_overlay');

// -------------------------------
// Padding settings (optional repeater)
// -------------------------------
$padding_classes = ['max-lg:pt-[7rem]', 'pb-[2.5rem]'];
if (have_rows('padding_settings')) {
    $padding_classes = [];
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = (string) get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        if ($screen_size !== '' && $padding_top !== null)    $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        if ($screen_size !== '' && $padding_bottom !== null) $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

// -------------------------------
// Style string
// -------------------------------
$styles   = [];
$styles[] = "background-color: " . esc_attr($bg_color) . ";";

if ($bg_image_url) {
    if (!empty($bg_overlay)) {
        $styles[] = "background-image: " . esc_attr($bg_overlay) . ", url('" . esc_url($bg_image_url) . "');";
        $styles[] = "background-size: cover, cover;";
        $styles[] = "background-position: center, center;";
        $styles[] = "background-repeat: no-repeat, no-repeat;";
    } else {
        $styles[] = "background-image: url('" . esc_url($bg_image_url) . "');";
        $styles[] = "background-size: cover;";
        $styles[] = "background-position: center;";
        $styles[] = "background-repeat: no-repeat;";
    }
}

$style_attr = implode(' ', $styles);

// -------------------------------
// Title rendering (WYSIWYG-safe inside heading)
// -------------------------------
$allowed_title_tags = [
    'br'     => [],
    'strong' => [],
    'em'     => [],
    'span'   => ['class' => true],
];
$title_inline = '';
if (!empty($title)) {
    $title_clean = preg_replace('#</?p[^>]*>#i', '', (string) $title);
    $title_clean = preg_replace('#</?div[^>]*>#i', '', (string) $title_clean);
    $title_clean = preg_replace('#</?h[1-6][^>]*>#i', '', (string) $title_clean);
    $title_inline = trim((string) wp_kses($title_clean, $allowed_title_tags));
}

// -------------------------------
// YouTube helpers
// -------------------------------
function matrix_extract_youtube_id($url) {
    $url = trim((string) $url);
    if ($url === '') return '';
    if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{6,})~', $url, $m)) return $m[1];

    $parts = wp_parse_url($url);
    if (!empty($parts['host']) && (strpos($parts['host'], 'youtube.com') !== false || strpos($parts['host'], 'm.youtube.com') !== false)) {
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $q);
            if (!empty($q['v'])) return (string) $q['v'];
        }
        if (!empty($parts['path']) && preg_match('~^/embed/([a-zA-Z0-9_-]{6,})~', $parts['path'], $m)) return $m[1];
        if (!empty($parts['path']) && preg_match('~^/shorts/([a-zA-Z0-9_-]{6,})~', $parts['path'], $m)) return $m[1];
    }
    return '';
}

$youtube_id = ($media_type === 'video_youtube') ? matrix_extract_youtube_id($youtube_url) : '';
$youtube_embed = '';
if ($youtube_id) {
    $youtube_embed = "https://www.youtube.com/embed/" . rawurlencode($youtube_id)
        . "?autoplay=1&mute=1&loop=1&playlist=" . rawurlencode($youtube_id)
        . "&controls=0&modestbranding=1&rel=0&playsinline=1";

    if (!$poster_url) {
        $poster_url = "https://i.ytimg.com/vi/" . rawurlencode($youtube_id) . "/hqdefault.jpg";
    }
}

// If local video autoplay is requested, force muted+playsinline for best compatibility
if ($media_type === 'video_local' && $video_autoplay) {
    $video_muted = true;
    $video_playsinline = true;
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="overflow-hidden relative min-h-[639px] w-full h-full flex items-end"
    style="<?php echo esc_attr($style_attr); ?>"
    role="banner"
    aria-labelledby="<?php echo esc_attr($title_id); ?>"
    <?php if (!empty($description)): ?>aria-describedby="<?php echo esc_attr($desc_id); ?>"<?php endif; ?>
>
    <div class="mx-auto w-full max-w-container">
        <!-- GRID: 1 col < lg, 30/70 split at lg+ -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[35%_70%] max-lg:px-5 w-full <?php echo esc_attr(implode(' ', $padding_classes)); ?>">

            <!-- Text -->
            <header class="flex flex-col gap-4 self-start px-5 min-w-0 xl:pl-8">
                <div class="w-full">
                    <?php if (!empty($kicker)): ?>
                        <p class="text-left text-[1.8125rem] md:text-[2.25rem] font-bold leading-[2.75rem] tracking-[-0.125rem] text-white">
                            <?php echo esc_html($kicker); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($title_inline)): ?>
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($title_id); ?>"
                            class="text-left text-[2.25rem] font-light leading-[2.75rem] tracking-[-0.125rem] text-white"
                        >
                            <?php echo $title_inline; ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php else: ?>
                        <h1 id="<?php echo esc_attr($title_id); ?>" class="sr-only">
                            <?php echo esc_html__('Hero section', 'matrix-starter'); ?>
                        </h1>
                    <?php endif; ?>
                </div>

                <?php if (!empty($description)): ?>
                    <div id="<?php echo esc_attr($desc_id); ?>" class="text-left text-[1.125rem] lg:max-w-[303px] w-full font-normal leading-6 text-[#f9fafb] wp_editor">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if (
                    ($primary_cta && is_array($primary_cta) && !empty($primary_cta['url']) && !empty($primary_cta['title'])) ||
                    ($secondary_cta && is_array($secondary_cta) && !empty($secondary_cta['url']) && !empty($secondary_cta['title']))
                ): ?>
                    <div class="grid grid-cols-1 gap-4 pt-4 w-full max-w-[400px] md:grid-cols-2 max-xl:pb-4" role="group" aria-label="<?php echo esc_attr__('Hero calls to action', 'matrix-starter'); ?>">
                        <?php if ($primary_cta && is_array($primary_cta) && !empty($primary_cta['url']) && !empty($primary_cta['title'])): ?>
                            <a
                                href="<?php echo esc_url($primary_cta['url']); ?>"
                                target="<?php echo esc_attr($primary_cta['target'] ?? '_self'); ?>"
                                class="inline-flex w-fit justify-center items-center gap-2 bg-white rounded-full text-center px-6 py-4 text-[0.875rem] leading-5 font-bold text-primary hover:opacity-90 transition-opacity duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/80 whitespace-nowrap"
                                aria-label="<?php echo esc_attr($primary_cta['title']); ?>"
                            >
                                <?php echo esc_html($primary_cta['title']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($secondary_cta && is_array($secondary_cta) && !empty($secondary_cta['url']) && !empty($secondary_cta['title'])): ?>
                            <a
                                href="<?php echo esc_url($secondary_cta['url']); ?>"
                                target="<?php echo esc_attr($secondary_cta['target'] ?? '_self'); ?>"
                                class="inline-flex justify-center text-center items-center gap-2 border border-white rounded-full px-6 py-4 text-[0.875rem] leading-5 font-bold text-white hover:opacity-90 transition-opacity duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/80 w-fit whitespace-nowrap"
                                aria-label="<?php echo esc_attr($secondary_cta['title']); ?>"
                            >
                                <?php echo esc_html($secondary_cta['title']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Media -->
            <div class="flex justify-end min-w-0">
                <figure class="relative overflow-hidden w-full h-auto rounded-lg xl:max-w-[768px] xl:max-h-[512px] <?php echo esc_attr($media_ratio_class); ?>">

                    <?php if ($media_type === 'video_local' && !empty($video_url)): ?>
                        <video
                            class="w-full h-full object-cover rounded-[inherit]"
                            <?php if ($poster_url): ?>poster="<?php echo esc_url($poster_url); ?>"<?php endif; ?>
                            <?php if ($video_autoplay): ?>autoplay<?php endif; ?>
                            <?php if ($video_loop): ?>loop<?php endif; ?>
                            <?php if ($video_muted): ?>muted<?php endif; ?>
                            <?php if ($video_controls): ?>controls<?php endif; ?>
                            <?php if ($video_playsinline): ?>playsinline<?php endif; ?>
                            preload="metadata"
                        >
                            <source src="<?php echo esc_url($video_url); ?>" <?php if ($video_mime): ?>type="<?php echo esc_attr($video_mime); ?>"<?php endif; ?>>
                        </video>

                    <?php elseif ($media_type === 'video_youtube' && !empty($youtube_embed)): ?>
                        <?php if ($poster_url): ?>
                            <div class="absolute inset-0">
                                <img
                                    src="<?php echo esc_url($poster_url); ?>"
                                    alt=""
                                    class="object-cover w-full h-full"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                        <?php endif; ?>

                        <iframe
                            class="absolute inset-0 w-full h-full"
                            src="<?php echo esc_url($youtube_embed); ?>"
                            title="<?php echo esc_attr__('Hero video', 'matrix-starter'); ?>"
                            frameborder="0"
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowfullscreen
                        ></iframe>

                    <?php elseif (!empty($media_image)): ?>
                        <?php echo wp_get_attachment_image($media_image, 'full', false, [
                            'alt'      => esc_attr($media_image_alt),
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                            'class'    => 'w-full h-full object-cover rounded-[inherit]',
                        ]); ?>

                    <?php else: ?>
                        <div class="w-full h-full bg-white/10 rounded-[inherit]" aria-hidden="true"></div>
                    <?php endif; ?>

                </figure>
            </div>

        </div>
    </div>
</section>