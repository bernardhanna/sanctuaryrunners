<?php
// Get ACF fields
$section_heading     = get_sub_field('section_heading') ?: 'Latest Posts';
$section_heading_tag = get_sub_field('section_heading_tag') ?: 'h2';
$show_filters        = get_sub_field('show_filters');
if ($show_filters === null) $show_filters = true;
$show_search         = get_sub_field('show_search');
if ($show_search === null) $show_search = true;
$posts_per_page      = get_sub_field('posts_per_page') ?: get_option('posts_per_page') ?: 6;
$show_pagination     = get_sub_field('show_pagination');
if ($show_pagination === null) $show_pagination = true;
$background_color    = get_sub_field('background_color') ?: '#ffffff';
$layout_option       = get_sub_field('layout_option') ?: 'layout_1';
$is_layout_2         = $layout_option === 'layout_2';

// Padding settings
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

// Get current page for pagination
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$search_query = is_search() ? get_search_query() : '';
if ($search_query === '' && isset($_GET['s'])) {
    $search_query = sanitize_text_field(wp_unslash($_GET['s']));
}

// Query posts
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_status'    => 'publish'
);
if ($search_query !== '') {
    $args['s'] = $search_query;
}

$blog_query = new WP_Query($args);

// Get categories for filters
$categories = get_categories(array(
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC'
));

$section_id = 'blog-listing-' . uniqid();
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    x-data="blogFilter('<?php echo esc_js($search_query); ?>')"
    x-init="filterPosts()"
