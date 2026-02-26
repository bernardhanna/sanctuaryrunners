<?php

$section_id = 'timeline-' . wp_generate_uuid4();

$heading_text = get_sub_field('heading_text');
$heading_tag = get_sub_field('heading_tag');

$show_subheading = get_sub_field('show_subheading');
$subheading_text = get_sub_field('subheading_text');
$subheading_tag = get_sub_field('subheading_tag');

$show_body = get_sub_field('show_body');
$body_text = get_sub_field('body_text');

$background_color = get_sub_field('background_color');
$heading_color = get_sub_field('heading_color');
$subheading_color = get_sub_field('subheading_color');
$body_text_color = get_sub_field('body_text_color');

$card_bg_color = get_sub_field('card_bg_color');
$card_text_color = get_sub_field('card_text_color');
$timeline_line_color = get_sub_field('timeline_line_color');
$date_text_color = get_sub_field('date_text_color');
$card_radius = get_sub_field('card_radius');

$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if ($screen_size !== '' && $padding_top !== '' && $padding_top !== null) {
            $padding_classes[] = $screen_size . ':pt-[' . $padding_top . 'rem]';
        }
        if ($screen_size !== '' && $padding_bottom !== '' && $padding_bottom !== null) {
            $padding_classes[] = $screen_size . ':pb-[' . $padding_bottom . 'rem]';
        }
    }
}
$padding_class_string = implode(' ', $padding_classes);

$allowed_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (!in_array($heading_tag, $allowed_tags, true)) $heading_tag = 'h3';
if (!in_array($subheading_tag, $allowed_tags, true)) $subheading_tag = 'h4';

$heading_id = $section_id . '-heading';

