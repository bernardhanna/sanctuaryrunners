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

    $post_type = get_post_type($post_id);
    $posts_page_id = (int) get_option('page_for_posts');
    $event_registration_link = null;
    $event_registration_label = 'Register for event';
    $hero_primary_cta = null;
    $hero_layout_option = 'layout_1';
    $hero_layout_2_padding_class = 'pt-[0rem] pb-0 md:pb-0';

    if ($post_type === 'event') {
        $archive_label = post_type_archive_title('', false) ?: 'Events';
        $archive_url = get_post_type_archive_link('event') ?: home_url('/events/');
        $event_registration_link = function_exists('get_field') ? get_field('event_registration_link', $post_id) : null;
        $event_registration_custom_label = function_exists('get_field') ? trim((string) get_field('event_registration_label', $post_id)) : '';
        if ($event_registration_custom_label !== '') {
            $event_registration_label = $event_registration_custom_label;
        }

        $event_start_raw = (string) get_post_meta($post_id, 'event_start', true);
        $event_when = '';
        if ($event_start_raw !== '') {
            $event_dt = DateTime::createFromFormat('Y-m-d H:i:s', $event_start_raw);
            if ($event_dt instanceof DateTime) {
                $event_when = $event_dt->format('j M Y \a\t gA');
            }
        }

        $event_locations = get_the_terms($post_id, 'event_location');
        $event_where = '';
        if (!empty($event_locations) && !is_wp_error($event_locations)) {
            $event_where = implode(', ', wp_list_pluck($event_locations, 'name'));
        }

        $event_meta_parts = [];
        if ($event_when !== '') {
            $event_meta_parts[] = '<span class="inline-flex items-center gap-2 rounded-full bg-[#EBF9FF] px-3 py-1 text-[14px] font-normal leading-5 text-[var(--Blue-SR-500,#00628F)]"><i class="fa-regular fa-clock text-[14px]" aria-hidden="true"></i><span>' . esc_html($event_when) . '</span></span>';
        }
        if ($event_where !== '') {
            $event_meta_parts[] = '<span class="inline-flex items-center gap-2 rounded-full bg-[#EBF9FF] px-3 py-1 text-[14px] font-normal leading-5 text-[var(--Blue-SR-500,#00628F)]"><i class="fa-solid fa-location-dot text-[14px]" aria-hidden="true"></i><span>' . esc_html($event_where) . '</span></span>';
        }

        $hero_content = '';
        if (!empty($event_meta_parts)) {
            $hero_content .= '<div class="flex flex-wrap gap-2 items-center mt-3">' . implode('', $event_meta_parts) . '</div>';
        }

        if (is_array($event_registration_link) && !empty($event_registration_link['url'])) {
            $hero_primary_cta = [
                'url' => $event_registration_link['url'],
                'title' => $event_registration_label,
                'target' => !empty($event_registration_link['target']) ? $event_registration_link['target'] : '_blank',
            ];
        }

        $hero_layout_option = 'layout_2';
        $hero_layout_2_padding_class = 'pt-[0rem] pb-8 md:pb-12';
    } else {
        $archive_label = $posts_page_id ? get_the_title($posts_page_id) : 'News & Media';
        $archive_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/news-and-media/');
        $hero_content = '';

        if ($excerpt !== '') {
            $hero_content .= wpautop($excerpt);
        }

        // Show publish/read meta only on standard blog posts.
        if ($post_type === 'post') {
            $post_meta_parts = [];
            $post_meta_parts[] = '<span>' . esc_html(get_the_date('j M Y')) . '</span>';
            $post_meta_parts[] = '<span>' . esc_html($read_time) . ' min read</span>';
            $cats = get_the_category($post_id);
            if (!empty($cats)) {
                $post_meta_parts[] = '<span>' . esc_html($cats[0]->name) . '</span>';
            }

            if (!empty($post_meta_parts)) {
                $hero_content .= '<div class="mt-3 flex flex-wrap items-center gap-2 text-[14px] leading-5 text-[var(--Gray-700,#00263E)]">';
                foreach ($post_meta_parts as $index => $part) {
                    if ($index > 0) {
                        $hero_content .= '<span aria-hidden="true">|</span>';
                    }
                    $hero_content .= $part;
                }
                $hero_content .= '</div>';
            }
        }
    }

    $breadcrumbs = [
        ['title' => 'Home', 'url' => home_url('/'), 'is_current' => false],
        ['title' => $archive_label, 'url' => $archive_url, 'is_current' => false],
        ['title' => $title, 'url' => '', 'is_current' => true],
    ];

    $post_permalink = get_permalink($post_id);
    $share_url      = rawurlencode((string) $post_permalink);
    $share_title    = rawurlencode(wp_strip_all_tags($title));
    $share_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url;
    $share_twitter  = 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title;
    $share_linkedin = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_url;
    ?>

    <?php
    get_template_part('template-parts/hero/subhero', null, [
        'heading'            => $title,
        'heading_tag'        => 'h1',
        'content'            => $hero_content,
        'layout_2_container_padding_class' => $hero_layout_2_padding_class,
        'disable_mobile_absolute_media' => true,
        'mobile_image_contain' => true,
        'section_extra_classes' => 'max-md:mt-2',
        'layout_option'      => $hero_layout_option,
        'background_color'   => '#EEF6FC',
        'use_white_text'     => false,
        'custom_breadcrumbs' => true,
        'breadcrumbs'        => $breadcrumbs,
        'image'              => $featured_id ?: null,
        'primary_cta'        => $hero_primary_cta,
        'secondary_cta'      => null,
    ]);
    ?>

    <section class="flex overflow-hidden relative bg-white">
        <div class="px-5 pt-8 pb-16 mx-auto w-full max-w-container lg:pt-12">
            <div class="mx-auto w-full max-w-[860px]">
                <div class="mb-6 flex flex-wrap items-center gap-3 text-[14px] leading-5 text-[var(--Gray-700,#00263E)]" role="group" aria-label="<?php esc_attr_e('Share this post', 'matrix-starter'); ?>">
                    <span class="font-sans font-semibold text-[var(--Blue-SR-500,#00628F)]"><?php esc_html_e('Share', 'matrix-starter'); ?></span>
                    <a href="<?php echo esc_url($share_facebook); ?>" class="btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg class="object-contain w-7 h-7 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M22 12.06C22 6.48 17.52 2 11.94 2 6.36 2 1.88 6.48 1.88 12.06c0 4.99 3.65 9.13 8.43 9.94v-7.03H7.93v-2.91h2.38V9.41c0-2.35 1.4-3.65 3.55-3.65 1.03 0 2.1.18 2.1.18v2.3h-1.18c-1.16 0-1.52.72-1.52 1.46v1.77h2.58l-.41 2.91h-2.17V22c4.78-.81 8.43-4.95 8.43-9.94Z"/>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url($share_twitter); ?>" class="btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                            <mask id="single-share-x-top-mask" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="14" height="14">
                                <path d="M15.25 2.75V15.25H2.75V2.75H15.25Z" fill="white" stroke="white"/>
                            </mask>
                            <g mask="url(#single-share-x-top-mask)">
                                <path d="M13.8516 3.38257L10.0527 7.73608L9.78516 8.04175L10.0303 8.36597L14.7461 14.6169H11.8311L8.7168 10.5378L8.34473 10.0505L7.94238 10.5125L4.36133 14.6169H3.61523L7.72852 9.90112L7.99707 9.59351L7.75 9.26929L3.26074 3.38354H6.27246L9.06934 7.09253L9.44043 7.58472L9.8457 7.12085L13.1084 3.38257H13.8516ZM4.2666 4.36304L11.7559 14.1785L11.9062 14.3757H14.3047L13.7002 13.574L6.29395 3.75854L6.14453 3.5603H3.6543L4.2666 4.36304Z" fill="#020617" stroke="#0A1119"/>
                            </g>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url($share_linkedin); ?>" class="btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                            <path d="M12.4502 7.33887C13.6967 7.33892 14.3319 7.67001 14.6934 8.17383C15.0861 8.72126 15.25 9.59212 15.25 10.8916V15.25H13.5498V11.4893C13.5498 10.7202 13.4606 10.0373 13.1992 9.53125C13.0641 9.26967 12.8763 9.04361 12.623 8.88477C12.3685 8.72512 12.0784 8.65332 11.7695 8.65332C10.9983 8.65332 10.3839 8.93703 9.9834 9.49316C9.60385 10.0204 9.4649 10.7268 9.46484 11.4883V15.25H7.76465V7.4541H9.27148V8.20312L10.2178 8.42773C10.4791 7.90806 11.2123 7.33887 12.4502 7.33887ZM4.83496 7.33887V15.1348H3.13477V7.33887H4.83496ZM3.98535 2.75C4.31409 2.75 4.62908 2.87965 4.86133 3.10938C5.09251 3.33816 5.22168 3.64829 5.22168 3.97168C5.22172 4.13164 5.18959 4.28991 5.12793 4.4375C5.06622 4.58518 4.97623 4.71962 4.8623 4.83203L4.86035 4.83301C4.74537 4.94738 4.60899 5.03796 4.45898 5.09961C4.30901 5.16123 4.14847 5.19284 3.98633 5.19238C3.69897 5.19174 3.42179 5.0922 3.2002 4.91309L3.1084 4.83203C2.99537 4.71919 2.90524 4.58492 2.84375 4.4375C2.78225 4.2899 2.75052 4.13158 2.75 3.97168C2.75 3.64829 2.87917 3.33816 3.11035 3.10938C3.3141 2.90763 3.58035 2.78345 3.86328 2.75586L3.98535 2.75Z" fill="#020617" stroke="#0A1119"/>
                        </svg>
                    </a>
                </div>

                <article class="wp_editor prose prose-lg max-w-none prose-headings:font-sans prose-headings:text-[var(--Blue-SR-500,#00628F)] prose-p:font-sans prose-p:text-[16px] prose-p:leading-[22px] prose-p:text-[var(--Gray-700,#00263E)] prose-a:text-[var(--Blue-SR-500,#00628F)]">
                    <?php the_content(); ?>
                </article>

                <?php if ($post_type === 'event' && is_array($event_registration_link) && !empty($event_registration_link['url'])) : ?>
                    <div class="mt-8">
                        <a
                            href="<?php echo esc_url($event_registration_link['url']); ?>"
                            target="<?php echo esc_attr($event_registration_link['target'] ?? '_blank'); ?>"
                            rel="<?php echo esc_attr(($event_registration_link['target'] ?? '_blank') === '_blank' ? 'noopener' : ''); ?>"
                            class="inline-flex h-[52px] items-center justify-center gap-2 rounded-full bg-[#008BCC] px-6 py-4 font-['Public_Sans'] text-[14px] font-bold leading-5 text-white transition-colors duration-200 hover:bg-[#00628F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1C959B] focus-visible:ring-offset-2"
                        >
                            <?php echo esc_html($event_registration_label); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-8 pt-6 border-t border-[var(--Gray-200,#D0D5DD)] flex flex-wrap items-center gap-3 text-[14px] leading-5 text-[var(--Gray-700,#00263E)]" role="group" aria-label="<?php esc_attr_e('Share this post', 'matrix-starter'); ?>">
                    <span class="font-sans font-semibold text-[var(--Blue-SR-500,#00628F)]"><?php esc_html_e('Share', 'matrix-starter'); ?></span>
                    <a href="<?php echo esc_url($share_facebook); ?>" class="btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg class="object-contain w-7 h-7 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M22 12.06C22 6.48 17.52 2 11.94 2 6.36 2 1.88 6.48 1.88 12.06c0 4.99 3.65 9.13 8.43 9.94v-7.03H7.93v-2.91h2.38V9.41c0-2.35 1.4-3.65 3.55-3.65 1.03 0 2.1.18 2.1.18v2.3h-1.18c-1.16 0-1.52.72-1.52 1.46v1.77h2.58l-.41 2.91h-2.17V22c4.78-.81 8.43-4.95 8.43-9.94Z"/>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url($share_twitter); ?>" class="btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                            <mask id="single-share-x-bottom-mask" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="14" height="14">
                                <path d="M15.25 2.75V15.25H2.75V2.75H15.25Z" fill="white" stroke="white"/>
                            </mask>
                            <g mask="url(#single-share-x-bottom-mask)">
                                <path d="M13.8516 3.38257L10.0527 7.73608L9.78516 8.04175L10.0303 8.36597L14.7461 14.6169H11.8311L8.7168 10.5378L8.34473 10.0505L7.94238 10.5125L4.36133 14.6169H3.61523L7.72852 9.90112L7.99707 9.59351L7.75 9.26929L3.26074 3.38354H6.27246L9.06934 7.09253L9.44043 7.58472L9.8457 7.12085L13.1084 3.38257H13.8516ZM4.2666 4.36304L11.7559 14.1785L11.9062 14.3757H14.3047L13.7002 13.574L6.29395 3.75854L6.14453 3.5603H3.6543L4.2666 4.36304Z" fill="#020617" stroke="#0A1119"/>
                            </g>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url($share_linkedin); ?>" class="btn w-fit hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                            <path d="M12.4502 7.33887C13.6967 7.33892 14.3319 7.67001 14.6934 8.17383C15.0861 8.72126 15.25 9.59212 15.25 10.8916V15.25H13.5498V11.4893C13.5498 10.7202 13.4606 10.0373 13.1992 9.53125C13.0641 9.26967 12.8763 9.04361 12.623 8.88477C12.3685 8.72512 12.0784 8.65332 11.7695 8.65332C10.9983 8.65332 10.3839 8.93703 9.9834 9.49316C9.60385 10.0204 9.4649 10.7268 9.46484 11.4883V15.25H7.76465V7.4541H9.27148V8.20312L10.2178 8.42773C10.4791 7.90806 11.2123 7.33887 12.4502 7.33887ZM4.83496 7.33887V15.1348H3.13477V7.33887H4.83496ZM3.98535 2.75C4.31409 2.75 4.62908 2.87965 4.86133 3.10938C5.09251 3.33816 5.22168 3.64829 5.22168 3.97168C5.22172 4.13164 5.18959 4.28991 5.12793 4.4375C5.06622 4.58518 4.97623 4.71962 4.8623 4.83203L4.86035 4.83301C4.74537 4.94738 4.60899 5.03796 4.45898 5.09961C4.30901 5.16123 4.14847 5.19284 3.98633 5.19238C3.69897 5.19174 3.42179 5.0922 3.2002 4.91309L3.1084 4.83203C2.99537 4.71919 2.90524 4.58492 2.84375 4.4375C2.78225 4.2899 2.75052 4.13158 2.75 3.97168C2.75 3.64829 2.87917 3.33816 3.11035 3.10938C3.3141 2.90763 3.58035 2.78345 3.86328 2.75586L3.98535 2.75Z" fill="#020617" stroke="#0A1119"/>
                        </svg>
                    </a>
                </div>
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