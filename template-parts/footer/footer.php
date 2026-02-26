<?php
/**
 * Footer Template (Sanctuary Runner style) — NO Back to Top
 *
 * @package Matrix_Starter
 */

/** Helpers */
function matrix_link_url($link_field) {
    return (is_array($link_field) && !empty($link_field['url'])) ? $link_field['url'] : '';
}
function matrix_link_target($link_field) {
    return (is_array($link_field) && !empty($link_field['target'])) ? $link_field['target'] : '_self';
}
function matrix_img_meta($image_id, $fallback_alt = '') {
    $alt = (string) get_post_meta($image_id, '_wp_attachment_image_alt', true);
    $title = (string) get_the_title($image_id);

    if ($alt === '') {
        $alt = $fallback_alt !== '' ? $fallback_alt : ($title !== '' ? $title : get_bloginfo('name'));
    }
    if ($title === '') {
        $title = $alt;
    }

    return [$alt, $title];
}

/** Options */
$footer_logo = (int) get_field('footer_logo', 'option');

$footer_social_links = get_field('footer_social_links', 'option');
$footer_reg_text = (string) get_field('footer_reg_text', 'option');

$col1_heading = (string) get_field('footer_col1_heading', 'option');
$col2_heading = (string) get_field('footer_col2_heading', 'option');
$col3_heading = (string) get_field('footer_col3_heading', 'option');

$copyright_left_tpl = (string) get_field('footer_copyright_left', 'option');
$copyright_left = str_replace('{year}', date('Y'), $copyright_left_tpl ?: 'Sanctuary Runners © {year}');

$credit_prefix = (string) get_field('footer_credit_prefix', 'option');
$credit_link = get_field('footer_credit_link', 'option');

$top_bar_color   = (string) get_field('footer_top_bar_color', 'option');
$main_bg_color   = (string) get_field('footer_main_bg', 'option');
$bottom_bg_color = (string) get_field('footer_bottom_bg', 'option');

