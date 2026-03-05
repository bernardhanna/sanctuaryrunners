<?php
// Get ACF fields
$section_heading  = get_sub_field('section_heading') ?: 'Upcoming events';
$heading_tag      = get_sub_field('heading_tag') ?: 'h2';
$view_all_button  = get_sub_field('view_all_button');
$number_of_events = get_sub_field('number_of_events') ?: 3;

// Padding settings
$padding_classes = ['pt-5', 'pb-5'];
if (have_rows('padding_settings')) {
    $padding_classes = [];
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

// Query upcoming events
$current_date = current_time('Y-m-d H:i:s');
$events_query = new WP_Query([
    'post_type'      => 'event',
    'posts_per_page' => (int) $number_of_events,
    'post_status'    => 'publish',
    'meta_key'       => 'event_start',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => 'event_start',
            'value'   => $current_date,
            'compare' => '>=',
            'type'    => 'DATETIME',
        ],
    ],
]);

// Generate unique section ID
$section_id = 'events-' . uniqid();
?>

<section id="<?php echo esc_attr($section_id); ?>" class="flex overflow-hidden relative">
    <div class="flex flex-col items-center w-full mx-auto max-w-container <?php echo esc_attr(implode(' ', $padding_classes)); ?> max-lg:px-5">
        <div class="overflow-hidden px-14 pt-14 pb-20 w-full max-md:px-5">

            <?php
            // Build the "View all events" button once so we can reuse it in desktop + mobile positions
            $view_all_html = '';
            if ($view_all_button && is_array($view_all_button) && isset($view_all_button['url'], $view_all_button['title'])) {
                ob_start();
                ?>
                <a href="<?php echo esc_url($view_all_button['url']); ?>"
                   class="flex gap-2 justify-center items-center self-stretch py-3 pr-4 pl-6 my-auto text-sm font-bold leading-none text-sky-800 min-h-[42px] rounded-[100px] max-md:pl-5 btn w-fit whitespace-nowrap hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-800 transition-colors duration-200"
                   target="<?php echo esc_attr($view_all_button['target'] ?? '_self'); ?>"
                   aria-label="<?php echo esc_attr($view_all_button['title']); ?>">
                    <span class="self-stretch my-auto text-sky-800">
                        <?php echo esc_html($view_all_button['title']); ?>
                    </span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="object-contain self-stretch my-auto w-4 shrink-0 aspect-square" aria-hidden="true">
                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <?php
                $view_all_html = ob_get_clean();
            }
            ?>

            <!-- Header (Desktop: button on same line) -->
            <header class="flex flex-wrap gap-10 justify-between items-center w-full max-md:max-w-full">
                <div class="flex gap-3.5 items-center self-stretch my-auto min-w-60" role="banner">
                    <div class="flex gap-2 justify-center items-center self-stretch my-auto w-10 h-10 bg-rose-50 border-4 border-red-200 border-solid min-h-10 rounded-[100px]" role="img" aria-label="Calendar icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="object-contain self-stretch my-auto w-6 aspect-square">
                            <path d="M8 2V6M16 2V6M3 10H21M5 4H19C20.1046 4 21 4.89543 21 6V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V6C3 4.89543 3.89543 4 5 4Z" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <?php if (!empty($section_heading)): ?>
                        <<?php echo esc_attr($heading_tag); ?> class="self-stretch my-auto text-3xl font-light leading-none text-sky-950">
                            <?php echo esc_html($section_heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>
                </div>

                <?php if ($view_all_html): ?>
                    <div class="hidden md:block">
                        <?php echo $view_all_html; ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if ($events_query->have_posts()): ?>
                <main class="flex flex-wrap gap-4 items-start mt-6 w-full max-md:max-w-full" role="main">
                    <?php while ($events_query->have_posts()): $events_query->the_post(); ?>
                        <?php
                        $event_id    = get_the_ID();
                        $permalink   = get_permalink($event_id);

                        $event_image = get_post_thumbnail_id($event_id);
                        $event_image_alt = $event_image
                            ? (get_post_meta($event_image, '_wp_attachment_image_alt', true) ?: get_the_title($event_id))
                            : get_the_title($event_id);

                        $event_start = get_post_meta($event_id, 'event_start', true);
                        $event_date_formatted = '';

                        if ($event_start) {
                            $date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $event_start);
                            if ($date_obj) {
                                $event_date_formatted = $date_obj->format('j/n/Y | gA');
                            }
                        }

                        $event_locations = get_the_terms($event_id, 'event_location');
                        $location_name = '';
                        if ($event_locations && !is_wp_error($event_locations)) {
                            $location_name = $event_locations[0]->name;
                        }
                        ?>

                        <article class="flex-1 shrink basis-0 min-w-60" role="article">
                            <a href="<?php echo esc_url($permalink); ?>"
                               class="flex overflow-hidden gap-4 items-start p-6 rounded-lg transition-colors duration-200 bg-sky-950 max-md:px-5 hover:bg-sky-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                               aria-label="<?php echo esc_attr(get_the_title($event_id)); ?>">

                                <?php if ($event_image): ?>
                                    <div class="shrink-0 rounded aspect-square w-[100px]">
                                        <?php echo wp_get_attachment_image($event_image, 'thumbnail', false, [
                                            'alt'     => esc_attr($event_image_alt),
                                            'class'   => 'object-contain shrink-0 rounded aspect-square w-[100px] h-[100px]',
                                            'loading' => 'lazy',
                                        ]); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="shrink-0 rounded aspect-square w-[100px] h-[100px] bg-gray-300 flex items-center justify-center" role="img" aria-label="No image available">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-gray-500">
                                            <path d="M21 19V5C21 3.9 20.1 3 19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19ZM8.5 13.5L11 16.51L14.5 12L19 18H5L8.5 13.5Z" fill="currentColor"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <div class="flex flex-col flex-1 shrink basis-0">
                                    <h3 class="text-lg font-bold leading-6 text-red">
                                        <?php echo esc_html(get_the_title($event_id)); ?>
                                    </h3>

                                    <?php if ($event_date_formatted): ?>
                                        <time datetime="<?php echo esc_attr($event_start); ?>" class="mt-1 text-sm leading-none text-gray-100">
                                            <?php echo esc_html($event_date_formatted); ?>
                                        </time>
                                    <?php endif; ?>

                                    <?php if ($location_name): ?>
                                        <div class="flex gap-2 justify-center items-center self-start px-3 py-1 mt-1 text-xs bg-red rounded-[100px] text-slate-900">
                                            <span class="self-stretch my-auto text-slate-900">
                                                <?php echo esc_html($location_name); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>

                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </main>

                <?php if ($view_all_html): ?>
                    <!-- Mobile: button below the grid -->
                    <div class="mt-6 flex justify-center md:hidden">
                        <?php echo $view_all_html; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="p-8 mt-6 text-center text-gray-600 bg-gray-50 rounded-lg" role="status" aria-live="polite">
                    <p class="text-lg">No upcoming events found.</p>
                    <p class="mt-2 text-sm">Check back later for new events.</p>
                </div>

                <?php if ($view_all_html): ?>
                    <!-- Mobile: keep button below even if no events -->
                    <div class="mt-6 flex justify-start md:hidden">
                        <?php echo $view_all_html; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</section>