<?php
// Get ACF field values
$heading            = get_sub_field('heading');
$heading_tag        = get_sub_field('heading_tag');
$content            = get_sub_field('content');
$main_image         = get_sub_field('main_image');
$main_image_alt     = get_post_meta($main_image, '_wp_attachment_image_alt', true) ?: 'Strategy main image';
$secondary_image_1  = get_sub_field('secondary_image_1');
$secondary_image_1_alt = get_post_meta($secondary_image_1, '_wp_attachment_image_alt', true) ?: 'Strategy secondary image';
$secondary_image_2  = get_sub_field('secondary_image_2');
$secondary_image_2_alt = get_post_meta($secondary_image_2, '_wp_attachment_image_alt', true) ?: 'Strategy secondary image';
$button             = get_sub_field('button');
$background_color   = get_sub_field('background_color');

// Generate unique section ID
$section_id = 'strategy-' . uniqid();

// Build padding classes
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
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-8 pb-8 md:pt-16 md:pb-16 w-full max-lg:px-5">
        <!-- Changed: remove flex-wrap + enforce stacked mobile / 2-col desktop -->
        <div class="flex flex-col md:flex-row overflow-hidden gap-10 items-center w-full pr-0 lg:pr-24">


            <!-- Images Section (50%) -->
            <div class="w-full md:w-1/2 min-w-0 flex gap-4 my-auto" role="img" aria-label="Strategy visual content">

                <!-- Main Image (always 50%) -->
                <?php if ($main_image): ?>
                    <div class="w-1/2 min-w-0 object-contain self-start">
                        <?php echo wp_get_attachment_image($main_image, 'full', false, [
                            'alt'     => esc_attr($main_image_alt),
                            'class'   => 'object-contain w-full h-auto',
                            'loading' => 'lazy'
                        ]); ?>
                    </div>
                <?php endif; ?>

                <!-- Secondary Images (always 50%) -->
                <div class="w-1/2 min-w-0 flex flex-col justify-center">

                    <?php if ($secondary_image_1): ?>
                        <div class="overflow-hidden w-full bg-gray-50 rounded-none">
                            <?php echo wp_get_attachment_image($secondary_image_1, 'full', false, [
                                'alt'     => esc_attr($secondary_image_1_alt),
                                'class'   => 'object-contain w-full h-auto',
                                'loading' => 'lazy'
                            ]); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($secondary_image_2): ?>
                        <div class="overflow-hidden mt-4 w-full bg-gray-50 rounded-none">
                            <?php echo wp_get_attachment_image($secondary_image_2, 'full', false, [
                                'alt'     => esc_attr($secondary_image_2_alt),
                                'class'   => 'object-contain w-full h-auto',
                                'loading' => 'lazy'
                            ]); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Content Section (50%) -->
            <article class="w-full md:w-1/2 min-w-0 my-auto font-bold text-sky-800">

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
                        <div class="flex pt-4 mt-4 w-full">
                            <a
                                href="<?php echo esc_url($button['url']); ?>"
                                class="flex justify-center items-center w-full md:w-fit px-6 py-4 text-sm leading-none whitespace-nowrap rounded-[100px] border border-[#00628F] bg-transparent md:bg-[#EBF9FF] text-[#00628F] transition-all duration-200 hover:border-transparent hover:shadow-[0_0_0_4px_#1C959B]"
                            >
                                <?php echo esc_html($button['title']); ?>
                            </a>
                        </div>
                <?php endif; ?>
               
            </article>

        </div>
    </div>
</section>