<?php
/**
 * Related Posts by Category (non-ACF)
 * Design matches property cards: bg image fill + gradient overlay + inner slab.
 *
 * Usage:
 * get_template_part('template-parts/blog/related-posts', null, [
 *   'heading' => 'Related posts',
 *   'limit'   => 6,
 *   'orderby' => 'date',
 *   'order'   => 'DESC',
 * ]);
 */

if (!defined('ABSPATH')) { exit; }

// ========== Args / defaults ==========
$args = wp_parse_args($args ?? [], [
  'heading' => 'Related posts',
  'limit'   => 3,
  'orderby' => 'date',
  'order'   => 'DESC',
]);

$heading = is_string($args['heading']) ? $args['heading'] : 'Related posts';
$limit   = (int) $args['limit'];
if ($limit < 1) { $limit = 6; }
$orderby = in_array($args['orderby'], ['date','modified','rand','title'], true) ? $args['orderby'] : 'date';
$order   = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

// ========== Identify categories from current post ==========
$current_id = get_the_ID();
if (!$current_id) { return; }

$cats = get_the_terms($current_id, 'category');
$cat_ids = [];
if (!empty($cats) && !is_wp_error($cats)) {
  foreach ($cats as $c) { $cat_ids[] = (int) $c->term_id; }
}

// ========== Build query ==========
$q_args = [
  'post_type'           => 'post',
  'posts_per_page'      => $limit,
  'post_status'         => 'publish',
  'orderby'             => $orderby,
  'order'               => $order,
  'post__not_in'        => [$current_id],
  'ignore_sticky_posts' => true,
];

// If categories exist, use them; else we’ll fall back to latest posts
if (!empty($cat_ids)) {
  $q_args['category__in'] = $cat_ids;
}

$q = new WP_Query($q_args);

// Fallback if nothing found and we had categories
if (!$q->have_posts() && !empty($cat_ids)) {
  $q = new WP_Query(array_merge($q_args, [
    'category__in' => [], // drop category constraint
  ]));
}

if (!$q->have_posts()) {
  // Nothing to show; bail silently
  return;
}

