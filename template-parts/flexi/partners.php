<?php
$section_id = 'partners-' . wp_rand(1000, 999999);

$heading_text = (string) get_sub_field('heading_text');
$heading_tag  = (string) get_sub_field('heading_tag');

$subheading_text = (string) get_sub_field('subheading_text');
$subheading_tag  = (string) get_sub_field('subheading_tag');

$logos = get_sub_field('logos');

$background_color = (string) get_sub_field('background_color');

$allowed_heading_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (!in_array($heading_tag, $allowed_heading_tags, true)) {
  $heading_tag = 'h3';
}
if (!in_array($subheading_tag, $allowed_heading_tags, true)) {
  $subheading_tag = 'h4';
}

$padding_classes = [];
if (have_rows('padding_settings')) {
  while (have_rows('padding_settings')) {
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

$heading_id = $section_id . '-heading';
?>

<section
  id="<?php echo esc_attr($section_id); ?>"
  class="flex overflow-hidden relative max-md:overflow-visible"
  role="region"
  aria-labelledby="<?php echo esc_attr($heading_id); ?>"
  style="background-color: <?php echo esc_attr($background_color ?: '#009DE6'); ?>;"
>
  <div class="flex flex-col items-center w-full mx-auto max-w-container pt-5 pb-5 max-lg:px-5 <?php echo esc_attr($padding_class_string); ?>">
    <div class="w-full max-w-[64rem] mx-auto">
      <div class="flex flex-col gap-8 self-center pt-6 pr-5 pb-6 pl-5 mx-auto w-full h-auto md:justify-start md:items-center max-xl:px-5">

        <div class="flex flex-col md:justify-start md:items-start gap-4 pr-4 pl-4 rounded-[0.5rem] w-full self-start mx-auto max-xl:px-5">
          <<?php echo esc_html($heading_tag); ?>
            id="<?php echo esc_attr($heading_id); ?>"
            class="break-words text-center text-[1.875rem] font-[700] leading-[2.375rem] text-white font-['Public Sans'] w-full"
          >
            <?php echo esc_html($heading_text ?: 'A growing global movement'); ?>
          </<?php echo esc_html($heading_tag); ?>>

          <?php if ($subheading_text !== '') : ?>
            <<?php echo esc_html($subheading_tag); ?>
              class="break-words text-center text-[1.25rem] font-[400] leading-[1.625rem] text-[#fcfcfd] font-['Public Sans'] w-full"
            >
              <?php echo esc_html($subheading_text); ?>
            </<?php echo esc_html($subheading_tag); ?>>
          <?php endif; ?>
        </div>

        <?php if (!empty($logos) && is_array($logos)) : ?>
          <div
            class="flex flex-col gap-6 self-center pr-4 pl-4 w-full h-auto md:flex-row md:justify-between md:items-center max-xl:px-5"
            role="list"
            aria-label="Partner logos"
          >
            <?php foreach ($logos as $logo_item) : ?>
              <?php
              $logo_image = $logo_item['logo_image'] ?? null;
              $logo_link  = $logo_item['logo_link'] ?? null;

              $logo_url = '';
              $logo_id  = 0;

              if (is_array($logo_image)) {
                $logo_url = (string) ($logo_image['url'] ?? '');
                $logo_id  = (int) ($logo_image['ID'] ?? 0);
              }

              if ($logo_url === '') {
                continue;
              }

              $logo_alt = '';
              $logo_title = '';
              if ($logo_id > 0) {
                $logo_alt = (string) get_post_meta($logo_id, '_wp_attachment_image_alt', true);
                $logo_title = (string) get_the_title($logo_id);
              }

              if ($logo_alt === '') {
                $logo_alt = $logo_title !== '' ? $logo_title : 'Partner logo';
              }
              if ($logo_title === '') {
                $logo_title = $logo_alt;
              }
              ?>

              <div role="listitem" class="flex">
                <?php if (is_array($logo_link) && !empty($logo_link['url'])) : ?>
                  <a
                    href="<?php echo esc_url($logo_link['url']); ?>"
                    target="<?php echo esc_attr($logo_link['target'] ?? '_self'); ?>"
                    class="inline-flex btn"
                    aria-label="<?php echo esc_attr($logo_link['title'] ?: $logo_alt); ?>"
                  >
                    <img
                      src="<?php echo esc_url($logo_url); ?>"
                      alt="<?php echo esc_attr($logo_alt); ?>"
                      title="<?php echo esc_attr($logo_title); ?>"
                      loading="lazy"
                      decoding="async"
                      class="object-cover max-w-full mix-blend-luminosity shrink-0 max-md:h-auto max-md:object-contain"
                    />
                  </a>
                <?php else : ?>
                  <img
                    src="<?php echo esc_url($logo_url); ?>"
                    alt="<?php echo esc_attr($logo_alt); ?>"
                    title="<?php echo esc_attr($logo_title); ?>"
                    loading="lazy"
                    decoding="async"
                    class="object-cover max-w-full mix-blend-luminosity shrink-0 max-md:h-auto max-md:object-contain"
                  />
                <?php endif; ?>
              </div>

            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>