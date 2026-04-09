<?php
/**
 * Author Section – Design-matched, with Team featured image > Gravatar fallback
 */

if (!defined('ABSPATH')) exit;

$author_id      = (int) get_post_field('post_author', get_the_ID());
$author_name    = get_the_author_meta('display_name', $author_id);
$post_permalink = get_permalink();
$post_title     = get_the_title();

// Attempt to find Team post where Title === Author Display Name
$team_post      = null;
$team_link      = '';
$profile_img    = ''; // final URL we will render

$team_q = new WP_Query([
    'post_type'           => 'team',
    'posts_per_page'      => 1,
    'post_status'         => 'publish',
    'title'               => $author_name, // exact match param
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
]);

if ($team_q->have_posts()) {
    $team_q->the_post();
    $team_post = get_post(get_the_ID());
    $team_link = get_permalink($team_post->ID);

    // Try team featured image first
    $thumb_id = get_post_thumbnail_id($team_post->ID);
    if ($thumb_id) {
        $profile_img = wp_get_attachment_image_url($thumb_id, 'medium_large');
    }
}
wp_reset_postdata();

// Fallbacks
if (empty($team_link)) {
    $team_link = get_author_posts_url($author_id);
}
if (empty($profile_img)) {
    // If no team featured image (or no team match), use author avatar/gravatar
    $profile_img = get_avatar_url($author_id, ['size' => 200]);
}

// Last update date
$modified_ts   = get_post_modified_time('U', true);
$datetime_attr = gmdate('Y-m-d', $modified_ts);
$human_date    = date_i18n('j F Y', $modified_ts);

// Share URLs
$share_url   = rawurlencode($post_permalink);
$share_title = rawurlencode(wp_strip_all_tags($post_title));

$share_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url;
$share_twitter  = 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title;
$share_linkedin = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_url;
$share_bluesky  = 'https://bsky.app/intent/compose?text=' . $share_title . '%20' . $share_url;
?>

<section class="flex overflow-hidden relative bg-[#ededed]">
  <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full max-w-container max-lg:px-5">
    <div class="flex flex-wrap gap-10 justify-between items-center w-full text-black">
      <figure class="flex gap-8 items-center min-w-60 max-md:max-w-full">
        <a href="<?php echo esc_url($team_link); ?>" class="block" aria-label="<?php echo esc_attr($author_name); ?>">
          <img
            src="<?php echo esc_url($profile_img); ?>"
            alt="<?php echo esc_attr('Profile photo of ' . $author_name); ?>"
            class="object-cover shrink-0 aspect-square rounded-full w-[140px] h-[140px]"
            loading="lazy"
            decoding="async"
          />
        </a>
        <div class="flex flex-col justify-center min-w-60">
          <span class="text-2xl font-semibold leading-none text-black text-seconary">
            <a href="<?php echo esc_url($team_link); ?>" class="hover:opacity-80">
              <?php echo esc_html(sprintf('by %s', $author_name)); ?>
            </a>
          </span>
          <time class="mt-2 text-lg leading-none text-black" datetime="<?php echo esc_attr($datetime_attr); ?>">
            <?php echo esc_html(sprintf(__('Last update: %s', 'matrix-starter'), $human_date)); ?>
          </time>
        </div>
      </figure>

      <div class="flex gap-4 items-center text-lg leading-none min-w-60" role="group" aria-label="<?php esc_attr_e('Share this post', 'matrix-starter'); ?>">
        <span class="text-black"><?php esc_html_e('Share this post', 'matrix-starter'); ?></span>

        <a
          href="<?php echo esc_url($share_facebook); ?>"
          class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-brand-primary-hover focus:ring-offset-2 focus:ring-offset-white btn"
          aria-label="<?php esc_attr_e('Share on Facebook', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.6-1.6H16.7V4.8c-.3 0-1.4-.1-2.7-.1-2.7 0-4.5 1.6-4.5 4.6V11H6.8v3h2.7v8h4Z"/>
          </svg>
        </a>

        <a
          href="<?php echo esc_url($share_twitter); ?>"
          class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-brand-primary-hover focus:ring-offset-2 focus:ring-offset-white btn"
          aria-label="<?php esc_attr_e('Share on X (Twitter)', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M18.9 2H21l-6.6 7.5L22.5 22h-6.7l-4.7-6.1L5.7 22H3.5l7.1-8.1L1.5 2H8.4l4.2 5.6L18.9 2Zm-2.3 18h1.7L7.4 3.9H5.6l11 16.1Z"/>
          </svg>
        </a>

        <a
          href="<?php echo esc_url($share_linkedin); ?>"
          class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-brand-primary-hover focus:ring-offset-2 focus:ring-offset-white btn"
          aria-label="<?php esc_attr_e('Share on LinkedIn', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M6.5 3.8A2.3 2.3 0 1 1 6.5 8.4a2.3 2.3 0 0 1 0-4.6ZM4.4 20.5V9.3h4.2v11.2H4.4Zm6.8 0V9.3h4v1.5h.1c.6-1 2-1.9 3.9-1.9 4.2 0 5 2.8 5 6.4v5.2H20v-4.6c0-1.1 0-2.6-1.6-2.6s-1.8 1.2-1.8 2.5v4.7h-4.2Z"/>
          </svg>
        </a>

        <a
          href="<?php echo esc_url($share_bluesky); ?>"
          class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-brand-primary-hover focus:ring-offset-2 focus:ring-offset-white btn"
          aria-label="<?php esc_attr_e('Share on Bluesky', 'matrix-starter'); ?>"
          target="_blank" rel="noopener"
        >
          <svg class="w-4 h-4" viewBox="0 0 16 14" fill="currentColor" aria-hidden="true">
            <path d="M3.46829 0.94239C5.30251 2.31114 7.2757 5.08593 7.99995 6.57504C8.72421 5.08593 10.6975 2.31114 12.5317 0.94239C13.8555 -0.0449892 16 -0.809181 16 1.62217C16 2.10758 15.7199 5.70132 15.5555 6.28489C14.9845 8.31295 12.9032 8.83028 11.052 8.51726C14.288 9.06455 15.1111 10.8772 13.3333 12.69C9.95702 16.133 8.48052 11.8262 8.10248 10.7227C7.99345 10.405 8.00902 10.3977 7.89745 10.7227C7.51939 11.8262 6.04301 16.133 2.66676 12.69C0.888939 10.8772 1.71203 9.06455 4.94798 8.51726C3.09676 8.83028 1.01547 8.31295 0.44447 6.28489C0.280063 5.70132 0 2.10758 0 1.62217C0 -0.809181 2.14479 -0.0449892 3.46829 0.94239Z"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>
