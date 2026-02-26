<?php
// Get ACF fields
$section_heading = get_sub_field('section_heading') ?: 'Events';
$heading_tag = get_sub_field('heading_tag') ?: 'h2';
$show_filters = get_sub_field('show_filters');
$show_search = get_sub_field('show_search');
$events_per_page = get_sub_field('events_per_page') ?: 6;
$background_color = get_sub_field('background_color') ?: '#ffffff';

// Padding settings
$padding_classes = ['pt-5', 'pb-5'];
if (have_rows('padding_settings')) {
    $padding_classes = [];
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get filter parameters
$selected_location = isset($_GET['event_location']) ? sanitize_text_field($_GET['event_location']) : '';
$search_keyword = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Build query args
$query_args = array(
    'post_type' => 'event',
    'posts_per_page' => $events_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'meta_key' => 'event_start',
    'orderby' => 'meta_value',
    'order' => 'ASC'
);

// Add taxonomy filter
if (!empty($selected_location)) {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'event_location',
            'field'    => 'slug',
            'terms'    => $selected_location,
        ),
    );
}

// Add search filter
if (!empty($search_keyword)) {
    $query_args['s'] = $search_keyword;
}

// Execute query
$events_query = new WP_Query($query_args);

// Get all event locations for filters
$event_locations = get_terms(array(
    'taxonomy' => 'event_location',
    'hide_empty' => true,
));

