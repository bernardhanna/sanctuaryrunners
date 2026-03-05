<?php
// Get ACF fields
$heading            = get_sub_field('heading') ?: 'About Sanctuary Runners';
$heading_tag        = get_sub_field('heading_tag') ?: 'h1';
$content            = get_sub_field('content') ?: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.';
$image              = get_sub_field('image');
$image_alt          = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'Featured image') : '';
$background_color   = get_sub_field('background_color') ?: '#EEF6FC';
$use_white_text     = get_sub_field('use_white_text');
$custom_breadcrumbs = get_sub_field('custom_breadcrumbs');

// Generate breadcrumbs automatically if not custom
$breadcrumbs = [];
if ($custom_breadcrumbs && have_rows('breadcrumb_items')) {
    while (have_rows('breadcrumb_items')) {
        the_row();
        $breadcrumbs[] = [
            'title'      => get_sub_field('breadcrumb_title'),
            'url'        => get_sub_field('breadcrumb_url'),
            'is_current' => get_sub_field('is_current_page')
        ];
    }
} else {
    // Auto-generate breadcrumbs
    $breadcrumbs[] = ['title' => 'Home', 'url' => home_url('/'), 'is_current' => false];

    // Add parent pages if they exist
    global $post;
    if ($post && $post->post_parent) {
        $ancestors = get_post_ancestors($post);
        $ancestors = array_reverse($ancestors);
        foreach ($ancestors as $ancestor) {
            $breadcrumbs[] = [
                'title'      => get_the_title($ancestor),
                'url'        => get_permalink($ancestor),
                'is_current' => false
            ];
        }
    }

    // Add current page
    $current_title = is_home() ? 'Home' : get_the_title();
    $breadcrumbs[] = ['title' => $current_title, 'url' => '', 'is_current' => true];
}

// Text color classes
$text_color_class         = $use_white_text ? 'text-white' : 'text-gray-900';
$breadcrumb_text_class    = $use_white_text ? 'text-white' : 'text-gray-500';
$breadcrumb_current_class = $use_white_text ? 'text-white' : 'text-gray-900';
$heading_color_class      = $use_white_text ? 'text-white' : 'text-sky-800';
$content_color_class      = $use_white_text ? 'text-gray-100' : 'text-gray-600';

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

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    role="banner"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="max-xl:px-5 mx-auto w-full max-w-container pt-[8rem] pb-6 md:pb-16">

        <!-- 12-col grid, slightly smaller gap -->
        <div class="grid grid-cols-1 gap-12 items-start max-md:gap-10 md:grid-cols-12">

            <!-- Image Column (FIRST on mobile, larger on desktop) -->
            <?php if ($image): ?>
                <div class="max-md:w-full order-1 md:order-2 md:col-span-8">
                    <figure class="w-full">
                        <?php echo wp_get_attachment_image($image, 'full', false, [
                            'alt'      => esc_attr($image_alt),
                            // If you want fixed ratio like Figma, use aspect-[16/9] or aspect-[4/3]
                            'class'    => 'w-full h-auto rounded-lg object-cover',
                            'loading'  => 'lazy',
                            'decoding' => 'async'
                        ]); ?>

                        <?php if ($image_alt): ?>
                            <figcaption class="sr-only">
                                <?php echo esc_html($image_alt); ?>
                            </figcaption>
                        <?php endif; ?>
                    </figure>
                </div>
            <?php endif; ?>

            <!-- Content Column (SECOND on mobile, smaller on desktop) -->
            <div class="flex flex-col max-md:max-w-full order-2 md:order-1 md:col-span-4">

                <!-- Breadcrumb Navigation -->
                <?php if (!empty($breadcrumbs)): ?>
                    <nav class="mb-4 max-sm:mb-6" aria-label="Breadcrumb navigation" role="navigation">
                        <ol class="flex gap-2 items-center text-sm leading-5 <?php echo esc_attr($breadcrumb_text_class); ?>">
                            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                                <li class="flex gap-2 items-center">
                                    <?php if ($breadcrumb['is_current']): ?>
                                        <span
                                            class="text-[#00263E] font-['Public_Sans'] text-[12px] leading-[18px] font-normal"
                                            aria-current="page"
                                        >
                                            <?php echo esc_html($breadcrumb['title']); ?>
                                        </span>
                                    <?php else: ?>
                                        <a
                                            href="<?php echo esc_url($breadcrumb['url']); ?>"
                                            class="text-[#00263E] font-['Public_Sans'] text-[12px] leading-[18px] font-bold rounded hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-700"
                                            tabindex="0"
                                        >
                                            <?php echo esc_html($breadcrumb['title']); ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($index < count($breadcrumbs) - 1): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                                            <path d="M1 9L5 5L1 1" stroke="#00263E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>

                <!-- Main Heading -->
                <?php if (!empty($heading)): ?>
                    <<?php echo esc_attr($heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>-heading"
                        class="mb-6 text-[30px] leading-[38px] font-bold not-italic <?php echo esc_attr($heading_color_class); ?> max-sm:text-4xl max-sm:leading-10"
                    >
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_attr($heading_tag); ?>>
                <?php endif; ?>

                <!-- Content Description -->
                <?php if (!empty($content)): ?>
                    <div
                        class="wp_editor text-[#00263E] font-['Public_Sans'] text-[18px] leading-[24px] font-normal"
                        role="region"
                        aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
                    >
                        <?php echo wp_kses_post($content); ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</section>