/** Padding classes (options repeater) */
$padding_classes = [];
if (have_rows('padding_settings', 'option')) {
    while (have_rows('padding_settings', 'option')) {
        the_row();
        $screen_size    = (string) get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if ($screen_size !== '' && $padding_top !== null && $padding_top !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }
        if ($screen_size !== '' && $padding_bottom !== null && $padding_bottom !== '') {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}
$padding_class_string = implode(' ', $padding_classes);

/** Menus */
$menu_about       = 'footer_one';
$menu_latest      = 'footer_two';
$menu_getinvolved = 'footer_three';
$menu_legal       = 'copyright';

/** SVGs for social icons */
function matrix_social_svg($icon) {
    if ($icon === 'bluesky') {
        return '<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M3.46829 0.94239C5.30251 2.31114 7.2757 5.08593 7.99995 6.57504C8.72421 5.08593 10.6975 2.31114 12.5317 0.94239C13.8555 -0.0449892 16 -0.809181 16 1.62217C16 2.10758 15.7199 5.70132 15.5555 6.28489C14.9845 8.31295 12.9032 8.83028 11.052 8.51726C14.288 9.06455 15.1111 10.8772 13.3333 12.69C9.95702 16.133 8.48052 11.8262 8.10248 10.7227C7.99345 10.405 8.00902 10.3977 7.89745 10.7227C7.51939 11.8262 6.04301 16.133 2.66676 12.69C0.888939 10.8772 1.71203 9.06455 4.94798 8.51726C3.09676 8.83028 1.01547 8.31295 0.44447 6.28489C0.280063 5.70132 0 2.10758 0 1.62217C0 -0.809181 2.14479 -0.0449892 3.46829 0.94239Z" fill="#00628F"/></svg>';
    }
    if ($icon === 'linkedin') {
        return '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M11.99 9.60071C11.9898 10.025 11.8215 10.4319 11.5223 10.7318C11.223 11.0317 10.8172 11.2 10.3942 11.1998C9.9712 11.1996 9.5656 11.0308 9.26664 10.7307C8.96767 10.4305 8.79984 10.0234 8.80005 9.59911C8.80026 9.17479 8.9685 8.76793 9.26777 8.46804C9.56703 8.16815 9.9728 7.99979 10.3958 8C10.8188 8.00021 11.2244 8.16898 11.5234 8.46917C11.8223 8.76936 11.9902 9.17639 11.99 9.60071ZM12.0378 12.3846H8.8479V22.4H12.0378V12.3846ZM17.0779 12.3846H13.9039V22.4H17.046V17.1443C17.046 14.2165 20.85 13.9445 20.85 17.1443V22.4H24V16.0564C24 11.1206 18.3698 11.3046 17.046 13.7285L17.0779 12.3846Z" fill="#00628F"/></svg>';
    }
    if ($icon === 'instagram') {
        return '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M23.1567 13.0314C23.1448 12.4341 23.0316 11.8432 22.8221 11.2837C22.6413 10.8033 22.3579 10.3681 21.9916 10.0084C21.632 9.64217 21.1967 9.35876 20.7163 9.17796C20.1568 8.96842 19.5659 8.85526 18.9686 8.8433C18.2006 8.80829 17.9555 8.79999 16 8.79999C14.0446 8.79999 13.7995 8.80829 13.0315 8.8433C12.4342 8.85526 11.8433 8.96842 11.2838 9.17796C10.8034 9.35876 10.3681 9.64217 10.0085 10.0084C9.64223 10.3681 9.35882 10.8033 9.17802 11.2837C8.96848 11.8432 8.85532 12.4341 8.84336 13.0314C8.80835 13.7994 8.80005 14.0446 8.80005 16C8.80005 17.9554 8.80835 18.2006 8.84336 18.9685C8.85532 19.5658 8.96848 20.1568 9.17802 20.7162C9.35882 21.1967 9.64223 21.6319 10.0085 21.9915C10.3681 22.3578 10.8034 22.6412 11.2838 22.822C11.8433 23.0316 12.4342 23.1447 13.0315 23.1567C13.7995 23.1917 14.0446 23.2 16 23.2C17.9555 23.2 18.2006 23.1917 18.9686 23.1567C19.5659 23.1447 20.1568 23.0316 20.7163 22.822C21.1946 22.637 21.629 22.3542 21.9916 21.9915C22.3542 21.6289 22.6371 21.1945 22.8221 20.7163C23.0316 20.1568 23.1448 19.5658 23.1567 18.9685C23.1917 18.2006 23.2 17.9554 23.2 16C23.2 14.0446 23.1917 13.7994 23.1567 13.0314ZM16 12.3027C15.2688 12.3027 14.554 12.5195 13.9459 12.9258C13.3379 13.3321 12.864 13.9095 12.5842 14.5851C12.3043 15.2607 12.2311 16.0041 12.3738 16.7213C12.5164 17.4385 12.8686 18.0973 13.3857 18.6144C13.9027 19.1315 14.5615 19.4836 15.2787 19.6263C15.9959 19.7689 16.7394 19.6957 17.4149 19.4159C18.0905 19.136 18.668 18.6621 19.0743 18.0541C19.4805 17.4461 19.6974 16.7312 19.6974 16Z" fill="#00628F"/></svg>';
    }
    // facebook default
    return '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M17.6055 17.3246H19.8992L20.8167 13.7924H17.6055V12.0262C17.6055 11.1167 17.6055 10.2601 19.4405 10.2601H20.8167V7.293C20.5176 7.25503 19.3882 7.16937 18.1954 7.16937C15.7045 7.16937 13.9356 8.63261 13.9356 11.3198V13.7924H11.1832V17.3246H13.9356V24.8307H17.6055V17.3246Z" fill="#00628F"/></svg>';
}
?>

<footer role="contentinfo" aria-label="Site footer">
  <div class="h-2" style="background-color: <?php echo esc_attr($top_bar_color ?: '#E6F4FB'); ?>;"></div>

  <div style="background-color: <?php echo esc_attr($main_bg_color ?: '#00263E'); ?>;">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-8 lg:px-14 py-14 <?php echo esc_attr($padding_class_string); ?>">
      <div class="flex flex-col gap-10 lg:flex-row lg:gap-16 xl:gap-32">

        <div class="flex flex-col gap-4 shrink-0">
          <div class="w-40">
            <?php if ($footer_logo) : ?>
              <?php
              [$alt, $title] = matrix_img_meta($footer_logo, 'Sanctuary Runners');
              echo wp_get_attachment_image($footer_logo, 'full', false, [
                  'class' => 'w-full h-auto',
                  'alt' => esc_attr($alt),
                  'title' => esc_attr($title),
              ]);
              ?>
            <?php else : ?>
              <span class="text-base font-bold text-white font-primary">
                <?php echo esc_html(get_bloginfo('name')); ?>
              </span>
            <?php endif; ?>
          </div>

          <?php if (!empty($footer_social_links) && is_array($footer_social_links)) : ?>
            <nav class="flex gap-4 items-center" aria-label="Social media links">
              <?php foreach ($footer_social_links as $social) :
                $label = (string) ($social['label'] ?? 'Social link');
                $icon  = (string) ($social['icon'] ?? 'facebook');
                $link  = $social['link'] ?? null;

                $url = matrix_link_url($link);
                if ($url === '') {
                    continue;
                }
                $target = matrix_link_target($link);
              ?>
                <a
                  href="<?php echo esc_url($url); ?>"
                  target="<?php echo esc_attr($target); ?>"
                  rel="noopener"
                  aria-label="<?php echo esc_attr($label); ?>"
                  class="flex justify-center items-center w-8 h-8 bg-white rounded-full transition-opacity hover:opacity-80 shrink-0 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent btn"
                >
                  <?php echo matrix_social_svg($icon); ?>
                </a>
              <?php endforeach; ?>
            </nav>
          <?php endif; ?>

          <?php if ($footer_reg_text !== '') : ?>
            <p class="text-white font-primary text-[10px] leading-4 max-w-[180px]">
              <?php echo esc_html($footer_reg_text); ?>
            </p>
          <?php endif; ?>
        </div>

        <nav class="flex flex-col gap-4" aria-labelledby="footer-col-1">
          <h3 id="footer-col-1" class="text-2xl font-light leading-8 text-[#54A5DE] font-primary">
            <?php echo esc_html($col1_heading ?: 'About us'); ?>
          </h3>
          <?php
          wp_nav_menu([
              'theme_location' => $menu_about,
              'container'      => false,
              'menu_class'     => 'flex flex-col gap-4',
              'fallback_cb'    => '__return_empty_string',
              'link_before'    => '<span class="text-sm font-bold leading-5 text-white transition-colors font-primary hover:text-[#54A5DE]">',
              'link_after'     => '</span>',
          ]);
          ?>
        </nav>

        <nav class="flex flex-col gap-4" aria-labelledby="footer-col-2">
          <h3 id="footer-col-2" class="text-2xl font-light leading-8 text-[#54A5DE] font-primary">
            <?php echo esc_html($col2_heading ?: 'Latest'); ?>
          </h3>
          <?php
          wp_nav_menu([
              'theme_location' => $menu_latest,
              'container'      => false,
              'menu_class'     => 'flex flex-col gap-4',
              'fallback_cb'    => '__return_empty_string',
              'link_before'    => '<span class="text-sm font-bold leading-5 text-white transition-colors font-primary hover:text-[#54A5DE]">',
              'link_after'     => '</span>',
          ]);
          ?>
        </nav>

        <nav class="flex flex-col gap-4" aria-labelledby="footer-col-3">
          <h3 id="footer-col-3" class="text-2xl font-light leading-8 text-[#54A5DE] font-primary">
            <?php echo esc_html($col3_heading ?: 'Get involved'); ?>
          </h3>
          <?php
          wp_nav_menu([
              'theme_location' => $menu_getinvolved,
              'container'      => false,
              'menu_class'     => 'flex flex-col gap-4',
              'fallback_cb'    => '__return_empty_string',
              'link_before'    => '<span class="text-sm font-bold leading-5 text-white transition-colors font-primary hover:text-[#54A5DE]">',
              'link_after'     => '</span>',
          ]);
          ?>
        </nav>

      </div>
    </div>
  </div>

  <div style="background-color: <?php echo esc_attr($bottom_bg_color ?: '#F0F9FF'); ?>;">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-8 lg:px-14 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[#475467] font-primary text-xs leading-[18px]">
        <span><?php echo esc_html($copyright_left); ?></span>
        <span class="text-[#475467]" aria-hidden="true">|</span>

        <?php
        wp_nav_menu([
            'theme_location' => $menu_legal,
            'container'      => false,
            'menu_class'     => 'flex flex-wrap items-center gap-x-2 gap-y-1',
            'fallback_cb'    => '__return_empty_string',
            'link_before'    => '<span class="transition-colors hover:text-sr-blue-500">',
            'link_after'     => '</span><span class="text-[#475467]" aria-hidden="true">|</span>',
        ]);
        ?>
      </div>

      <div class="flex items-center gap-1 text-[#475467] font-primary text-xs leading-[18px] whitespace-nowrap">
        <span><?php echo esc_html($credit_prefix ?: 'All Rights Reserved - Designed & Developed by'); ?></span>
        <?php if (is_array($credit_link) && !empty($credit_link['url'])) : ?>
          <a
            href="<?php echo esc_url($credit_link['url']); ?>"
            target="<?php echo esc_attr($credit_link['target'] ?? '_self'); ?>"
            class="font-medium transition-colors hover:text-sr-blue-500 btn"
          >
            <?php echo esc_html($credit_link['title'] ?: 'Matrix Internet'); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>