>
    <div class="flex flex-col items-center pt-5 lg:pt-[3.5rem] pb-5 mx-auto w-full max-w-container max-lg:px-5">

        <!-- Filters and Search Section -->
        <div class="grid grid-cols-1 gap-6 items-center pb-4 w-full text-sm leading-none md:grid-cols-[60%_40%]">

            <?php if ($show_filters && !empty($categories)): ?>
                <!-- Filters -->
                <div class="flex gap-4 items-center self-stretch my-auto min-w-0 w-full">
                    <div class="self-stretch my-auto text-sky-950">
                        Filter by:
                    </div>

                    <div
                        class="flex flex-wrap gap-2 items-center self-stretch my-auto font-semibold text-sky-800"
                        role="group"
                        aria-label="Filter posts by category"
                    >
                        <button
                            type="button"
                            class="flex flex-col justify-center items-center self-stretch px-4 py-2 my-auto whitespace-nowrap rounded-full border-2 border-sky-500 min-h-9 w-fit transition-colors duration-200 focus:ring-sky-500"
                            :class="activeFilter === 'all'
                                ? 'bg-sky-500 text-white'
                                : 'bg-white text-sky-800 hover:bg-[#87c0e8] hover:text-white'"
                            @click="setFilter('all')"
                            aria-pressed="true"
                        >
                            All
                        </button>

                        <?php foreach ($categories as $category): ?>
                            <button
                                type="button"
                                class="flex flex-col justify-center items-center self-stretch px-4 py-2 my-auto whitespace-nowrap rounded-full min-h-9 w-fit transition-colors duration-200 focus:ring-sky-500"
                                :class="activeFilter === '<?php echo esc_attr($category->slug); ?>'
                                    ? 'bg-sky-500 text-white'
                                    : 'bg-blue-200 text-sky-800 hover:bg-[#87c0e8] hover:text-white'"
                                @click="setFilter('<?php echo esc_attr($category->slug); ?>')"
                                aria-pressed="false"
                            >
                                <?php echo esc_html($category->name); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($show_search): ?>
                <!-- Search -->
                <div class="flex self-stretch my-auto w-full min-h-[60px] items-center lg:justify-end">
                    <div class="flex-1 shrink basis-12 min-w-0 w-full max-w-[396px] shadow-[0px_0px_20px_rgba(63,0,119,0.07)] rounded-r-[30px] overflow-hidden">
                        <div class="w-full">
                            <div class="flex items-center">
                                <div 
                                class="flex flex-1 gap-2 items-center self-stretch my-auto w-full shrink basis-0 min-w-60 px-4 py-2 max-h-[60px] rounded-l-[4px] border border-transparent bg-white transition-all duration-200 hover:bg-[#CBF3F6] hover:border-[#1C959B] focus-within:bg-[#C2EDFF] focus-within:border-[#C2EDFF]">
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
                                        x-model="searchTerm"
                                        @input="filterPosts()"
                                        aria-label="Search posts"
                                    />
                                </div>

                                <button
                                    type="button"
                                    class="flex gap-2 justify-center items-center px-6 py-4 h-full min-h-[60px] font-bold whitespace-nowrap bg-yellow-300 text-slate-800 max-md:px-5 w-fit hover:bg-[#FCF4C5] focus:ring-yellow-300 border-0 rounded-r-[100px] shadow-none"
                                    @click="filterPosts()"
                                    aria-label="Search posts"
                                >
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>

        <!-- Blog Posts Grid -->
        <main class="flex flex-col mt-4 w-full max-md:max-w-full" role="main" aria-label="Blog posts">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full min-h-[454px] max-md:max-w-full" id="posts-container">

                <?php if ($blog_query->have_posts()): ?>
                    <?php while ($blog_query->have_posts()): $blog_query->the_post(); ?>
                        <?php
                        $post_id         = get_the_ID();
                        $post_categories = get_the_category($post_id);
                        $category_slugs  = array();
                        $primary_category = null;

                        if (!empty($post_categories)) {
                            foreach ($post_categories as $cat) {
                                $category_slugs[] = $cat->slug;
                            }
                            $primary_category = $post_categories[0];
                        }

                        $featured_image = get_post_thumbnail_id($post_id);
                        $image_alt      = get_post_meta($featured_image, '_wp_attachment_image_alt', true) ?: get_the_title();
                        $post_date      = get_the_date('j M Y');
                        $reading_time   = '12 mins';
                        ?>

                        <article
                            class="overflow-hidden flex-1 bg-yellow-50 rounded-[4px] shrink basis-0 min-w-60 post-item cursor-pointer transition-all duration-200 hover:bg-[#FCF4C5] hover:shadow-[0_0_0_4px_#009DE6]"
                            data-categories="<?php echo esc_attr(implode(' ', $category_slugs)); ?>"
                            data-title="<?php echo esc_attr(strtolower(get_the_title())); ?>"
                            data-url="<?php echo esc_url(get_permalink()); ?>"
                            tabindex="0"
                        >
                            <!-- Featured Image with Tag -->
                            <div class="flex overflow-hidden relative flex-col gap-2.5 items-start pt-6 pb-44 w-full text-xs font-bold text-sky-800 whitespace-nowrap aspect-[1.565] min-h-[232px] max-md:pb-24 rounded-t-[8px]">
                                <?php if ($featured_image): ?>
                                    <?php echo wp_get_attachment_image($featured_image, 'large', false, [
                                        'alt'     => esc_attr($image_alt),
                                        'class'   => 'object-cover absolute inset-0 size-full',
                                        'loading' => 'lazy'
                                    ]); ?>
                                <?php endif; ?>

                                <?php if ($primary_category): ?>
                                    <div class="flex overflow-hidden relative gap-1 items-center px-3 py-1 <?php echo $primary_category->slug === 'event' ? 'bg-[#FBEA5E]' : 'bg-sky-300'; ?> rounded-r-[100px]">
                                        <span class="self-stretch my-auto">
                                            <?php echo esc_html($primary_category->name); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Post Content -->
                            <div class="flex flex-col p-6 w-full text-sm text-sky-950 max-md:px-5">
                                <div class="flex gap-2 items-start self-start leading-none <?php echo $is_layout_2 ? 'hidden' : ''; ?>">
                                    <time class="text-sky-950" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>">
                                        <?php echo esc_html($post_date); ?>
                                    </time>
                                    <span class="text-sky-950" aria-hidden="true">|</span>
                                    <span class="text-sky-950">
                                        Read time <?php echo esc_html($reading_time); ?>
                                    </span>
                                </div>

                                <h3 class="mt-2 font-sans text-[18px] font-bold not-italic leading-[24px] <?php echo $is_layout_2 ? 'text-[#F68DA7]' : 'text-sky-800'; ?>">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" class="hover:underline focus:underline">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <div class="mt-2 leading-5 text-sky-950">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else: ?>
                    <div class="py-12 w-full text-center">
                        <p class="text-lg text-gray-600">No posts found.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($show_pagination && $blog_query->max_num_pages > 1): ?>
                <!-- Pagination -->
                <nav
                    class="flex flex-wrap gap-3 md:gap-8 justify-evenly md:justify-center items-center self-center max-w-full pt-8 pb-6 md:pt-20 md:pb-12 mt-6 text-base leading-none text-sky-600 whitespace-nowrap"
                    aria-label="Posts pagination"
                >
                    <!-- Previous Button -->
                    <?php if ($paged > 1): ?>
                        <a
                            href="<?php echo esc_url(get_pagenum_link($paged - 1)); ?>"
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
    $total_pages  = $blog_query->max_num_pages;
    $current_page = $paged;

    $pagination_link_class = 'flex flex-col justify-center items-center self-stretch my-auto w-12 min-h-12 rounded-full text-sky-600 transition-all duration-200 hover:bg-[#009DE6] hover:text-white hover:shadow-[0_0_0_4px_#009DE6] focus:bg-[#009DE6] focus:text-white focus:shadow-[0_0_0_4px_#009DE6]';

    $pagination_current_class = 'flex flex-col justify-center items-center self-stretch my-auto w-12 min-h-12 rounded-full text-white bg-[#009DE6] shadow-[0_0_0_4px_#009DE6]';
    ?>

    <?php if ($current_page == 1): ?>
        <div class="<?php echo esc_attr($pagination_current_class); ?>">
            <span aria-current="page">1</span>
        </div>
    <?php else: ?>
        <a
            href="<?php echo esc_url(get_pagenum_link(1)); ?>"
            class="<?php echo esc_attr($pagination_link_class); ?>"
            aria-label="Go to page 1"
        >
            <span>1</span>
        </a>
    <?php endif; ?>

    <?php for ($i = max(2, $current_page - 2); $i <= min($total_pages - 1, $current_page + 2); $i++): ?>
        <?php if ($i == $current_page): ?>
            <div class="<?php echo esc_attr($pagination_current_class); ?>">
                <span aria-current="page"><?php echo esc_html($i); ?></span>
            </div>
        <?php else: ?>
            <a
                href="<?php echo esc_url(get_pagenum_link($i)); ?>"
                class="<?php echo esc_attr($pagination_link_class); ?>"
                aria-label="Go to page <?php echo esc_attr($i); ?>"
            >
                <span><?php echo esc_html($i); ?></span>
            </a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($total_pages > 5 && $current_page < $total_pages - 2): ?>
        <div class="flex flex-col justify-center items-start self-stretch py-1 my-auto w-6">
            <span class="text-sky-600" aria-hidden="true">...</span>
        </div>
        <a
            href="<?php echo esc_url(get_pagenum_link($total_pages)); ?>"
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
function blogFilter(initialSearchTerm = '') {
    return {
        activeFilter: 'all',
        searchTerm: initialSearchTerm || '',

        setFilter(filter) {
            this.activeFilter = filter;
            this.filterPosts();
        },

        filterPosts() {
            const posts = document.querySelectorAll('.post-item');

            posts.forEach(post => {
                const categories = post.dataset.categories || '';
                const title = post.dataset.title || '';

                const matchesFilter = this.activeFilter === 'all' || categories.includes(this.activeFilter);
                const matchesSearch = this.searchTerm === '' || title.includes(this.searchTerm.toLowerCase());

                if (matchesFilter && matchesSearch) {
                    post.style.display = 'block';
                } else {
                    post.style.display = 'none';
                }
            });
        }
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var cards = document.querySelectorAll('#<?php echo esc_js($section_id); ?> .post-item[data-url]');
    if (!cards.length) return;

    cards.forEach(function (card) {
        card.addEventListener('click', function (e) {
            if (e.target.closest('a, button, input, textarea, select, label, [role="button"]')) return;
            var url = card.getAttribute('data-url');
            if (url) window.location.href = url;
        });

        card.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            if (e.target.closest('a, button, input, textarea, select, label, [role="button"]')) return;
            e.preventDefault();
            var url = card.getAttribute('data-url');
            if (url) window.location.href = url;
        });
    });
});
</script>