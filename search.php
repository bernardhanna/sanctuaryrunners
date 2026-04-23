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

$pdf_results = [];
if ($search_term !== '') {
    $allowed_pdf_url = function_exists('matrix_allowed_search_pdf_url')
        ? matrix_allowed_search_pdf_url()
        : 'https://sanctuaryrunners.s1.matrix-test.com/wp-content/uploads/2026/03/Code-of-Conduct-February-2026.pdf';
    $pdf_query = new WP_Query([
        'post_type'              => 'attachment',
        'post_status'            => 'inherit',
        'post_mime_type'         => 'application/pdf',
        'posts_per_page'         => 12,
        's'                      => $search_term,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);
    if ($pdf_query->have_posts()) {
        while ($pdf_query->have_posts()) {
            $pdf_query->the_post();
            $candidate_url = (string) wp_get_attachment_url(get_the_ID());
            if ($candidate_url === '' || $candidate_url !== $allowed_pdf_url) {
                continue;
            }
            $pdf_results[] = get_post();
        }
        wp_reset_postdata();
    }
}
?>

<main id="main-content" class="site-main">
    <section class="relative overflow-hidden" data-disable-nav-offset="true">
        <div class="mx-auto w-full max-w-[1158px] px-5 py-12">
            <div class="mb-8 flex items-center justify-between gap-4">
                <p class="text-base text-slate-600">
                    <?php
                    global $wp_query;
                    $total_results = (int) $wp_query->found_posts + count($pdf_results);
                    echo esc_html(sprintf('%d result%s', $total_results, $total_results === 1 ? '' : 's'));
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

            <?php
                $grouped_results = [];
                if (have_posts()) {
                while (have_posts()) :
                    the_post();
                    $post_type = get_post_type() ?: 'post';
                    $grouped_results[$post_type][] = get_post();
                endwhile;
                wp_reset_postdata();
                }
                if (!empty($pdf_results)) {
                    $grouped_results['attachment_pdf'] = $pdf_results;
                }
            ?>

            <?php if (!empty($grouped_results)) : ?>
                <div class="space-y-10">
                    <?php foreach ($grouped_results as $post_type => $results) : ?>
                        <?php
                        if ($post_type === 'attachment_pdf') {
                            $group_heading = 'PDFs';
                        } else {
                            $post_type_object = get_post_type_object($post_type);
                            $group_heading = $post_type_object && !empty($post_type_object->labels->name)
                                ? $post_type_object->labels->name
                                : ucfirst($post_type);
                        }
                        ?>
                        <section aria-label="<?php echo esc_attr($group_heading); ?> results">
                            <h2 class="mb-4 text-2xl font-bold text-slate-800"><?php echo esc_html($group_heading); ?></h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <?php foreach ($results as $result_post) : ?>
                                    <?php
                                    $result_type = get_post_type($result_post);
                                    $result_url = $result_type === 'attachment'
                                        ? wp_get_attachment_url((int) $result_post->ID)
                                        : get_permalink($result_post);
                                    $result_title_raw = (string) get_the_title($result_post);
                                    $result_title = wp_strip_all_tags(wp_specialchars_decode($result_title_raw, ENT_QUOTES));
                                    $result_excerpt = trim((string) get_the_excerpt($result_post));
                                    if ($result_excerpt === '') {
                                        $result_excerpt = trim((string) get_post_field('post_excerpt', (int) $result_post->ID));
                                    }
                                    if ($result_excerpt === '') {
                                        $result_excerpt = trim((string) get_post_field('post_content', (int) $result_post->ID));
                                    }
                                    if ($result_type === 'attachment' && $result_excerpt === '') {
                                        $result_excerpt = 'PDF document';
                                    }
                                    ?>
                                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                                        <h3 class="mb-1 text-lg font-bold text-slate-900">
                                            <a class="hover:text-[#008BCC]" href="<?php echo esc_url((string) $result_url); ?>">
                                                <?php echo esc_html($result_title); ?>
                                            </a>
                                        </h3>
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <?php echo esc_html(get_the_date('j F Y', $result_post)); ?>
                                        </p>
                                        <p class="text-sm text-slate-600">
                                            <?php echo esc_html(wp_trim_words(wp_strip_all_tags($result_excerpt), 24)); ?>
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

