<?php
/**
 * Hero block (ACF flexible layout)
 * template-parts/hero/hero.php
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
$title         = get_sub_field('title');
$heading_tag   = get_sub_field('heading_tag') ?: 'h1';
$description   = get_sub_field('description');
$primary_cta   = get_sub_field('primary_cta');
$secondary_cta = get_sub_field('secondary_cta');

// -------------------------------
// Media
// -------------------------------
$media_type        = get_sub_field('media_type') ?: 'image';
$media_image       = get_sub_field('media_image');
$media_ratio_class = get_sub_field('media_ratio') ?: 'aspect-[16/9]';

$video_source      = get_sub_field('video_source') ?: 'local';
$video_file        = get_sub_field('video_file');
$video_youtube_url = get_sub_field('video_youtube_url');
$video_vimeo_url   = get_sub_field('video_vimeo_url');
$video_poster      = get_sub_field('video_poster');

$video_autoplay    = matrix_hero_acf_bool(get_sub_field('video_autoplay'), true);
$video_muted       = matrix_hero_acf_bool(get_sub_field('video_muted'), true);
$video_loop        = matrix_hero_acf_bool(get_sub_field('video_loop'), true);
$video_playsinline = matrix_hero_acf_bool(get_sub_field('video_playsinline'), true);
$video_controls    = matrix_hero_acf_bool(get_sub_field('video_controls'), false);

$video_embed_url = '';
if ($media_type === 'video') {
    $embed_opts = [
        'autoplay'    => $video_autoplay,
        'mute'        => $video_muted,
        'loop'        => $video_loop,
        'controls'    => $video_controls,
        'playsinline' => $video_playsinline,
    ];
    if ($video_source === 'youtube' && !empty($video_youtube_url)) {
        $yt_id = matrix_hero_parse_youtube_id($video_youtube_url);
        if ($yt_id !== '') {
            $video_embed_url = matrix_hero_youtube_embed_url($yt_id, $embed_opts);
        }
    } elseif ($video_source === 'vimeo' && !empty($video_vimeo_url)) {
        $vm_id = matrix_hero_parse_vimeo_id($video_vimeo_url);
        if ($vm_id !== '') {
            $video_embed_url = matrix_hero_vimeo_embed_url($vm_id, $embed_opts);
        }
    }
}

$video_poster_url = ($video_poster && $media_type === 'video') ? wp_get_attachment_image_url((int) $video_poster, 'full') : '';

// -------------------------------
// Background
// -------------------------------
$bg_color = get_sub_field('background_color') ?: 'rgba(0,157,230,1)';

// -------------------------------
// Title cleanup
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

$hero_iframe_title = $title_inline !== ''
    ? wp_strip_all_tags($title_inline)
    : __('Hero video', 'matrix-starter');
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="overflow-hidden relative min-h-[500px] w-full flex items-center lg:pb-[50px]"
    style="background-color: <?php echo esc_attr($bg_color); ?>;"
    role="banner"
    aria-labelledby="<?php echo esc_attr($title_id); ?>"
>

    <div class="mx-auto w-full max-w-container">

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[35%_70%] w-full max-lg:px-5 py-[2rem] lg:py-0">

            <!-- Text -->
            <header class="flex flex-col order-2 gap-4 self-start pr-5 pl-0 min-w-0 lg:order-1">

                <div class="w-full">

                    <?php if (!empty($kicker)): ?>
                        <p class="text-left text-[1.8125rem] md:text-[2.25rem] font-bold leading-[2.75rem] tracking-[-0.045rem] text-white">
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
                    <?php endif; ?>

                </div>

                <?php if (!empty($description)): ?>
                    <div id="<?php echo esc_attr($desc_id); ?>" class="flex flex-col gap-[10px] text-left lg:max-w-[303px] text-[18px] leading-6 text-white">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if ($primary_cta || $secondary_cta): ?>
                    <div class="flex gap-6 pt-0 md:pt-4 w-full lg:max-w-[400px]  md:justify-start">

                        <?php if ($primary_cta): ?>
                            <a
                                href="<?php echo esc_url($primary_cta['url']); ?>"
                                target="<?php echo esc_attr($primary_cta['target'] ?? '_self'); ?>"
                                class="hero-cta inline-flex items-center justify-center gap-2 bg-white rounded-full px-6 py-4 text-[14px] font-bold text-[#00628F]
                                       hover:bg-[var(--Turquoise-50,#CBF3F6)]
                                       active:bg-[var(--Turquoise-100,#75E0E6)]
                                       focus:outline-none focus-visible:ring-0 focus-visible:border-[3px]
                                       focus-visible:border-[var(--Turquoise-500,#1C959B)]
                                       transition-colors duration-200 whitespace-nowrap"
                            >
                                <?php echo esc_html($primary_cta['title']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($secondary_cta): ?>
                            <a
                                href="<?php echo esc_url($secondary_cta['url']); ?>"
                                target="<?php echo esc_attr($secondary_cta['target'] ?? '_self'); ?>"
                                class="hero-cta inline-flex items-center justify-center gap-2 rounded-full border border-solid border-white px-6 py-4 text-[14px] font-bold text-white
                                       hover:border-[var(--Yellow-100,#FCF4C5)]
                                       active:border-[var(--Base-White,#FFF)] active:bg-[var(--Blue-SR-400,#008BCC)]
                                       focus:outline-none focus-visible:ring-0 focus-visible:border-[3px] focus-visible:border-solid
                                       focus-visible:border-[var(--Turquoise-500,#1C959B)] focus-visible:bg-[var(--Purple-50,#D9CCE4)]
                                       transition-[border-color,background-color,border-width] duration-200 whitespace-nowrap"
                            >
                                <?php echo esc_html($secondary_cta['title']); ?>
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </header>

            <!-- Media -->
            <div class="flex order-1 justify-end min-w-0 lg:order-2">

                <figure class="relative overflow-hidden w-full rounded-lg xl:max-w-[768px] xl:max-h-[512px] <?php echo esc_attr($media_ratio_class); ?>">

                    <?php if ($media_type === 'video'): ?>
                        <?php
                        $video_media_classes = 'absolute inset-0 w-full h-full object-cover';
                        if ($video_source === 'local' && is_array($video_file) && !empty($video_file['url'])) :
                            $mime = !empty($video_file['mime_type']) ? $video_file['mime_type'] : 'video/mp4';
                            ?>
                            <video
                                class="<?php echo esc_attr($video_media_classes); ?>"
                                <?php echo $video_controls ? 'controls' : ''; ?>
                                <?php echo $video_autoplay ? 'autoplay' : ''; ?>
                                <?php echo $video_muted ? 'muted' : ''; ?>
                                <?php echo $video_loop ? 'loop' : ''; ?>
                                <?php echo $video_playsinline ? 'playsinline' : ''; ?>
                                <?php echo $video_poster_url ? 'poster="' . esc_url($video_poster_url) . '"' : ''; ?>
                                preload="metadata"
                            >
                                <source src="<?php echo esc_url($video_file['url']); ?>" type="<?php echo esc_attr($mime); ?>">
                            </video>
                        <?php elseif (($video_source === 'youtube' || $video_source === 'vimeo') && $video_embed_url !== '') : ?>
                            <iframe
                                class="<?php echo esc_attr($video_media_classes); ?>"
                                src="<?php echo esc_url($video_embed_url); ?>"
                                title="<?php echo esc_attr($hero_iframe_title); ?>"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                                loading="eager"
                            ></iframe>
                        <?php elseif (!empty($video_poster_url)) : ?>
                            <img
                                src="<?php echo esc_url($video_poster_url); ?>"
                                alt=""
                                class="<?php echo esc_attr($video_media_classes); ?>"
                                decoding="async"
                            >
                        <?php endif; ?>

                    <?php elseif (!empty($media_image)): ?>
                        <?php echo wp_get_attachment_image($media_image, 'full', false, [
                            'class' => 'w-full h-full object-cover'
                        ]); ?>
                    <?php endif; ?>

                </figure>

            </div>

        </div>

    </div>

</section>