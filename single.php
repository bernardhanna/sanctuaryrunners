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
            $event_meta_parts[] = '<span class="inline-flex items-center gap-2 rounded-pill bg-brand-primary-soft px-3 py-1 text-[14px] font-normal leading-5 text-brand-primary-hover"><i class="fa-regular fa-clock text-[14px]" aria-hidden="true"></i><span>' . esc_html($event_when) . '</span></span>';
        }
        if ($event_where !== '') {
            $event_meta_parts[] = '<span class="inline-flex items-center gap-2 rounded-pill bg-brand-primary-soft px-3 py-1 text-[14px] font-normal leading-5 text-brand-primary-hover"><i class="fa-solid fa-location-dot text-[14px]" aria-hidden="true"></i><span>' . esc_html($event_where) . '</span></span>';
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
        if ($post_type === 'post') {
            $archive_label = $posts_page_id ? get_the_title($posts_page_id) : 'News & Media';
            $archive_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/news-and-media/');
        } elseif ($post_type === 'people') {
            $archive_label = 'Our Team';
            $archive_url = home_url('/about/our-team/');
        } elseif ($post_type === 'running_group') {
            $archive_label = 'Running Groups';
            $archive_url = home_url('/find-a-group-near-you/');
        } else {
            $post_type_obj = get_post_type_object($post_type);
            $archive_label = ($post_type_obj && !empty($post_type_obj->labels->name))
                ? (string) $post_type_obj->labels->name
                : 'Archive';
            $archive_url = get_post_type_archive_link($post_type) ?: home_url('/');
        }
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
                $hero_content .= '<div class="mt-3 flex flex-wrap items-center gap-2 text-[14px] leading-5 text-content-body">';
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
    $share_bluesky  = 'https://bsky.app/intent/compose?text=' . $share_title . '%20' . $share_url;
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
                <div class="mb-6 flex flex-wrap items-center gap-3 text-[14px] leading-5 text-content-body" role="group" aria-label="<?php esc_attr_e('Share this post', 'matrix-starter'); ?>">
                    <span class="font-sans font-semibold text-brand-primary-hover"><?php esc_html_e('Share', 'matrix-starter'); ?></span>
                    <a href="<?php echo esc_url($share_facebook); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M17.6055 17.3246H19.8992L20.8167 13.7924H17.6055V12.0262C17.6055 11.1167 17.6055 10.2601 19.4405 10.2601H20.8167V7.293C20.5176 7.25503 19.3882 7.16937 18.1954 7.16937C15.7045 7.16937 13.9356 8.63261 13.9356 11.3198V13.7924H11.1832V17.3246H13.9356V24.8307H17.6055V17.3246Z" fill="#00628F"></path></svg>
                    </a>
                    <a href="<?php echo esc_url($share_twitter); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false"><mask id="single-share-x-top-mask" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="14" height="14"><path d="M15.25 2.75V15.25H2.75V2.75H15.25Z" fill="white" stroke="white"></path></mask><g mask="url(#single-share-x-top-mask)"><path d="M13.8516 3.38257L10.0527 7.73608L9.78516 8.04175L10.0303 8.36597L14.7461 14.6169H11.8311L8.7168 10.5378L8.34473 10.0505L7.94238 10.5125L4.36133 14.6169H3.61523L7.72852 9.90112L7.99707 9.59351L7.75 9.26929L3.26074 3.38354H6.27246L9.06934 7.09253L9.44043 7.58472L9.8457 7.12085L13.1084 3.38257H13.8516ZM4.2666 4.36304L11.7559 14.1785L11.9062 14.3757H14.3047L13.7002 13.574L6.29395 3.75854L6.14453 3.5603H3.6543L4.2666 4.36304Z" fill="#020617" stroke="#0A1119"></path></g></svg>
                    </a>
                    <a href="<?php echo esc_url($share_linkedin); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M11.99 9.60071C11.9898 10.025 11.8215 10.4319 11.5223 10.7318C11.223 11.0317 10.8172 11.2 10.3942 11.1998C9.9712 11.1996 9.5656 11.0308 9.26664 10.7307C8.96767 10.4305 8.79984 10.0234 8.80005 9.59911C8.80026 9.17479 8.9685 8.76793 9.26777 8.46804C9.56703 8.16815 9.9728 7.99979 10.3958 8C10.8188 8.00021 11.2244 8.16898 11.5234 8.46917C11.8223 8.76936 11.9902 9.17639 11.99 9.60071ZM12.0378 12.3846H8.8479V22.4H12.0378V12.3846ZM17.0779 12.3846H13.9039V22.4H17.046V17.1443C17.046 14.2165 20.85 13.9445 20.85 17.1443V22.4H24V16.0564C24 11.1206 18.3698 11.3046 17.046 13.7285L17.0779 12.3846Z" fill="#00628F"></path></svg>
                    </a>
                    <a href="<?php echo esc_url($share_bluesky); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on Bluesky', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M3.46829 0.94239C5.30251 2.31114 7.2757 5.08593 7.99995 6.57504C8.72421 5.08593 10.6975 2.31114 12.5317 0.94239C13.8555 -0.0449892 16 -0.809181 16 1.62217C16 2.10758 15.7199 5.70132 15.5555 6.28489C14.9845 8.31295 12.9032 8.83028 11.052 8.51726C14.288 9.06455 15.1111 10.8772 13.3333 12.69C9.95702 16.133 8.48052 11.8262 8.10248 10.7227C7.99345 10.405 8.00902 10.3977 7.89745 10.7227C7.51939 11.8262 6.04301 16.133 2.66676 12.69C0.888939 10.8772 1.71203 9.06455 4.94798 8.51726C3.09676 8.83028 1.01547 8.31295 0.44447 6.28489C0.280063 5.70132 0 2.10758 0 1.62217C0 -0.809181 2.14479 -0.0449892 3.46829 0.94239Z" fill="#00628F"></path></svg>
                    </a>
                </div>

                <article class="wp_editor prose prose-lg max-w-none prose-headings:font-sans prose-headings:text-brand-primary-hover prose-p:font-sans prose-p:text-[16px] prose-p:leading-[22px] prose-p:text-content-body prose-a:text-brand-primary-hover">
                    <?php the_content(); ?>
                </article>

                <?php if ($post_type === 'event' && is_array($event_registration_link) && !empty($event_registration_link['url'])) : ?>
                    <div class="mt-8">
                        <a
                            href="<?php echo esc_url($event_registration_link['url']); ?>"
                            target="<?php echo esc_attr($event_registration_link['target'] ?? '_blank'); ?>"
                            rel="<?php echo esc_attr(($event_registration_link['target'] ?? '_blank') === '_blank' ? 'noopener' : ''); ?>"
                            class="inline-flex h-[52px] items-center justify-center gap-2 rounded-pill bg-brand-primary px-6 py-4 font-['Public_Sans'] text-[14px] font-bold leading-5 text-white transition-colors duration-200 hover:bg-brand-primary-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-accent focus-visible:ring-offset-2"
                        >
                            <?php echo esc_html($event_registration_label); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-8 pt-6 border-t border-[var(--Gray-200,#D0D5DD)] flex flex-wrap items-center gap-3 text-[14px] leading-5 text-content-body" role="group" aria-label="<?php esc_attr_e('Share this post', 'matrix-starter'); ?>">
                    <span class="font-sans font-semibold text-brand-primary-hover"><?php esc_html_e('Share', 'matrix-starter'); ?></span>
                    <a href="<?php echo esc_url($share_facebook); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M17.6055 17.3246H19.8992L20.8167 13.7924H17.6055V12.0262C17.6055 11.1167 17.6055 10.2601 19.4405 10.2601H20.8167V7.293C20.5176 7.25503 19.3882 7.16937 18.1954 7.16937C15.7045 7.16937 13.9356 8.63261 13.9356 11.3198V13.7924H11.1832V17.3246H13.9356V24.8307H17.6055V17.3246Z" fill="#00628F"></path></svg>
                    </a>
                    <a href="<?php echo esc_url($share_twitter); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false"><mask id="single-share-x-bottom-mask" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="2" width="14" height="14"><path d="M15.25 2.75V15.25H2.75V2.75H15.25Z" fill="white" stroke="white"></path></mask><g mask="url(#single-share-x-bottom-mask)"><path d="M13.8516 3.38257L10.0527 7.73608L9.78516 8.04175L10.0303 8.36597L14.7461 14.6169H11.8311L8.7168 10.5378L8.34473 10.0505L7.94238 10.5125L4.36133 14.6169H3.61523L7.72852 9.90112L7.99707 9.59351L7.75 9.26929L3.26074 3.38354H6.27246L9.06934 7.09253L9.44043 7.58472L9.8457 7.12085L13.1084 3.38257H13.8516ZM4.2666 4.36304L11.7559 14.1785L11.9062 14.3757H14.3047L13.7002 13.574L6.29395 3.75854L6.14453 3.5603H3.6543L4.2666 4.36304Z" fill="#020617" stroke="#0A1119"></path></g></svg>
                    </a>
                    <a href="<?php echo esc_url($share_linkedin); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M11.99 9.60071C11.9898 10.025 11.8215 10.4319 11.5223 10.7318C11.223 11.0317 10.8172 11.2 10.3942 11.1998C9.9712 11.1996 9.5656 11.0308 9.26664 10.7307C8.96767 10.4305 8.79984 10.0234 8.80005 9.59911C8.80026 9.17479 8.9685 8.76793 9.26777 8.46804C9.56703 8.16815 9.9728 7.99979 10.3958 8C10.8188 8.00021 11.2244 8.16898 11.5234 8.46917C11.8223 8.76936 11.9902 9.17639 11.99 9.60071ZM12.0378 12.3846H8.8479V22.4H12.0378V12.3846ZM17.0779 12.3846H13.9039V22.4H17.046V17.1443C17.046 14.2165 20.85 13.9445 20.85 17.1443V22.4H24V16.0564C24 11.1206 18.3698 11.3046 17.046 13.7285L17.0779 12.3846Z" fill="#00628F"></path></svg>
                    </a>
                    <a href="<?php echo esc_url($share_bluesky); ?>" class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn" aria-label="<?php esc_attr_e('Share on Bluesky', 'matrix-starter'); ?>" target="_blank" rel="noopener">
                        <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M3.46829 0.94239C5.30251 2.31114 7.2757 5.08593 7.99995 6.57504C8.72421 5.08593 10.6975 2.31114 12.5317 0.94239C13.8555 -0.0449892 16 -0.809181 16 1.62217C16 2.10758 15.7199 5.70132 15.5555 6.28489C14.9845 8.31295 12.9032 8.83028 11.052 8.51726C14.288 9.06455 15.1111 10.8772 13.3333 12.69C9.95702 16.133 8.48052 11.8262 8.10248 10.7227C7.99345 10.405 8.00902 10.3977 7.89745 10.7227C7.51939 11.8262 6.04301 16.133 2.66676 12.69C0.888939 10.8772 1.71203 9.06455 4.94798 8.51726C3.09676 8.83028 1.01547 8.31295 0.44447 6.28489C0.280063 5.70132 0 2.10758 0 1.62217C0 -0.809181 2.14479 -0.0449892 3.46829 0.94239Z" fill="#00628F"></path></svg>
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
            <p class="font-sans text-[16px] text-content-body">No content found.</p>
        </div>
    </section>
<?php endif; ?>
</main>

<?php get_footer(); ?>