$section_id = 'events-listing-' . uniqid();
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    x-data="eventsFilter()"
>
    <div class="flex flex-col items-center mx-auto w-full max-w-container max-lg:px-5">

        <?php if ($show_filters || $show_search): ?>
        <div class="flex flex-wrap gap-10 justify-between items-center pb-4 w-full text-sm leading-none max-md:max-w-full">

            <?php if ($show_filters): ?>
            <div class="flex gap-4 items-center self-stretch my-auto">
                <div class="self-stretch my-auto text-sky-950">
                    Filter by:
                </div>
                <div class="flex gap-2 items-center self-stretch my-auto font-semibold text-sky-800 whitespace-nowrap">
                    <button
                        type="button"
                        class="flex flex-col justify-center items-center self-stretch px-4 py-2 my-auto rounded-full transition-colors duration-200 min-h-9 btn focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500"
                        :class="selectedLocation === '' ? 'bg-blue-200 text-sky-800' : 'border-2 border-sky-500 text-sky-800 hover:bg-blue-100'"
                        @click="filterEvents('')"
                        aria-label="Show all events"
                    >
                        <span class="text-sky-800">All</span>
                    </button>

                    <?php if (!empty($event_locations)): ?>
                        <?php foreach ($event_locations as $location): ?>
                        <button
                            type="button"
                            class="flex flex-col justify-center items-center self-stretch px-4 py-2 my-auto rounded-full transition-colors duration-200 min-h-9 btn focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500"
                            :class="selectedLocation === '<?php echo esc_attr($location->slug); ?>' ? 'bg-blue-200 text-sky-800' : 'border-2 border-sky-500 text-sky-800 hover:bg-blue-100'"
                            @click="filterEvents('<?php echo esc_attr($location->slug); ?>')"
                            aria-label="Filter by <?php echo esc_attr($location->name); ?>"
                        >
                            <span class="text-sky-800"><?php echo esc_html($location->name); ?></span>
                        </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($show_search): ?>
            <form
                class="flex self-stretch my-auto min-h-[60px] shadow-[0px_0px_20px_rgba(63,0,119,0.07)] w-full max-w-[405px]"
                @submit.prevent="searchEvents()"
                role="search"
                aria-label="Search events"
            >
                <div class="flex-1 text-gray-400 shrink basis-12">
                    <div class="flex-1 w-full">
                        <div class="flex flex-1 justify-between items-center px-4 py-3 bg-white rounded-l size-full">
                            <div class="flex flex-1 gap-2 items-center self-stretch my-auto w-full shrink basis-0">
                                <svg
                                    class="object-contain self-stretch my-auto w-6 text-gray-400 shrink-0 aspect-square"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input
                                    type="search"
                                    class="flex-1 self-stretch my-auto placeholder-gray-400 text-gray-400 bg-transparent border-none outline-none shrink focus:text-gray-900"
                                    placeholder="Type keyword"
                                    x-model="searchKeyword"
                                    aria-label="Search events by keyword"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    type="submit"
                    class="flex gap-2 justify-center items-center px-6 py-4 h-full font-bold whitespace-nowrap bg-yellow-300 rounded-r transition-colors duration-200 text-slate-800 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 btn max-md:px-5"
                    aria-label="Search events"
                >
                    <span class="self-stretch my-auto text-slate-800">Search</span>
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="flex flex-col mt-4 w-full max-md:max-w-full">
            <div class="grid grid-cols-1 gap-6 w-full md:grid-cols-2 lg:grid-cols-3" x-show="!loading">
                <?php if ($events_query->have_posts()): ?>
                    <?php while ($events_query->have_posts()): $events_query->the_post(); ?>
                        <?php
                        $event_start = get_post_meta(get_the_ID(), 'event_start', true);
                        $event_date = $event_start ? date('j M Y', strtotime($event_start)) : '';
                        $event_locations_terms = get_the_terms(get_the_ID(), 'event_location');
                        $location_name = $event_locations_terms && !is_wp_error($event_locations_terms) ? $event_locations_terms[0]->name : 'Location TBD';
                        $featured_image = get_post_thumbnail_id();
                        $image_alt = get_post_meta($featured_image, '_wp_attachment_image_alt', true) ?: get_the_title();
                        ?>
                        <article class="overflow-hidden rounded-lg bg-sky-950">
                            <?php if ($featured_image): ?>
                            <div class="relative">
                                <?php echo wp_get_attachment_image($featured_image, 'large', false, [
                                    'alt' => esc_attr($image_alt),
                                    'class' => 'object-cover w-full aspect-[1.56] min-h-[232px]',
                                ]); ?>
                            </div>
                            <?php endif; ?>

                            <div class="flex flex-col p-6 w-full max-md:px-5">
                                <h3 class="text-lg font-bold leading-none text-red-300">
                                    <a
                                        href="<?php echo esc_url(get_permalink()); ?>"
                                        class="transition-colors duration-200 hover:text-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-300 focus:ring-offset-sky-950"
                                        aria-label="Read more about <?php echo esc_attr(get_the_title()); ?>"
                                    >
                                        <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                </h3>

                                <div class="self-start mt-2 text-sm leading-none text-gray-200">
                                    <?php if ($event_date): ?>
                                    <div class="flex gap-2 items-center">
                                        <time
                                            datetime="<?php echo esc_attr($event_start); ?>"
                                            class="self-stretch my-auto text-gray-200"
                                        >
                                            <?php echo esc_html($event_date); ?>
                                        </time>
                                    </div>
                                    <?php endif; ?>

                                    <div class="flex gap-2 items-center mt-2">
                                        <svg
                                            class="object-contain self-stretch my-auto w-4 text-gray-200 shrink-0 aspect-square"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                        <span class="self-stretch my-auto text-gray-200">
                                            <?php echo esc_html($location_name); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full py-12 text-center">
                        <p class="text-lg text-gray-600">No events found matching your criteria.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Loading state -->
            <div x-show="loading" class="grid grid-cols-1 gap-6 w-full md:grid-cols-2 lg:grid-cols-3">
                <?php for ($i = 0; $i < 3; $i++): ?>
                <div class="overflow-hidden rounded-lg animate-pulse bg-sky-950">
                    <div class="bg-gray-300 w-full aspect-[1.56] min-h-[232px]"></div>
                    <div class="flex flex-col p-6 w-full">
                        <div class="mb-2 h-6 bg-gray-300 rounded"></div>
                        <div class="mb-2 w-24 h-4 bg-gray-300 rounded"></div>
                        <div class="w-32 h-4 bg-gray-300 rounded"></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <?php if ($events_query->max_num_pages > 1): ?>
            <nav
                class="flex flex-wrap gap-8 justify-center items-center self-center pt-6 mt-6 text-base leading-none text-sky-600 whitespace-nowrap max-md:max-w-full"
                aria-label="Events pagination"
            >
                <?php
                $current_page = max(1, get_query_var('paged'));
                $total_pages = $events_query->max_num_pages;
                ?>

                <!-- Previous button -->
                <?php if ($current_page > 1): ?>
                <a
                    href="<?php echo esc_url(get_pagenum_link($current_page - 1)); ?>"
                    class="flex gap-1 items-center self-stretch py-1 pr-2 pl-1 my-auto text-sky-600 transition-colors duration-200 hover:text-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 btn"
                    aria-label="Go to previous page"
                >
                    <svg
                        class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="self-stretch my-auto">Back</span>
                </a>
                <?php else: ?>
                <span class="flex gap-1 items-center self-stretch py-1 pr-2 pl-1 my-auto text-gray-300" aria-hidden="true">
                    <svg
                        class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="self-stretch my-auto text-gray-300">Back</span>
                </span>
                <?php endif; ?>

                <!-- Page numbers -->
                <div class="flex gap-2 items-center self-stretch my-auto">
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    // Always show first page
                    if ($start_page > 1): ?>
                        <a
                            href="<?php echo esc_url(get_pagenum_link(1)); ?>"
                            class="flex flex-col justify-center items-center self-stretch my-auto w-12 text-sky-600 rounded-full transition-colors duration-200 min-h-12 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 btn"
                            aria-label="Go to page 1"
                        >
                            <span>1</span>
                        </a>
                        <?php if ($start_page > 2): ?>
                        <span class="flex flex-col justify-center items-start self-stretch py-1 my-auto w-6 text-sky-600" aria-hidden="true">
                            <span>...</span>
                        </span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <?php if ($i == $current_page): ?>
                        <span
                            class="flex flex-col justify-center items-center self-stretch my-auto w-12 text-sky-800 bg-blue-200 rounded-full min-h-12"
                            aria-current="page"
                            aria-label="Current page, page <?php echo esc_attr($i); ?>"
                        >
                            <span><?php echo esc_html($i); ?></span>
                        </span>
                        <?php else: ?>
                        <a
                            href="<?php echo esc_url(get_pagenum_link($i)); ?>"
                            class="flex flex-col justify-center items-center self-stretch my-auto w-12 text-sky-600 rounded-full transition-colors duration-200 min-h-12 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 btn"
                            aria-label="Go to page <?php echo esc_attr($i); ?>"
                        >
                            <span><?php echo esc_html($i); ?></span>
                        </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Always show last page -->
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                        <span class="flex flex-col justify-center items-start self-stretch py-1 my-auto w-6 text-sky-600" aria-hidden="true">
                            <span>...</span>
                        </span>
                        <?php endif; ?>
                        <a
                            href="<?php echo esc_url(get_pagenum_link($total_pages)); ?>"
                            class="flex flex-col justify-center items-center self-stretch my-auto w-12 text-sky-600 rounded-full transition-colors duration-200 min-h-12 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 btn"
                            aria-label="Go to page <?php echo esc_attr($total_pages); ?>"
                        >
                            <span><?php echo esc_html($total_pages); ?></span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Next button -->
                <?php if ($current_page < $total_pages): ?>
                <a
                    href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>"
                    class="flex gap-1 items-center self-stretch py-1 pr-1 pl-2.5 my-auto text-sky-600 transition-colors duration-200 hover:text-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 btn"
                    aria-label="Go to next page"
                >
                    <span class="self-stretch my-auto">Next</span>
                    <svg
                        class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <?php else: ?>
                <span class="flex gap-1 items-center self-stretch py-1 pr-1 pl-2.5 my-auto text-gray-300" aria-hidden="true">
                    <span class="self-stretch my-auto text-gray-300">Next</span>
                    <svg
                        class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function eventsFilter() {
    return {
        selectedLocation: '<?php echo esc_js($selected_location); ?>',
        searchKeyword: '<?php echo esc_js($search_keyword); ?>',
        loading: false,

        filterEvents(location) {
            this.selectedLocation = location;
            this.updateUrl();
        },

        searchEvents() {
            this.updateUrl();
        },

        updateUrl() {
            this.loading = true;
            const url = new URL(window.location);

            if (this.selectedLocation) {
                url.searchParams.set('event_location', this.selectedLocation);
            } else {
                url.searchParams.delete('event_location');
            }

            if (this.searchKeyword) {
                url.searchParams.set('search', this.searchKeyword);
            } else {
                url.searchParams.delete('search');
            }

            // Remove page parameter when filtering
            url.searchParams.delete('paged');

            window.location.href = url.toString();
        }
    }
}
</script>

<?php wp_reset_postdata(); ?>
