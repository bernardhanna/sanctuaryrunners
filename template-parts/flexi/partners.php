<?php
$section_id = 'partners-' . wp_rand(1000, 999999);

$heading_text = (string) get_sub_field('heading_text');
$heading_tag  = (string) get_sub_field('heading_tag');

$subheading_text = (string) get_sub_field('subheading_text');
$subheading_tag  = (string) get_sub_field('subheading_tag');

$logos = get_sub_field('logos');
$enable_logo_slider = (bool) get_sub_field('enable_logo_slider');
$show_slider_arrows = (bool) get_sub_field('show_slider_arrows');
$show_mobile_dots = (bool) get_sub_field('show_mobile_dots');
$slider_autoplay = (bool) get_sub_field('slider_autoplay');
$slider_autoplay_speed = (int) (get_sub_field('slider_autoplay_speed') ?: 3000);
if ($slider_autoplay_speed < 1000) {
  $slider_autoplay_speed = 1000;
}
$slider_slides_desktop = (int) (get_sub_field('slider_slides_desktop') ?: 5);
if ($slider_slides_desktop < 1) {
  $slider_slides_desktop = 1;
}

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
      <div class="flex flex-col gap-8 self-center pt-0 pr-5 pb-6 pl-5 mx-auto w-full h-auto md:justify-start md:items-center max-xl:px-5">

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
          <?php
          $logo_items = [];
          foreach ($logos as $logo_item) {
            $logo_image = $logo_item['logo_image'] ?? null;
            $logo_link  = $logo_item['logo_link'] ?? null;

            $logo_url = '';
            $logo_id  = 0;

            if (is_array($logo_image)) {
              $logo_url = (string) ($logo_image['url'] ?? '');
              $logo_id  = (int) ($logo_image['ID'] ?? 0);
            }
            if ($logo_url === '') continue;

            $logo_alt = '';
            $logo_title = '';
            if ($logo_id > 0) {
              $logo_alt = (string) get_post_meta($logo_id, '_wp_attachment_image_alt', true);
              $logo_title = (string) get_the_title($logo_id);
            }
            if ($logo_alt === '') $logo_alt = $logo_title !== '' ? $logo_title : 'Partner logo';
            if ($logo_title === '') $logo_title = $logo_alt;

            $logo_items[] = [
              'url' => $logo_url,
              'alt' => $logo_alt,
              'title' => $logo_title,
              'link' => $logo_link,
            ];
          }
          ?>

          <?php if (!$enable_logo_slider) : ?>
            <div class="flex flex-col gap-6 self-center pr-4 pl-4 w-full h-auto md:flex-row md:justify-between md:items-center max-xl:px-5" role="list" aria-label="Partner logos">
              <?php foreach ($logo_items as $logo_data) : ?>
                <div role="listitem" class="flex justify-center md:justify-start">
                  <?php if (is_array($logo_data['link']) && !empty($logo_data['link']['url'])) : ?>
                    <a href="<?php echo esc_url($logo_data['link']['url']); ?>" target="<?php echo esc_attr($logo_data['link']['target'] ?? '_self'); ?>" class="inline-flex btn" aria-label="<?php echo esc_attr($logo_data['link']['title'] ?: $logo_data['alt']); ?>">
                      <img src="<?php echo esc_url($logo_data['url']); ?>" alt="<?php echo esc_attr($logo_data['alt']); ?>" title="<?php echo esc_attr($logo_data['title']); ?>" loading="lazy" decoding="async" class="object-cover max-w-full mix-blend-luminosity shrink-0 max-md:h-auto max-md:object-contain" />
                    </a>
                  <?php else : ?>
                    <img src="<?php echo esc_url($logo_data['url']); ?>" alt="<?php echo esc_attr($logo_data['alt']); ?>" title="<?php echo esc_attr($logo_data['title']); ?>" loading="lazy" decoding="async" class="object-cover max-w-full mix-blend-luminosity shrink-0 max-md:h-auto max-md:object-contain" />
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else : ?>
            <div class="partners-slider-wrap relative w-full px-4 max-xl:px-5">
              <?php if ($show_slider_arrows) : ?>
                <button type="button" class="group partners-prev absolute left-2 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] transition hover:opacity-100 hover:bg-[#009DE6] xl:-left-4 md:flex" aria-label="Previous partners">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M15 8H1M1 8L8 15M1 8L8 1" class="transition-colors duration-200 group-hover:stroke-white" stroke="#1D2939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
                <button type="button" class="group partners-next absolute right-2 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] transition hover:opacity-100 hover:bg-[#009DE6] xl:-right-4 md:flex" aria-label="Next partners">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" class="transition-colors duration-200 group-hover:stroke-white" stroke="#1D2939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              <?php endif; ?>

              <div class="hidden md:block">
                <div
                  class="js-partners-slider"
                  data-autoplay="<?php echo $slider_autoplay ? '1' : '0'; ?>"
                  data-speed="<?php echo esc_attr((string) $slider_autoplay_speed); ?>"
                  data-slides="<?php echo esc_attr((string) $slider_slides_desktop); ?>"
                  role="list"
                  aria-label="Partner logos slider"
                >
                  <?php foreach ($logo_items as $logo_data) : ?>
                    <div class="px-2 partners-slide">
                      <div role="listitem" class="flex h-[100px] items-center justify-center">
                        <?php if (is_array($logo_data['link']) && !empty($logo_data['link']['url'])) : ?>
                          <a href="<?php echo esc_url($logo_data['link']['url']); ?>" target="<?php echo esc_attr($logo_data['link']['target'] ?? '_self'); ?>" class="inline-flex btn" aria-label="<?php echo esc_attr($logo_data['link']['title'] ?: $logo_data['alt']); ?>">
                            <img src="<?php echo esc_url($logo_data['url']); ?>" alt="<?php echo esc_attr($logo_data['alt']); ?>" title="<?php echo esc_attr($logo_data['title']); ?>" loading="lazy" decoding="async" class="max-h-[82px] w-auto max-w-full object-contain mix-blend-luminosity" />
                          </a>
                        <?php else : ?>
                          <img src="<?php echo esc_url($logo_data['url']); ?>" alt="<?php echo esc_attr($logo_data['alt']); ?>" title="<?php echo esc_attr($logo_data['title']); ?>" loading="lazy" decoding="async" class="max-h-[82px] w-auto max-w-full object-contain mix-blend-luminosity" />
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="md:hidden">
                <div class="partners-scroll-track" role="list" aria-label="Partner logos slider">
                  <?php foreach ($logo_items as $logo_data) : ?>
                    <div class="partners-scroll-slide" role="listitem">
                      <div class="flex h-[96px] items-center justify-center rounded-[10px] bg-white/5 px-3">
                        <?php if (is_array($logo_data['link']) && !empty($logo_data['link']['url'])) : ?>
                          <a href="<?php echo esc_url($logo_data['link']['url']); ?>" target="<?php echo esc_attr($logo_data['link']['target'] ?? '_self'); ?>" class="inline-flex btn" aria-label="<?php echo esc_attr($logo_data['link']['title'] ?: $logo_data['alt']); ?>">
                            <img src="<?php echo esc_url($logo_data['url']); ?>" alt="<?php echo esc_attr($logo_data['alt']); ?>" title="<?php echo esc_attr($logo_data['title']); ?>" loading="lazy" decoding="async" class="max-h-[72px] w-auto max-w-full object-contain mix-blend-luminosity" />
                          </a>
                        <?php else : ?>
                          <img src="<?php echo esc_url($logo_data['url']); ?>" alt="<?php echo esc_attr($logo_data['alt']); ?>" title="<?php echo esc_attr($logo_data['title']); ?>" loading="lazy" decoding="async" class="max-h-[72px] w-auto max-w-full object-contain mix-blend-luminosity" />
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php if ($show_mobile_dots) : ?>
                  <div class="partners-mobile-dots mt-4"></div>
                <?php endif; ?>
              </div>
            </div>

            <style>
              #<?php echo esc_attr($section_id); ?> .partners-scroll-track {
                display: flex;
                gap: 12px;
                overflow-x: auto;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                padding-inline: 6px;
              }
              #<?php echo esc_attr($section_id); ?> .partners-scroll-track::-webkit-scrollbar { display: none; }
              #<?php echo esc_attr($section_id); ?> .partners-scroll-slide {
                flex: 0 0 min(72%, 260px);
                scroll-snap-align: start;
              }
              #<?php echo esc_attr($section_id); ?> .partners-mobile-dots {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
              }
              #<?php echo esc_attr($section_id); ?> .partners-mobile-dots button {
                width: 12px;
                height: 12px;
                padding: 0;
                border: 2px solid #ffffff;
                border-radius: 9999px;
                background: transparent;
                cursor: pointer;
                transition: background 0.2s, opacity 0.2s;
                opacity: 0.9;
              }
              #<?php echo esc_attr($section_id); ?> .partners-mobile-dots button.is-active {
                background: #ffffff;
                opacity: 1;
              }
            </style>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
              var section = document.getElementById('<?php echo esc_js($section_id); ?>');
              if (!section) return;
              if (typeof jQuery === 'undefined' || typeof jQuery.fn.slick === 'undefined') return;

              var $section = jQuery(section);
              var $slider = $section.find('.js-partners-slider');
              if (!$slider.length || $slider.hasClass('slick-initialized')) return;

              var autoplay = $slider.attr('data-autoplay') === '1';
              var speed = parseInt($slider.attr('data-speed') || '3000', 10);
              var slidesDesktop = parseInt($slider.attr('data-slides') || '5', 10);

              $slider.slick({
                slidesToShow: slidesDesktop,
                slidesToScroll: 1,
                autoplay: autoplay,
                autoplaySpeed: isNaN(speed) ? 3000 : speed,
                infinite: true,
                arrows: <?php echo $show_slider_arrows ? 'true' : 'false'; ?>,
                prevArrow: $section.find('.partners-prev'),
                nextArrow: $section.find('.partners-next'),
                dots: false,
                adaptiveHeight: false,
                responsive: [
                  { breakpoint: 1280, settings: { slidesToShow: Math.max(2, slidesDesktop - 1) } },
                  { breakpoint: 1024, settings: { slidesToShow: Math.max(2, slidesDesktop - 2) } },
                  { breakpoint: 768, settings: 'unslick' }
                ]
              });

              <?php if ($show_mobile_dots) : ?>
              var track = section.querySelector('.partners-scroll-track');
              var dotsEl = section.querySelector('.partners-mobile-dots');
              if (track && dotsEl) {
                var slides = Array.from(track.querySelectorAll('.partners-scroll-slide'));
                var dots = [];

                function setActiveDot(idx) {
                  dots.forEach(function (dot, i) { dot.classList.toggle('is-active', i === idx); });
                }

                slides.forEach(function (_, idx) {
                  var dot = document.createElement('button');
                  dot.type = 'button';
                  dot.setAttribute('aria-label', 'Go to partner logo ' + (idx + 1));
                  dot.addEventListener('click', function () {
                    var target = slides[idx];
                    if (!target) return;
                    var trackPad = parseFloat(window.getComputedStyle(track).paddingLeft) || 0;
                    var targetScroll = Math.max(0, target.offsetLeft - trackPad);
                    track.scrollTo({ left: targetScroll, behavior: 'smooth' });
                  });
                  dotsEl.appendChild(dot);
                  dots.push(dot);
                });
                setActiveDot(0);

                var ticking = false;
                track.addEventListener('scroll', function () {
                  if (ticking) return;
                  ticking = true;
                  requestAnimationFrame(function () {
                    ticking = false;
                    var trackCx = track.scrollLeft + track.offsetWidth / 2;
                    var best = 0;
                    var bestDist = Infinity;
                    slides.forEach(function (sl, i) {
                      var dist = Math.abs((sl.offsetLeft + sl.offsetWidth / 2) - trackCx);
                      if (dist < bestDist) { bestDist = dist; best = i; }
                    });
                    setActiveDot(best);
                  });
                }, { passive: true });
              }
              <?php endif; ?>
            });
            </script>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>