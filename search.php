<?php
/**
 * Search results template.
 * Displays grouped results across all searchable public post types.
 */

get_header();

$search_term = trim((string) get_search_query());
$heading = $search_term !== '' ? sprintf('Search results for "%s"', $search_term) : 'Search results';
$description = $search_term !== ''
    ? 'Showing results from across the site.'
    : 'Type a search term to find content across the site.';

$breadcrumbs = [
    [
        'title'      => 'Home',
        'url'        => home_url('/'),
        'is_current' => false,
    ],
    [
        'title'      => $heading,
        'url'        => '',
        'is_current' => true,
    ],
];

get_template_part('template-parts/hero/subhero', null, matrix_get_archive_subhero_media_args(get_queried_object(), [
    'heading'            => $heading,
    'heading_tag'        => 'h1',
    'content'            => $description,
    'layout_option'      => 'layout_2',
    'background_color'   => '#EEF6FC',
    'use_white_text'     => false,
    'custom_breadcrumbs' => true,
    'breadcrumbs'        => $breadcrumbs,
    'primary_cta'        => null,
    'secondary_cta'      => null,
]));
?>

<main id="main-content" class="site-main">
    <section class="relative overflow-hidden">
        <div class="mx-auto w-full max-w-[1158px] px-5 py-12">
            <div class="mb-8 flex items-center justify-between gap-4">
                <p class="text-base text-slate-600">
                    <?php
                    global $wp_query;
                    echo esc_html(sprintf('%d result%s', (int) $wp_query->found_posts, (int) $wp_query->found_posts === 1 ? '' : 's'));
                    ?>
                </p>
                <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex w-full max-w-[420px] items-center gap-2" role="search" aria-label="Search all site content">
                    <label for="search-results-input" class="sr-only">Search all site content</label>
                    <input
                        id="search-results-input"
                        type="search"
                        name="s"
                        value="<?php echo esc_attr($search_term); ?>"
                        placeholder="Search all content"
                        class="h-[48px] w-full rounded-[999px] border border-slate-200 px-4 text-sm text-slate-700"
                    />
                    <button type="submit" class="h-[48px] rounded-[999px] px-5 btn-primary">Search</button>
                </form>
            </div>

            <?php if (have_posts()) : ?>
                <?php
                $grouped_results = [];
                while (have_posts()) :
                    the_post();
                    $post_type = get_post_type() ?: 'post';
                    $grouped_results[$post_type][] = get_post();
                endwhile;
                wp_reset_postdata();
                ?>

                <div class="space-y-10">
                    <?php foreach ($grouped_results as $post_type => $results) : ?>
                        <?php
                        $post_type_object = get_post_type_object($post_type);
                        $group_heading = $post_type_object && !empty($post_type_object->labels->name)
                            ? $post_type_object->labels->name
                            : ucfirst($post_type);
                        ?>
                        <section aria-label="<?php echo esc_attr($group_heading); ?> results">
                            <h2 class="mb-4 text-2xl font-bold text-slate-800"><?php echo esc_html($group_heading); ?></h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <?php foreach ($results as $result_post) : ?>
                                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                                        <h3 class="mb-1 text-lg font-bold text-slate-900">
                                            <a class="hover:text-[#008BCC]" href="<?php echo esc_url(get_permalink($result_post)); ?>">
                                                <?php echo esc_html(get_the_title($result_post)); ?>
                                            </a>
                                        </h3>
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <?php echo esc_html(get_the_date('j F Y', $result_post)); ?>
                                        </p>
                                        <p class="text-sm text-slate-600">
                                            <?php echo esc_html(wp_trim_words(wp_strip_all_tags((string) get_the_excerpt($result_post)), 24)); ?>
                                        </p>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>

                <?php
                the_posts_pagination([
                    'mid_size'  => 1,
                    'prev_text' => __('Previous', 'matrix-starter'),
                    'next_text' => __('Next', 'matrix-starter'),
                ]);
                ?>
            <?php else : ?>
                <div class="rounded-lg border border-slate-200 bg-white p-8 text-center">
                    <h2 class="mb-2 text-xl font-bold text-slate-800">No results found</h2>
                    <p class="text-slate-600">Try a different keyword or shorter phrase.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();

