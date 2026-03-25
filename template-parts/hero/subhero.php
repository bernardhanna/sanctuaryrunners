<?php
// Get ACF fields
$heading            = get_sub_field('heading') ?: 'About Sanctuary Runners';
$heading_tag        = get_sub_field('heading_tag') ?: 'h1';
$content            = get_sub_field('content') ?: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.';
$primary_cta        = get_sub_field('primary_cta');
$secondary_cta      = get_sub_field('secondary_cta');
$primary_cta_icon   = get_sub_field('primary_cta_icon');
$secondary_cta_icon = get_sub_field('secondary_cta_icon');
$image              = get_sub_field('image');
$image_alt          = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'Featured image') : '';
$background_color   = get_sub_field('background_color') ?: '#EEF6FC';
$use_white_text     = get_sub_field('use_white_text');
$custom_breadcrumbs = get_sub_field('custom_breadcrumbs');
$layout_option      = get_sub_field('layout_option') ?: 'layout_1';

$is_layout_2         = $layout_option === 'layout_2';
$is_dark_background  = strtolower($background_color) !== '#eef6fc';

// CTA icon alts
$primary_cta_icon_alt   = $primary_cta_icon ? (get_post_meta($primary_cta_icon, '_wp_attachment_image_alt', true) ?: '') : '';
$secondary_cta_icon_alt = $secondary_cta_icon ? (get_post_meta($secondary_cta_icon, '_wp_attachment_image_alt', true) ?: '') : '';

// CTA checks
$has_primary_cta = $primary_cta && is_array($primary_cta) && !empty($primary_cta['url']) && !empty($primary_cta['title']);
$has_secondary_cta = $secondary_cta && is_array($secondary_cta) && !empty($secondary_cta['url']) && !empty($secondary_cta['title']);
$has_ctas = $has_primary_cta || $has_secondary_cta;

// Generate breadcrumbs automatically if not custom
$breadcrumbs = [];
if ($custom_breadcrumbs && have_rows('breadcrumb_items')) {
    while (have_rows('breadcrumb_items')) {
        the_row();
        $breadcrumbs[] = [
            'title'      => get_sub_field('breadcrumb_title'),
            'url'        => get_sub_field('breadcrumb_url'),
            'is_current' => get_sub_field('is_current_page'),
        ];
    }
} else {
    $breadcrumbs[] = [
        'title'      => 'Home',
        'url'        => home_url('/'),
        'is_current' => false,
    ];

    global $post;
    if ($post && $post->post_parent) {
        $ancestors = get_post_ancestors($post);
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $ancestor) {
            $breadcrumbs[] = [
                'title'      => get_the_title($ancestor),
                'url'        => get_permalink($ancestor),
                'is_current' => false,
            ];
        }
    }

    $current_title = is_home() ? 'Home' : get_the_title();
    $breadcrumbs[] = [
        'title'      => $current_title,
        'url'        => '',
        'is_current' => true,
    ];
}

// Text color classes
$text_color_class         = $use_white_text ? 'text-white' : 'text-[#00263E]';
$breadcrumb_text_class    = $use_white_text ? 'text-white' : 'text-gray-500';
$breadcrumb_current_class = $use_white_text ? 'text-white' : 'text-[#00263E]';
$heading_color_class      = $use_white_text ? 'text-white' : 'text-sky-800';
$content_color_class      = $is_dark_background ? 'text-white [&_p]:!text-white' : 'text-[#00263E]';

// Padding classes
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

// Generate unique section ID
$section_id = 'subhero-' . uniqid();
?>

