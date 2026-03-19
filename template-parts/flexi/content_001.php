<?php
$heading         = get_sub_field('heading');
$heading_tag     = get_sub_field('heading_tag') ?: 'h2';
$content         = get_sub_field('content');
$image           = get_sub_field('image');
$image_alt       = $image ? (get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'About us image') : '';
$bullet_points   = get_sub_field('bullet_points');
$button          = get_sub_field('button');
$show_button_icon= get_sub_field('show_button_icon');
$reverse_layout  = get_sub_field('reverse_layout');
$background_color= get_sub_field('background_color');

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

$section_id = 'about-us-' . wp_rand(1000, 9999);
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="px-5 mx-auto w-full max-w-container2 lg:px-10">

        <div class="grid grid-cols-1 gap-12 items-center py-16 lg:grid-cols-2">

            <!-- IMAGE -->
            <?php if ($image): ?>
                <div class="<?php echo $reverse_layout ? 'lg:order-2' : ''; ?>">
                    <div class="overflow-hidden bg-gray-100 rounded-lg">
                        <?php echo wp_get_attachment_image($image, 'full', false, [
                            'alt'   => esc_attr($image_alt),
                            'class' => 'w-full h-auto object-cover',
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CONTENT -->
            <div class="<?php echo $reverse_layout ? 'lg:order-1' : ''; ?> max-w-[420px]">

                <?php if (!empty($heading)): ?>
                    <<?php echo esc_attr($heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>-heading"
                        class="text-[36px] font-bold leading-[44px] tracking-[-0.72px] text-[var(--Blue-SR-500,#00628F)]"
                    >
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_attr($heading_tag); ?>>
                <?php endif; ?>

                <?php if (!empty($content)): ?>
                    <div class="content-001-text mt-6 font-['Public_Sans'] text-[16px] font-normal leading-[22px] text-[var(--Gray-600,#475467)] wp_editor">
                        <?php echo wp_kses_post($content); ?>
                    </div>
                <?php endif; ?>

                <?php if ($bullet_points && have_rows('bullet_points')): ?>
                    <div class="mt-6 space-y-3 font-semibold text-slate-900">
                        <?php while (have_rows('bullet_points')): the_row();
                            $bullet_text = get_sub_field('bullet_text');
                            $bullet_icon = get_sub_field('bullet_icon');
                            if (!$bullet_text) continue;
                        ?>
                            <div class="flex gap-2 items-center px-4 py-2 bg-sky-50 rounded-full w-fit">
                                <?php if ($bullet_icon): ?>
                                    <?php echo wp_get_attachment_image($bullet_icon, 'thumbnail', false, [
                                        'alt' => '',
                                        'class' => 'w-4 h-4',
                                        'aria-hidden' => 'true',
                                    ]); ?>
                                <?php else: ?>
                                    <svg width="13" height="10" viewBox="0 0 13 10" fill="none" aria-hidden="true">
                                        <path d="M11.6667 1L4.33333 8.33333L1 5"
                                            stroke="#009DE6"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                                <span class="font-['Public_Sans'] text-[14px] font-semibold leading-5 text-[var(--Gray-800,#001929)]"><?php echo esc_html($bullet_text); ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <?php if ($button && is_array($button) && isset($button['url'], $button['title'])): ?>
                    <div class="mt-8">
                        <a
                            href="<?php echo esc_url($button['url']); ?>"
                            class="inline-flex gap-2 justify-center items-center px-6 py-3 mx-auto text-sm font-bold text-white rounded-[100px] w-full md:w-fit border-[3px] border-[var(--Turquoise-500,#1C959B)] bg-[var(--Blue-SR-400,#008BCC)]"
                            target="<?php echo esc_attr($button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($button['title']); ?>"
                        >
                            <?php echo esc_html($button['title']); ?>

                            <?php if ($show_button_icon): ?>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M5 12H19M19 12L12 5M19 12L12 19"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>