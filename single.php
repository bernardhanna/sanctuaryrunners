<?php
get_header();
?>

<main class="overflow-hidden w-full min-h-screen site-main">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php
    $post_id = get_the_ID();
    $title = get_the_title();
    $excerpt = trim((string) get_the_excerpt());
    $featured_id = get_post_thumbnail_id($post_id);
    $featured_alt = $featured_id ? (get_post_meta($featured_id, '_wp_attachment_image_alt', true) ?: $title) : '';

    $content_for_rt = get_post_field('post_content', $post_id);
    $word_count = str_word_count(wp_strip_all_tags((string) $content_for_rt));
    $read_time = max(1, (int) ceil($word_count / 200));

    $posts_page_id = (int) get_option('page_for_posts');
    $news_media_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/news-and-media/');

    $breadcrumbs = [
        ['title' => 'Home', 'url' => home_url('/'), 'is_current' => false],
        ['title' => 'News & Media', 'url' => $news_media_url, 'is_current' => false],
        ['title' => $title, 'url' => '', 'is_current' => true],
    ];
    ?>

    <?php
    get_template_part('template-parts/hero/subhero', null, [
        'heading'            => $title,
        'heading_tag'        => 'h1',
        'content'            => $excerpt ?: 'Read the latest from Sanctuary Runners.',
        'layout_option'      => 'layout_2',
        'background_color'   => '#EEF6FC',
        'use_white_text'     => false,
        'custom_breadcrumbs' => true,
        'breadcrumbs'        => $breadcrumbs,
        'image'              => $featured_id ?: null,
        'primary_cta'        => null,
        'secondary_cta'      => null,
    ]);
    ?>

    <section class="flex overflow-hidden relative bg-white">
        <div class="px-5 pt-8 pb-16 mx-auto w-full max-w-container lg:pt-12">
            <div class="mx-auto w-full max-w-[860px]">
                <div class="mb-6 flex flex-wrap items-center gap-3 text-[14px] leading-5 text-[var(--Gray-700,#00263E)]">
                    <time datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>" class="font-sans">
                        <?php echo esc_html(get_the_date('j M Y')); ?>
                    </time>
                    <span aria-hidden="true">|</span>
                    <span class="font-sans"><?php echo esc_html($read_time); ?> min read</span>
                    <?php
                    $cats = get_the_category($post_id);
                    if (!empty($cats)) :
                    ?>
                        <span aria-hidden="true">|</span>
                        <span class="font-sans"><?php echo esc_html($cats[0]->name); ?></span>
                    <?php endif; ?>
                </div>

                <article class="wp_editor prose prose-lg max-w-none prose-headings:font-sans prose-headings:text-[var(--Blue-SR-500,#00628F)] prose-p:font-sans prose-p:text-[16px] prose-p:leading-[22px] prose-p:text-[var(--Gray-700,#00263E)] prose-a:text-[var(--Blue-SR-500,#00628F)]">
                    <?php the_content(); ?>
                </article>
            </div>
        </div>
    </section>

    <?php if (get_post_type() === 'post') : ?>
        <?php get_template_part('template-parts/single/related-posts'); ?>
        <?php get_template_part('template-parts/flexi/newsletter_001'); ?>
    <?php endif; ?>

<?php endwhile; else : ?>
    <section class="py-16">
        <div class="px-5 mx-auto w-full text-center max-w-container">
            <p class="font-sans text-[16px] text-[var(--Gray-700,#00263E)]">No content found.</p>
        </div>
    </section>
<?php endif; ?>
</main>

<?php get_footer(); ?>