$items = get_sub_field('timeline_items');
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="flex overflow-hidden relative"
    aria-labelledby="<?php echo esc_attr($heading_id); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container pt-5 pb-5 max-lg:px-5 <?php echo esc_attr($padding_class_string); ?>">
        <div class="flex flex-col gap-12 w-full">
            <header class="flex flex-col gap-4 w-full lg:px-32">
                <<?php echo esc_html($heading_tag); ?>
                    id="<?php echo esc_attr($heading_id); ?>"
                    class="break-words text-left text-[1.875rem] font-[700] leading-[2.375rem] font-['Public Sans'] w-full"
                    style="color: <?php echo esc_attr($heading_color); ?>;"
                >
                    <?php echo esc_html($heading_text); ?>
                </<?php echo esc_html($heading_tag); ?>>

                <?php if (!empty($show_subheading) && !empty($subheading_text)) { ?>
                    <<?php echo esc_html($subheading_tag); ?>
                        class="break-words text-left text-[1.25rem] font-[400] leading-[1.625rem] font-['Public Sans'] w-full"
                        style="color: <?php echo esc_attr($subheading_color); ?>;"
                    >
                        <?php echo esc_html($subheading_text); ?>
                    </<?php echo esc_html($subheading_tag); ?>>
                <?php } ?>

                <?php if (!empty($show_body) && !empty($body_text)) { ?>
                    <div
                        class="wp_editor break-words text-left text-[1rem] font-[400] leading-[1.375rem] font-['Public Sans'] w-full"
                        style="color: <?php echo esc_attr($body_text_color); ?>;"
                    >
                        <?php echo wp_kses_post($body_text); ?>
                    </div>
                <?php } ?>
            </header>

            <?php if (!empty($items) && is_array($items)) { ?>
                <ol class="flex flex-col gap-4 w-full">
                    <?php foreach ($items as $i => $item) { ?>
                        <?php
                        $side = !empty($item['side']) ? $item['side'] : 'left';

                        $event_date = !empty($item['event_date']) ? $item['event_date'] : '';
                        $event_date_label = !empty($item['event_date_label']) ? $item['event_date_label'] : '';
                        $display_date = $event_date_label;

                        if ($display_date === '' && $event_date !== '') {
                            $ts = strtotime($event_date);
                            $display_date = $ts ? date('j.n.Y', $ts) : $event_date;
                        }

                        $item_heading = !empty($item['item_heading']) ? $item['item_heading'] : '';
                        $item_heading_tag = !empty($item['item_heading_tag']) ? $item['item_heading_tag'] : 'h4';
                        if (!in_array($item_heading_tag, $allowed_tags, true)) $item_heading_tag = 'h4';

                        $item_text = !empty($item['item_text']) ? $item['item_text'] : '';

                        $show_cta = !empty($item['show_cta']);
                        $cta_link = !empty($item['cta_link']) && is_array($item['cta_link']) ? $item['cta_link'] : null;
                        $cta_url = $cta_link['url'] ?? '';
                        $cta_title = $cta_link['title'] ?? '';
                        $cta_target = $cta_link['target'] ?? '_self';

                        $card = function () use ($item_heading, $item_heading_tag, $item_text, $show_cta, $cta_url, $cta_title, $cta_target, $card_bg_color, $card_text_color, $heading_color, $card_radius) {
                            ?>
                            <article
                                class="w-full flex flex-col gap-2 pt-8 pb-8 px-5 md:px-8 <?php echo esc_attr($card_radius); ?>"
                                style="background-color: <?php echo esc_attr($card_bg_color); ?>;"
                            >
                                <?php if ($item_heading !== '') { ?>
                                    <<?php echo esc_html($item_heading_tag); ?>
                                        class="break-words text-left text-[1.25rem] font-[700] leading-[1.625rem] font-['Public Sans'] w-full"
                                        style="color: <?php echo esc_attr($heading_color); ?>;"
                                    >
                                        <?php echo esc_html($item_heading); ?>
                                    </<?php echo esc_html($item_heading_tag); ?>>
                                <?php } ?>

                                <?php if ($item_text !== '') { ?>
                                    <div
                                        class="wp_editor break-words text-left text-[1rem] font-[400] leading-[1.375rem] font-['Public Sans'] w-full"
                                        style="color: <?php echo esc_attr($card_text_color); ?>;"
                                    >
                                        <?php echo wp_kses_post($item_text); ?>
                                    </div>
                                <?php } ?>

                                <?php if ($show_cta && $cta_url !== '' && $cta_title !== '') { ?>
                                    <div class="pt-2">
                                        <a
                                            href="<?php echo esc_url($cta_url); ?>"
                                            target="<?php echo esc_attr($cta_target); ?>"
                                            rel="<?php echo esc_attr($cta_target === '_blank' ? 'noopener noreferrer' : ''); ?>"
                                            class="flex justify-center items-center gap-2 shrink-0 bg-[linear-gradient(313.479227deg,_rgba(5,157,237,1)_0%,_rgba(40,178,250,1)_91%)] bg-center rounded-[6.25rem] px-8 py-3.5 btn hover:opacity-90 transition-opacity duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 text-[0.875rem] leading-[1.25rem] font-[700] font-['Public Sans'] w-full"
                                            aria-label="<?php echo esc_attr($cta_title); ?>"
                                        >
                                            <span class="my-auto"><?php echo esc_html($cta_title); ?></span>
                                        </a>
                                    </div>
                                <?php } ?>
                            </article>
                            <?php
                        };
                        ?>

                        <li class="w-full">
                            <div class="w-full md:grid md:grid-cols-[1fr_auto_1fr] md:gap-4 items-stretch">
                                <!-- Left -->
                                <div class="w-full <?php echo esc_attr($side === 'right' ? 'hidden md:block' : ''); ?>">
                                    <?php if ($side === 'left') { $card(); } ?>
                                </div>

                                <!-- Center marker + line -->
                                <div class="flex relative justify-center items-center py-6 md:flex-col md:justify-between md:py-0">
                                    <div
                                        class="hidden absolute top-0 bottom-0 w-px md:block"
                                        aria-hidden="true"
                                        style="background-color: <?php echo esc_attr($timeline_line_color); ?>;"
                                    ></div>

                                    <div class="flex gap-3 items-center md:flex-col md:items-center md:gap-2">
                                        <span
                                            class="shrink-0 bg-[linear-gradient(90deg,_rgba(3,89,135,1)_0%,_rgba(4,122,185,1)_91%)] bg-center rounded-[62.5rem] w-[1.5rem] h-[1.5rem]"
                                            aria-hidden="true"
                                        ></span>

                                        <?php if ($display_date !== '') { ?>
                                            <time
                                                datetime="<?php echo esc_attr($event_date); ?>"
                                                class="break-words text-left text-[0.75rem] font-[700] leading-[1.125rem] font-['Public Sans'] uppercase"
                                                style="color: <?php echo esc_attr($date_text_color); ?>;"
                                            >
                                                <?php echo esc_html($display_date); ?>
                                            </time>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Right -->
                                <div class="w-full <?php echo esc_attr($side === 'left' ? 'hidden md:block' : ''); ?>">
                                    <?php if ($side === 'right') { $card(); } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ol>
            <?php } ?>
        </div>
    </div>
</section>