// ========== Markup ==========
$section_id = 'related-posts-' . wp_generate_uuid4();
?>
<section id="<?php echo esc_attr($section_id); ?>" class="relative flex overflow-hidden bg-[#F9FAFB]">
  <div class="mx-auto flex w-full max-w-container flex-col items-center px-5 py-10 lg:py-16">
    <div class="flex w-full flex-col gap-8">

      <!-- Heading -->
      <?php if (!empty($heading)): ?>
        <div class="w-full">
          <h2 class="font-sans text-[30px] font-bold not-italic leading-[38px] text-[var(--Blue-SR-500,#00628F)]">
            <?php echo esc_html($heading); ?>
          </h2>
        </div>
      <?php endif; ?>

      <!-- Grid: matches blog listing cards -->
      <div class="grid w-full min-h-[454px] grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" role="region" aria-label="<?php echo esc_attr($heading ?: 'Related posts'); ?>">
        <?php
        while ($q->have_posts()): $q->the_post();
          $pid       = get_the_ID();
          $title     = get_the_title();
          $permalink = get_permalink();
          $date_txt  = get_the_date('j F Y');

          // simple reading time
          $content    = get_post_field('post_content', $pid);
          $word_count = str_word_count(wp_strip_all_tags($content));
          $minutes    = max(1, (int) ceil($word_count / 200));
          $read_time  = $minutes . ' min' . ($minutes > 1 ? 's' : '');

          // categories
          $p_terms = get_the_terms($pid, 'category') ?: [];
          $cats_attr = implode(' ', wp_list_pluck($p_terms, 'slug'));
          $primary_category = !empty($p_terms) ? $p_terms[0] : null;
          $has_media_listing_category = function_exists('matrix_post_has_media_category')
            ? matrix_post_has_media_category($pid)
            : in_array('press-releases', wp_list_pluck($p_terms, 'slug'), true);

          // featured image + excerpt
          $thumb_id = get_post_thumbnail_id($pid);
          $img_alt  = $thumb_id ? (get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: $title) : $title;
          $excerpt = wp_trim_words(get_the_excerpt($pid), 20);
          $press_logo_custom_id = (int) get_field('post_listing_logo_custom', $pid);
          $press_logo_quick_select = trim((string) get_field('post_listing_logo_quick_select', $pid));
          $press_logo_bg_raw = trim((string) get_field('post_listing_logo_bg_color', $pid));
          $press_logo_bg = sanitize_hex_color($press_logo_bg_raw) ?: '#FFFFFF';
          $press_logo_bg_style = $has_media_listing_category ? 'background-color: ' . $press_logo_bg . ';' : '';
          $use_press_logo_override = $has_media_listing_category && ($press_logo_custom_id > 0 || $press_logo_quick_select !== '');
          $external_source_link = get_field('post_external_source_link', $pid);
          $open_external_source = get_field('post_listing_open_external_source', $pid);
          $open_external_source = ($open_external_source === 1 || $open_external_source === '1' || $open_external_source === true);
          $has_external_source_url = is_array($external_source_link) && !empty($external_source_link['url']);
          $auto_external_for_press_release = $has_media_listing_category && $has_external_source_url;
          if ($auto_external_for_press_release) {
            $open_external_source = true;
          }
          $card_target_url = $permalink;
          $card_target_window = '_self';
          $card_rel = '';

          if ($open_external_source && $has_external_source_url) {
            $card_target_url = (string) $external_source_link['url'];
            $card_target_window = '_blank';
            $card_rel = 'noopener noreferrer';
          }
          ?>
          <article
             class="post-item cursor-pointer overflow-hidden rounded-[4px] bg-yellow-50 transition-all duration-200 hover:bg-[#FCF4C5] hover:shadow-[0_0_0_4px_#009DE6]"
             data-categories="<?php echo esc_attr($cats_attr); ?>"
             data-url="<?php echo esc_url($card_target_url); ?>"
             data-url-target="<?php echo esc_attr($card_target_window); ?>"
             aria-label="<?php echo esc_attr('Read full article: ' . $title); ?>"
          >
            <div class="relative flex aspect-[1.565] min-h-[232px] w-full flex-col items-start gap-2.5 overflow-hidden rounded-t-[8px] pb-44 pt-6 text-xs font-bold text-sky-800 whitespace-nowrap max-md:pb-24">
              <?php if ($use_press_logo_override): ?>
                <?php if ($press_logo_custom_id > 0): ?>
                  <?php echo wp_get_attachment_image($press_logo_custom_id, 'large', false, [
                    'alt'     => esc_attr($img_alt),
                    'class'   => 'absolute inset-0 size-full object-contain',
                    'style'   => $press_logo_bg_style,
                    'loading' => 'lazy',
                  ]); ?>
                <?php else: ?>
                  <img
                    src="<?php echo esc_url($press_logo_quick_select); ?>"
                    alt="<?php echo esc_attr($img_alt); ?>"
                    class="absolute inset-0 size-full object-contain"
                    style="<?php echo esc_attr($press_logo_bg_style); ?>"
                    loading="lazy"
                    decoding="async"
                  />
                <?php endif; ?>
              <?php elseif ($thumb_id): ?>
                <?php echo wp_get_attachment_image($thumb_id, 'large', false, [
                  'alt'     => esc_attr($img_alt),
                  'class'   => $has_media_listing_category ? 'absolute inset-0 size-full object-contain' : 'absolute inset-0 size-full object-cover',
                  'style'   => $press_logo_bg_style,
                  'loading' => 'lazy',
                ]); ?>
              <?php endif; ?>

            </div>

            <div class="flex w-full flex-col p-6 text-sm text-sky-950 max-md:px-5">
              <?php if (!empty($p_terms)) : ?>
                <div class="mb-3 flex flex-wrap gap-2 items-center self-start">
                  <?php foreach ($p_terms as $cat) :
                    if (!$cat instanceof WP_Term) {
                      continue;
                    }
                    $chip_classes = 'bg-sky-200 text-sky-900';
                    if ($cat->slug === 'press-releases') {
                      $chip_classes = 'bg-[#D7EDF8] text-sky-900';
                    } elseif ($cat->slug === 'news' || $cat->slug === 'our-news') {
                      $chip_classes = 'bg-[#9EDCF6] text-sky-900';
                    } elseif ($cat->slug === 'event') {
                      $chip_classes = 'bg-[#FBEA5E] text-slate-800';
                    }
                  ?>
                    <span class="inline-flex items-center rounded-[100px] px-3 py-1 text-xs font-bold leading-none <?php echo esc_attr($chip_classes); ?>">
                      <?php echo esc_html($cat->name); ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <div class="flex items-start gap-2 self-start leading-none">
                <time class="text-sky-950" datetime="<?php echo esc_attr(get_the_date('Y-m-d', $pid)); ?>">
                  <?php echo esc_html($date_txt); ?>
                </time>
                <span class="text-sky-950" aria-hidden="true">|</span>
                <span class="text-sky-950">Read time <?php echo esc_html($read_time); ?></span>
              </div>

              <h3 class="mt-2 font-sans text-[18px] font-bold not-italic leading-[24px] text-sky-800">
                <a
                  href="<?php echo esc_url($card_target_url); ?>"
                  target="<?php echo esc_attr($card_target_window); ?>"
                  <?php if (!empty($card_rel)) : ?>rel="<?php echo esc_attr($card_rel); ?>"<?php endif; ?>
                  class="hover:underline focus:underline"
                >
                  <?php echo esc_html($title); ?>
                </a>
              </h3>

              <div class="mt-2 leading-5 text-sky-950">
                <?php echo esc_html($excerpt); ?>
              </div>
            </div>
          </article>
          <?php
        endwhile;
        wp_reset_postdata();
        ?>
      </div>

      <script>
      document.addEventListener('DOMContentLoaded', function () {
        var cards = document.querySelectorAll('#<?php echo esc_js($section_id); ?> .post-item[data-url]');
        if (!cards.length) return;

        cards.forEach(function (card) {
          card.addEventListener('click', function (e) {
            if (e.target.closest('a, button, input, textarea, select, label, [role="button"]')) return;
            var url = card.getAttribute('data-url');
            var target = card.getAttribute('data-url-target') || '_self';
            if (!url) return;
            if (target === '_blank') {
              window.open(url, '_blank', 'noopener');
              return;
            }
            window.location.href = url;
          });

          card.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            if (e.target.closest('a, button, input, textarea, select, label, [role="button"]')) return;
            e.preventDefault();
            var url = card.getAttribute('data-url');
            var target = card.getAttribute('data-url-target') || '_self';
            if (!url) return;
            if (target === '_blank') {
              window.open(url, '_blank', 'noopener');
              return;
            }
            window.location.href = url;
          });
        });
      });
      </script>
    </div>
  </div>
</section>
