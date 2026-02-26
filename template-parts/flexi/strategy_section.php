<?php
// Get ACF field values
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$content = get_sub_field('content');
$main_image = get_sub_field('main_image');
$main_image_alt = get_post_meta($main_image, '_wp_attachment_image_alt', true) ?: 'Strategy main image';
$secondary_image_1 = get_sub_field('secondary_image_1');
$secondary_image_1_alt = get_post_meta($secondary_image_1, '_wp_attachment_image_alt', true) ?: 'Strategy secondary image';
$secondary_image_2 = get_sub_field('secondary_image_2');
$secondary_image_2_alt = get_post_meta($secondary_image_2, '_wp_attachment_image_alt', true) ?: 'Strategy secondary image';
$button = get_sub_field('button');
$background_color = get_sub_field('background_color');

// Generate unique section ID
$section_id = 'strategy-' . uniqid();

// Build padding classes
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-16 pb-16 mx-auto w-full max-w-container max-lg:px-5">
        <div class="flex overflow-hidden flex-wrap gap-10 items-center pr-24 w-full max-md:pr-5">

            <!-- Images Section -->
            <div class="flex overflow-hidden flex-wrap gap-4 my-auto w-[640px] max-md:max-w-full" role="img" aria-label="Strategy visual content">

                <!-- Main Image -->
                <?php if ($main_image): ?>
                    <div class="object-contain self-start w-[312px]">
                        <?php echo wp_get_attachment_image($main_image, 'full', false, [
                            'alt' => esc_attr($main_image_alt),
                            'class' => 'object-contain w-full h-auto',
                            'loading' => 'lazy'
                        ]); ?>
                    </div>
                <?php endif; ?>

                <!-- Secondary Images -->
                <div class="flex flex-col flex-1 justify-center shrink basis-0">

                    <?php if ($secondary_image_1): ?>
                        <div class="overflow-hidden flex-1 w-full bg-gray-50 rounded-none">
                            <?php echo wp_get_attachment_image($secondary_image_1, 'full', false, [
                                'alt' => esc_attr($secondary_image_1_alt),
                                'class' => 'object-contain w-full h-auto',
                                'loading' => 'lazy'
                            ]); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($secondary_image_2): ?>
                        <div class="overflow-hidden flex-1 mt-4 w-full bg-gray-50 rounded-none">
                            <?php echo wp_get_attachment_image($secondary_image_2, 'full', false, [
                                'alt' => esc_attr($secondary_image_2_alt),
                                'class' => 'object-contain w-full h-auto',
                                'loading' => 'lazy'
                            ]); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Content Section -->
            <article class="flex-1 my-auto font-bold text-sky-800 shrink basis-0 max-md:max-w-full">

                <?php if (!empty($heading)): ?>
                    <header>
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="text-3xl leading-none text-sky-800 max-md:max-w-full"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    </header>
                <?php endif; ?>

                <?php if (!empty($content)): ?>
                    <div class="mt-4 text-base leading-6 text-sky-950 max-md:max-w-full wp_editor">
                        <?php echo wp_kses_post($content); ?>
                    </div>
                <?php endif; ?>

                <?php if ($button && is_array($button) && isset($button['url'], $button['title'])): ?>
                    <div class="flex gap-2 items-start pt-4 mt-4 w-full text-sm leading-none max-md:max-w-full">
                        <a
                            href="<?php echo esc_url($button['url']); ?>"
                            class="flex gap-2 justify-center items-center px-6 py-4 whitespace-nowrap rounded-full border border-sky-800 border-solid transition-all duration-300 w-fit hover:bg-sky-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-800 btn"
                            target="<?php echo esc_attr($button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($button['title']); ?>"
                        >
                            <span class="text-sky-800 transition-colors duration-300 hover:text-white">
                                <?php echo esc_html($button['title']); ?>
                            </span>
                        </a>

                        <style>
                            .btn:hover span {
                                color: white !important;
                            }
                        </style>
                    </div>
                <?php endif; ?>

            </article>

        </div>
    </div>
</section>
