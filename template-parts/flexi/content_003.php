<?php
// Get all dynamic content
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$description = get_sub_field('description');
$content = get_sub_field('content');
$image = get_sub_field('image');
$image_alt = get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'Start a new group image';
$primary_button = get_sub_field('primary_button');
$secondary_button = get_sub_field('secondary_button');
$reverse_layout = get_sub_field('reverse_layout');
$background_color = get_sub_field('background_color');

// Generate unique section ID
$section_id = 'start-group-' . uniqid();

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
>
    <div class="flex flex-col items-center pt-8 pb-16 mx-auto w-full max-w-container max-lg:px-5">
        <div class="flex overflow-hidden flex-wrap gap-10 items-center w-full <?php echo $reverse_layout ? 'flex-row-reverse' : ''; ?>">

            <!-- Text Content Section -->
            <article class="flex-1 shrink basis-0 min-w-60 max-md:max-w-full">
                <div class="w-full max-md:max-w-full">

                    <?php if (!empty($heading)): ?>
                        <<?php echo esc_attr($heading_tag); ?> class="text-2xl font-bold leading-none text-sky-800 max-md:max-w-full">
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($description)): ?>
                        <p class="mt-2 text-xl font-light leading-tight text-slate-600 max-md:max-w-full">
                            <?php echo esc_html($description); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($content)): ?>
                        <div class="mt-2 text-base leading-6 text-sky-950 max-md:max-w-full wp_editor">
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Key Points Section -->
                <?php if (have_rows('key_points')): ?>
                    <div class="flex flex-wrap gap-4 items-start pt-2 mt-4 text-sm font-semibold leading-none text-slate-900 max-md:max-w-full" role="list" aria-label="Key benefits">
                        <div class="flex flex-col" role="group" aria-label="First set of benefits">
                            <?php
                            $point_count = 0;
                            while (have_rows('key_points')):
                                the_row();
                                $point_text = get_sub_field('point_text');
                                if (!empty($point_text) && $point_count < 2):
                                    $point_count++;
                            ?>
                                <div class="flex overflow-hidden gap-1 items-center px-3 py-1 <?php echo $point_count > 1 ? 'mt-2' : ''; ?> bg-teal-100 rounded-full" role="listitem">
                                    <svg
                                        class="object-contain w-4 h-4 shrink-0"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                    <span class="text-slate-900">
                                        <?php echo esc_html($point_text); ?>
                                    </span>
                                </div>
                            <?php
                                endif;
                            endwhile;
                            ?>
                        </div>

                        <div class="flex flex-col min-w-60" role="group" aria-label="Second set of benefits">
                            <?php
                            $point_count = 0;
                            while (have_rows('key_points')):
                                the_row();
                                $point_text = get_sub_field('point_text');
                                if (!empty($point_text)):
                                    $point_count++;
                                    if ($point_count > 2):
                            ?>
                                <div class="flex overflow-hidden gap-1 items-center px-3 py-1 <?php echo $point_count > 3 ? 'mt-2' : ''; ?> bg-teal-100 rounded-full" role="listitem">
                                    <svg
                                        class="object-contain w-4 h-4 shrink-0"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                    <span class="text-slate-900">
                                        <?php echo esc_html($point_text); ?>
                                    </span>
                                </div>
                            <?php
                                    endif;
                                endif;
                            endwhile;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Buttons Section -->
                <div class="flex gap-2 items-start pt-4 mt-4 max-w-full text-sm font-bold leading-none w-[289px]">
                    <?php if ($primary_button && is_array($primary_button) && isset($primary_button['url'], $primary_button['title'])): ?>
                        <a
                            href="<?php echo esc_url($primary_button['url']); ?>"
                            class="flex gap-2 justify-center items-center px-6 py-4 text-white min-h-[52px] rounded-full w-fit whitespace-nowrap bg-sky-800 hover:bg-sky-700 focus:bg-sky-700 transition-colors duration-300 btn"
                            target="<?php echo esc_attr($primary_button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($primary_button['title']); ?>"
                        >
                            <span class="text-sm font-semibold leading-5">
                                <?php echo esc_html($primary_button['title']); ?>
                            </span>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_button && is_array($secondary_button) && isset($secondary_button['url'], $secondary_button['title'])): ?>
                        <a
                            href="<?php echo esc_url($secondary_button['url']); ?>"
                            class="flex gap-2 justify-center items-center px-6 py-4 text-sky-800 border border-sky-800 border-solid min-h-[52px] rounded-full w-fit whitespace-nowrap hover:bg-sky-800 hover:text-white focus:bg-sky-800 focus:text-white transition-colors duration-300 btn"
                            target="<?php echo esc_attr($secondary_button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($secondary_button['title']); ?>"
                        >
                            <span class="text-sm font-semibold leading-5">
                                <?php echo esc_html($secondary_button['title']); ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </article>

            <!-- Image Section -->
            <?php if ($image): ?>
                <div class="overflow-hidden bg-gray-100 rounded-lg min-w-60 w-[544px] max-md:max-w-full">
                    <?php echo wp_get_attachment_image($image, 'full', false, [
                        'alt' => esc_attr($image_alt),
                        'class' => 'object-contain w-full aspect-[1.42] max-md:max-w-full',
                    ]); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
