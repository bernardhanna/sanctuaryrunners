<?php
// Get ACF fields
$section_heading     = get_sub_field('section_heading') ?: 'Events';
$heading_tag         = get_sub_field('heading_tag') ?: 'h2';
$show_filters        = get_sub_field('show_filters');
$show_search         = get_sub_field('show_search');
$events_per_page     = get_sub_field('events_per_page') ?: 6;
$show_pagination     = get_sub_field('show_pagination');
$background_color    = get_sub_field('background_color') ?: '#ffffff';

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

// Get current page for pagination
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// Get filter parameters
$selected_location = isset($_GET['event_location']) ? sanitize_text_field($_GET['event_location']) : '';
$search_keyword    = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Build query args
$query_args = array(
    'post_type'      => 'event',
    'posts_per_page' => $events_per_page,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'meta_key'       => 'event_start',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
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
    'taxonomy'   => 'event_location',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
));

$section_id = 'events-listing-' . uniqid();
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    x-data="eventsFilter()"
>
    <div class="flex flex-col items-center pt-5 lg:pt-[3.5rem] pb-5 mx-auto w-full max-w-container max-lg:px-5">

        <?php if ($show_filters || $show_search): ?>
            <div class="flex flex-wrap gap-10 justify-between items-center pb-4 w-full text-sm leading-none max-md:max-w-full">

                <?php if ($show_filters && !empty($event_locations)): ?>
                    <!-- Filters -->
                    <div class="flex gap-4 items-center self-stretch my-auto min-w-60">
                        <div class="self-stretch my-auto text-sky-950">
                            Filter by:
                        </div>

                        <div
                            class="flex gap-2 items-center self-stretch my-auto font-semibold text-sky-800 whitespace-nowrap"
                            role="group"
                            aria-label="Filter events by location"
                        >
                            <button
                                type="button"
                                class="flex flex-col justify-center items-center self-stretch px-4 py-2 my-auto whitespace-nowrap rounded-full border-2 border-sky-500 min-h-9 w-fit transition-colors duration-200 focus:ring-sky-500"
                                :class="selectedLocation === ''
                                    ? 'bg-sky-500 text-white'
                                    : 'bg-white text-sky-800 hover:bg-[#87c0e8] hover:text-white'"
                                @click="filterEvents('')"
                                aria-pressed="true"
                            >
                                All
                            </button>

                            <?php foreach ($event_locations as $location): ?>
                                <button
                                    type="button"
                                    class="flex flex-col justify-center items-center self-stretch px-4 py-2 my-auto whitespace-nowrap rounded-full min-h-9 w-fit transition-colors duration-200 focus:ring-sky-500"
                                    :class="selectedLocation === '<?php echo esc_attr($location->slug); ?>'
                                        ? 'bg-sky-500 text-white'
                                        : 'bg-blue-200 text-sky-800 hover:bg-[#87c0e8] hover:text-white'"
                                    @click="filterEvents('<?php echo esc_attr($location->slug); ?>')"
                                    aria-pressed="false"
                                >
                                    <?php echo esc_html($location->name); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($show_search): ?>
                <!-- Search -->
                <form
                    class="flex self-stretch my-auto min-h-[60px] min-w-60 w-[396px] items-center"
                    @submit.prevent="searchEvents()"
                    role="search"
                    aria-label="Search events"
                >
                    <div class="flex-1 shrink basis-12 min-w-60 shadow-[0px_0px_20px_rgba(63,0,119,0.07)] rounded-r-[30px] overflow-hidden">
                        <div class="w-full">
                            <div class="flex items-center">
                                <div
                                    class="flex flex-1 gap-2 items-center self-stretch my-auto w-full shrink basis-0 min-w-60 px-4 py-2 max-h-[60px] rounded-l-[4px] border border-transparent bg-white transition-all duration-200 hover:bg-[#CBF3F6] hover:border-[#1C959B] focus-within:bg-[#C2EDFF] focus-within:border-[#C2EDFF]"
                                >
                                    <svg
                                        class="object-contain self-stretch my-auto w-6 shrink-0 aspect-square"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true"
                                    >
                                        <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                    <input
                                        type="search"
                                        class="flex-1 self-stretch my-auto text-gray-400 bg-transparent !border-0 !outline-none !ring-0 focus:!border-0 focus:!outline-none focus:!ring-0 active:!border-0 active:!outline-none active:!ring-0 appearance-none shrink basis-0 min-h-[28px]"
                                        placeholder="Type keyword"
                                        x-model="searchKeyword"
                                        aria-label="Search events"
                                    />
                                </div>

                                <button
                                    type="submit"
                                    class="flex -ml-px gap-2 justify-center items-center px-6 py-4 h-full min-h-[60px] font-bold whitespace-nowrap bg-yellow-300 text-slate-800 max-md:px-5 w-fit hover:bg-[#FCF4C5] focus:ring-yellow-300 border-0 rounded-r-[100px] shadow-none"
                                    aria-label="Search events"
                                >
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>


            </div>
        <?php endif; ?>

        <!-- Events Grid -->
        <main class="flex flex-col mt-4 w-full max-md:max-w-full" role="main" aria-label="Events listing">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-md:max-w-full" x-show="!loading">

                <?php if ($events_query->have_posts()): ?>
                    <?php while ($events_query->have_posts()): $events_query->the_post(); ?>
                        <?php
                        $post_id               = get_the_ID();
                        $event_start           = get_post_meta($post_id, 'event_start', true);
                        $event_date            = $event_start ? date('j M Y', strtotime($event_start)) : '';
                        $event_locations_terms = get_the_terms($post_id, 'event_location');
                        $location_name         = ($event_locations_terms && !is_wp_error($event_locations_terms)) ? $event_locations_terms[0]->name : 'Location TBD';
                        $featured_image        = get_post_thumbnail_id($post_id);
                        $image_alt             = get_post_meta($featured_image, '_wp_attachment_image_alt', true) ?: get_the_title();
                        ?>

                        <article 
    class="overflow-hidden flex-1 bg-sky-950 rounded-lg shrink basis-0 min-w-60 transition-all duration-200 hover:shadow-[0_0_0_4px_#F68DA7]"