<?php if (!$is_layout_2): ?>
    <!-- Layout 1 -->
    <section
        id="<?php echo esc_attr($section_id); ?>"
        class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
        style="background-color: <?php echo esc_attr($background_color); ?>;"
        role="banner"
        aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
    >
        <div class="max-xl:px-5 mx-auto w-full max-w-container pb-6 md:pb-8">
            <div class="grid grid-cols-1 items-center gap-12 max-md:gap-10 md:grid-cols-12">

                <?php if ($image): ?>
                    <div class="order-1 max-md:w-full md:order-2 md:col-span-7">
                        <figure class="w-full">
                            <?php
                            echo wp_get_attachment_image($image, 'full', false, [
                                'alt'      => esc_attr($image_alt),
                                'class'    => 'w-full max-h-[522px] h-auto rounded-lg object-cover object-center',
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]);
                            ?>

                            <?php if ($image_alt): ?>
                                <figcaption class="sr-only">
                                    <?php echo esc_html($image_alt); ?>
                                </figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php endif; ?>

                <div class="order-2 flex flex-col max-md:max-w-full md:order-1 md:col-span-5 <?php echo esc_attr($text_color_class); ?>">

                    <?php if (!empty($breadcrumbs)): ?>
                        <nav class="mb-4 max-sm:mb-6" aria-label="Breadcrumb navigation" role="navigation">
                            <ol class="flex items-center gap-2 text-sm leading-5 <?php echo esc_attr($breadcrumb_text_class); ?>">
                                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                                    <li class="flex items-center gap-2">
                                        <?php if ($breadcrumb['is_current']): ?>
                                            <span
                                                class="<?php echo esc_attr($breadcrumb_current_class); ?> font-['Public_Sans'] text-[12px] leading-[18px] font-normal"
                                                aria-current="page"
                                            >
                                                <?php echo esc_html($breadcrumb['title']); ?>
                                            </span>
                                        <?php else: ?>
                                            <a
                                                href="<?php echo esc_url($breadcrumb['url']); ?>"
                                                class="<?php echo esc_attr($text_color_class); ?> font-['Public_Sans'] text-[12px] leading-[18px] font-bold rounded hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-700"
                                            >
                                                <?php echo esc_html($breadcrumb['title']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($index < count($breadcrumbs) - 1): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none" aria-hidden="true">
                                                <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    <?php endif; ?>

                    <?php if (!empty($heading)): ?>
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="mb-6 text-[30px] leading-[38px] font-bold not-italic <?php echo esc_attr($heading_color_class); ?> max-sm:text-4xl max-sm:leading-10"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($content)): ?>
                        <div
                            class="wp_editor <?php echo esc_attr($content_color_class); ?> font-['Public_Sans'] text-[18px] leading-[24px] font-normal"
                            role="region"
                            aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
                        >
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_ctas): ?>
                        <div
                            class="flex flex-col flex-wrap items-start gap-4 pt-6 sm:flex-row sm:flex-nowrap"
                            role="group"
                            aria-label="<?php echo esc_attr__('Subhero calls to action', 'matrix-starter'); ?>"
                        >
                            <?php if ($has_primary_cta): ?>
                                <a
                                    href="<?php echo esc_url($primary_cta['url']); ?>"
                                    target="<?php echo esc_attr($primary_cta['target'] ?? '_self'); ?>"
                                    class="hero-cta inline-flex w-fit items-center justify-center gap-2 rounded-full bg-[#009DEA] px-6 py-4 text-center text-[0.875rem] leading-5 font-bold text-white transition-opacity duration-200 hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700 whitespace-nowrap"
                                    aria-label="<?php echo esc_attr($primary_cta['title']); ?>"
                                >
                                    <?php if ($primary_cta_icon): ?>
                                        <?php
                                        echo wp_get_attachment_image($primary_cta_icon, 'full', false, [
                                            'alt'      => esc_attr($primary_cta_icon_alt),
                                            'class'    => 'w-6 h-6 object-contain shrink-0',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]);
                                        ?>
                                    <?php endif; ?>

                                    <span><?php echo esc_html($primary_cta['title']); ?></span>
                                </a>
                            <?php endif; ?>

                            <?php if ($has_secondary_cta): ?>
                                <a
                                    href="<?php echo esc_url($secondary_cta['url']); ?>"
                                    target="<?php echo esc_attr($secondary_cta['target'] ?? '_self'); ?>"
                                    class="hero-cta inline-flex w-fit items-center justify-center gap-2 rounded-full border border-[#009DEA] px-6 py-4 text-[0.875rem] leading-5 font-bold text-[#009DEA] transition-opacity duration-200 hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700 whitespace-nowrap"
                                    aria-label="<?php echo esc_attr($secondary_cta['title']); ?>"
                                >
                                    <?php if ($secondary_cta_icon): ?>
                                        <?php
                                        echo wp_get_attachment_image($secondary_cta_icon, 'full', false, [
                                            'alt'      => esc_attr($secondary_cta_icon_alt),
                                            'class'    => 'w-6 h-6 object-contain shrink-0',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]);
                                        ?>
                                    <?php endif; ?>

                                    <span><?php echo esc_html($secondary_cta['title']); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- Layout 2 -->
    <section
        id="<?php echo esc_attr($section_id); ?>"
        class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
        style="background-color: <?php echo esc_attr($background_color); ?>;"
        role="banner"
        aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
    >
        <div class="max-xl:px-5 mx-auto flex w-full max-w-container pt-[0rem] pb-0 md:pb-0 lg:min-h-[300px] lg:items-center">
            <div class="grid grid-cols-1 items-center gap-12 max-md:gap-10 md:grid-cols-12">

                <?php if ($image): ?>
                    <div class="order-1 max-md:w-full md:order-2 md:col-span-6">
                        <figure class="w-full max-md:absolute max-md:inset-0 max-md:h-full">
                            <?php
                            echo wp_get_attachment_image($image, 'full', false, [
                                'alt'      => esc_attr($image_alt),
                                'class'    => 'w-full max-h-[300px] h-auto rounded-lg object-contain md:object-right max-md:object-center',
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]);
                            ?>

                            <?php if ($image_alt): ?>
                                <figcaption class="sr-only">
                                    <?php echo esc_html($image_alt); ?>
                                </figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php endif; ?>

                <div class="order-2 z-[2] flex flex-col max-md:max-w-full md:order-1 md:col-span-6 <?php echo esc_attr($text_color_class); ?>">

                    <?php if (!empty($breadcrumbs)): ?>
                        <nav class="mt-[3rem] sm:mt-0 mb-4 max-sm:mb-6" aria-label="Breadcrumb navigation" role="navigation">
                            <ol class="flex items-center gap-2 text-sm leading-5 <?php echo esc_attr($breadcrumb_text_class); ?>">
                                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                                    <li class="flex items-center gap-2">
                                        <?php if ($breadcrumb['is_current']): ?>
                                            <span
                                                class="<?php echo esc_attr($breadcrumb_current_class); ?> font-['Public_Sans'] text-[12px] leading-[18px] font-normal"
                                                aria-current="page"
                                            >
                                                <?php echo esc_html($breadcrumb['title']); ?>
                                            </span>
                                        <?php else: ?>
                                            <a
                                                href="<?php echo esc_url($breadcrumb['url']); ?>"
                                                class="<?php echo esc_attr($text_color_class); ?> font-['Public_Sans'] text-[12px] leading-[18px] font-bold rounded hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-700"
                                            >
                                                <?php echo esc_html($breadcrumb['title']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($index < count($breadcrumbs) - 1): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none" aria-hidden="true">
                                                <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    <?php endif; ?>

                    <?php if (!empty($heading)): ?>
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="mb-6 text-[30px] leading-[38px] font-bold not-italic <?php echo esc_attr($heading_color_class); ?> max-sm:text-4xl max-sm:leading-10"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($content)): ?>
                        <div
                            class="wp_editor <?php echo esc_attr($content_color_class); ?> font-['Public_Sans'] text-[18px] leading-[24px] font-normal"
                            role="region"
                            aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
                        >
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_ctas): ?>
                        <div
                            class="flex flex-col flex-wrap items-start gap-4 pt-6 sm:flex-row sm:flex-nowrap"
                            role="group"
                            aria-label="<?php echo esc_attr__('Subhero calls to action', 'matrix-starter'); ?>"
                        >
                            <?php if ($has_primary_cta): ?>
                                <a
                                    href="<?php echo esc_url($primary_cta['url']); ?>"
                                    target="<?php echo esc_attr($primary_cta['target'] ?? '_self'); ?>"
                                    class="hero-cta inline-flex w-fit items-center justify-center gap-2 rounded-full bg-[#009DEA] px-6 py-4 text-center text-[0.875rem] leading-5 font-bold text-white transition-opacity duration-200 hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700 whitespace-nowrap"
                                    aria-label="<?php echo esc_attr($primary_cta['title']); ?>"
                                >
                                    <?php if ($primary_cta_icon): ?>
                                        <?php
                                        echo wp_get_attachment_image($primary_cta_icon, 'full', false, [
                                            'alt'      => esc_attr($primary_cta_icon_alt),
                                            'class'    => 'w-6 h-6 object-contain shrink-0',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]);
                                        ?>
                                    <?php endif; ?>

                                    <span><?php echo esc_html($primary_cta['title']); ?></span>
                                </a>
                            <?php endif; ?>

                            <?php if ($has_secondary_cta): ?>
                                <a
                                    href="<?php echo esc_url($secondary_cta['url']); ?>"
                                    target="<?php echo esc_attr($secondary_cta['target'] ?? '_self'); ?>"
                                    class="hero-cta inline-flex w-fit items-center justify-center gap-2 rounded-full border border-[#009DEA] px-6 py-4 text-[0.875rem] leading-5 font-bold text-[#009DEA] transition-opacity duration-200 hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700 whitespace-nowrap"
                                    aria-label="<?php echo esc_attr($secondary_cta['title']); ?>"
                                >
                                    <?php if ($secondary_cta_icon): ?>
                                        <?php
                                        echo wp_get_attachment_image($secondary_cta_icon, 'full', false, [
                                            'alt'      => esc_attr($secondary_cta_icon_alt),
                                            'class'    => 'w-6 h-6 object-contain shrink-0',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]);
                                        ?>
                                    <?php endif; ?>

                                    <span><?php echo esc_html($secondary_cta['title']); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>
<?php endif; ?>