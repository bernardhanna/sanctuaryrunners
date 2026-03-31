<?php
get_header(); ?>

<?php
$not_found_settings = get_field('not_found_settings', 'option');
$title = $not_found_settings['hero_title'] ?? 'Sorry, We Can’t Find That Page.';
$text = $not_found_settings['hero_text'] ?? 'Here are some helpful links to get you back on track:';
$hero_image = $not_found_settings['hero_image'] ?? null;
$links = $not_found_settings['links'] ?? [];
$bg_color = $not_found_settings['background_color'] ?? '#f8f9fa';
$text_color = $not_found_settings['text_color'] ?? '#333';
$button_link_data = !empty($links[0]['link_data']) && is_array($links[0]['link_data']) ? $links[0]['link_data'] : null;
$button_url = !empty($button_link_data['url']) ? $button_link_data['url'] : home_url('/');
$button_title = !empty($button_link_data['title']) ? $button_link_data['title'] : 'Return home';
$button_target = !empty($button_link_data['target']) ? $button_link_data['target'] : '_self';
?>

<main
  class="flex items-center w-full site-main"
  style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>; padding-top: var(--site-nav-offset, 5rem); min-height: calc(100vh - var(--site-nav-offset, 5rem));"
>
  <div class="mx-auto w-full max-w-[1084px] px-5 py-10 lg:py-12">
    <div class="grid grid-cols-1 gap-10 items-center lg:grid-cols-2">
      <div>
        <?php if (!empty($hero_image['url'])) : ?>
          <figure class="overflow-hidden rounded-lg">
            <img
              src="<?php echo esc_url($hero_image['url']); ?>"
              alt="<?php echo esc_attr($hero_image['alt'] ?? '404 page image'); ?>"
              class="object-cover w-full h-full"
            />
          </figure>
        <?php else : ?>
          <div class="flex min-h-[320px] items-center justify-center rounded-lg bg-[#E8EBF4] text-[#00628F]">
            <span class="font-bold">404</span>
          </div>
        <?php endif; ?>
      </div>

      <article class="flex flex-col">
        <h1 class="text-[40px] font-bold leading-[1.15] text-[var(--Blue-SR-500,#00628F)] md:text-[56px]">
          <?php echo esc_html($title); ?>
        </h1>
        <div class="mt-4 text-[18px] leading-6 text-[var(--Gray-700,#00263E)]">
          <?php echo wp_kses_post($text); ?>
        </div>
        <div class="mt-8">
          <a
            href="<?php echo esc_url($button_url); ?>"
            target="<?php echo esc_attr($button_target); ?>"
            class="inline-flex gap-2 justify-center items-center px-6 py-3 text-sm font-bold text-white rounded-[100px] w-full md:w-fit border-[3px] border-[var(--Turquoise-500,#1C959B)] bg-[var(--Blue-SR-400,#008BCC)] hover:bg-[var(--Blue-SR-500,#00628F)] hover:text-white focus:outline-none focus-visible:ring-0 focus-visible:border-[3px] focus-visible:border-[var(--Turquoise-500,#1C959B)] focus-visible:bg-[var(--Blue-SR-500,#00628F)] focus-visible:text-white transition-colors duration-200 btn-primary"
          >
            <?php echo esc_html($button_title); ?>
          </a>
        </div>
      </article>
    </div>
  </div>
</main>
<?php
get_footer();
?>