>
                            <!-- Featured Image -->
                            <div class="flex overflow-hidden relative flex-col gap-2.5 items-start w-full aspect-[1.565] min-h-[232px] rounded-t-[8px]">
                                <?php if ($featured_image): ?>
                                    <?php echo wp_get_attachment_image($featured_image, 'large', false, [
                                        'alt'     => esc_attr($image_alt),
                                        'class'   => 'object-cover absolute inset-0 size-full',
                                        'loading' => 'lazy',
                                    ]); ?>
                                <?php endif; ?>
                            </div>

                            <!-- Event Content -->
                            <div class="flex flex-col p-6 w-full text-sm max-md:px-5">
                                <h3 class="text-lg font-bold leading-none text-red-300">
                                    <a
                                        href="<?php echo esc_url(get_permalink()); ?>"
                                        class="hover:underline focus:underline"
                                    >
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <div class="self-start mt-2 text-sm leading-none text-gray-200">
                                    <?php if ($event_date): ?>
                                        <div class="flex gap-2 items-center">
                                            <time
                                                datetime="<?php echo esc_attr(date('Y-m-d', strtotime($event_start))); ?>"
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
                    <div class="col-span-full py-12 w-full text-center">
                        <p class="text-lg text-gray-600">No events found matching your criteria.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Loading state -->
            <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
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

            <?php if ($show_pagination && $events_query->max_num_pages > 1): ?>
                <!-- Pagination -->
                <nav
                    class="flex flex-wrap gap-3 md:gap-8 justify-evenly md:justify-center items-center self-center max-w-full pt-8 pb-6 md:pt-20 md:pb-12 mt-6 text-base leading-none text-sky-600 whitespace-nowrap"
                    aria-label="Events pagination"
                >
                    <?php
                    $total_pages  = $events_query->max_num_pages;
                    $current_page = $paged;

                    $base_url = get_pagenum_link(1);

                    $build_page_url = function($page) use ($base_url, $selected_location, $search_keyword) {
                        $url = add_query_arg(array_filter([
                            'event_location' => $selected_location ?: null,
                            'search'         => $search_keyword ?: null,
                            'paged'          => $page > 1 ? $page : null,
                        ]), $base_url);

                        return $url;
                    };
                    ?>

                    <!-- Previous Button -->
                    <?php if ($current_page > 1): ?>
                        <a
                            href="<?php echo esc_url($build_page_url($current_page - 1)); ?>"
                            class="flex gap-1 items-center self-stretch py-1 pr-2 pl-1 my-auto text-sky-600 hover:text-sky-800 focus:text-sky-800 btn"
                            aria-label="Go to previous page"
                        >
                            <svg
                                class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                                width="32"
                                height="32"
                                viewBox="0 0 32 32"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path d="M20 24L12 16L20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="self-stretch my-auto">Back</span>
                        </a>
                    <?php else: ?>
                        <div class="flex gap-1 items-center self-stretch py-1 pr-2 pl-1 my-auto text-gray-300">
                            <svg
                                class="object-contain self-stretch my-auto w-8 shrink-0 aspect-square"
                                width="32"
                                height="32"
                                viewBox="0 0 32 32"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path d="M20 24L12 16L20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="self-stretch my-auto text-gray-300">Back</span>
                        </div>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <div class="flex gap-2 items-center self-stretch my-auto">
                        <?php
                        $pagination_link_class = 'flex flex-col justify-center items-center self-stretch my-auto w-12 min-h-12 rounded-full text-sky-600 transition-all duration-200 hover:bg-[#009DE6] hover:text-white hover:shadow-[0_0_0_4px_#009DE6] focus:bg-[#009DE6] focus:text-white focus:shadow-[0_0_0_4px_#009DE6]';

                        $pagination_current_class = 'flex flex-col justify-center items-center self-stretch my-auto w-12 min-h-12 rounded-full text-white bg-[#009DE6] shadow-[0_0_0_4px_#009DE6]';
                        ?>

                        <?php if ($current_page == 1): ?>
                            <div class="<?php echo esc_attr($pagination_current_class); ?>">
                                <span aria-current="page">1</span>
                            </div>
                        <?php else: ?>
                            <a
                                href="<?php echo esc_url($build_page_url(1)); ?>"
                                class="<?php echo esc_attr($pagination_link_class); ?>"
                                aria-label="Go to page 1"
                            >
                                <span>1</span>
                            </a>
                        <?php endif; ?>

                        <?php
                        for ($i = max(2, $current_page - 2); $i <= min($total_pages - 1, $current_page + 2); $i++):
                            if ($i == $current_page): ?>
                                <div class="<?php echo esc_attr($pagination_current_class); ?>">
                                    <span aria-current="page"><?php echo esc_html($i); ?></span>
                                </div>
                            <?php else: ?>
                                <a
                                    href="<?php echo esc_url($build_page_url($i)); ?>"
                                    class="<?php echo esc_attr($pagination_link_class); ?>"
                                    aria-label="Go to page <?php echo esc_attr($i); ?>"
                                >
                                    <span><?php echo esc_html($i); ?></span>
                                </a>
                            <?php endif;
                        endfor;
                        ?>

                        <?php if ($total_pages > 5 && $current_page < $total_pages - 2): ?>
                            <div class="flex flex-col justify-center items-start self-stretch py-1 my-auto w-6">
                                <span class="text-sky-600" aria-hidden="true">...</span>
                            </div>
                            <a
                                href="<?php echo esc_url($build_page_url($total_pages)); ?>"
                                class="<?php echo esc_attr($pagination_link_class); ?>"
                                aria-label="Go to page <?php echo esc_attr($total_pages); ?>"
                            >
                                <span><?php echo esc_html($total_pages); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>

                    
                </nav>
            <?php endif; ?>
        </main>
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

            url.searchParams.delete('paged');

            window.location.href = url.toString();
        }
    }
}
</script>

<?php wp_reset_postdata(); ?>