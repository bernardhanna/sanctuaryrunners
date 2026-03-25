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
            <header class="order-2 lg:order-1 flex flex-col gap-4 self-start pr-5 pl-0 min-w-0">

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
                    <div class="flex gap-6 pt-0 md:pt-4 w-full max-w-[400px] justify-between md:justify-start">

                        <?php if ($primary_cta): ?>
                            <a
                                href="<?php echo esc_url($primary_cta['url']); ?>"
                                target="<?php echo esc_attr($primary_cta['target'] ?? '_self'); ?>"
                                class="inline-flex items-center justify-center gap-2 bg-white rounded-full px-6 py-4 text-[14px] font-bold text-[#00628F]
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
                                class="inline-flex items-center justify-center gap-2 border border-white rounded-full px-6 py-4 text-[14px] font-bold text-white
                                       hover:border-[var(--Yellow-100,#FCF4C5)]
                                       active:border-white active:bg-[var(--Blue-SR-400,#008BCC)]
                                       focus:outline-none focus-visible:ring-0 focus-visible:border-[3px]
                                       focus-visible:border-[var(--Turquoise-500,#1C959B)]
                                       focus-visible:bg-[var(--Purple-50,#D9CCE4)]
                                       transition-colors duration-200 whitespace-nowrap"
                            >
                                <?php echo esc_html($secondary_cta['title']); ?>
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </header>

            <!-- Media -->
            <div class="order-1 lg:order-2 flex justify-end min-w-0">

                <figure class="relative overflow-hidden w-full rounded-lg xl:max-w-[768px] xl:max-h-[512px] <?php echo esc_attr($media_ratio_class); ?>">

                    <?php if (!empty($media_image)): ?>
                        <?php echo wp_get_attachment_image($media_image, 'full', false, [
                            'class' => 'w-full h-full object-cover'
                        ]); ?>
                    <?php endif; ?>

                </figure>

            </div>

        </div>

    </div>

</section>