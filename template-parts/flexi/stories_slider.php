<?php
/**
 * Flexi Block: Stories Slider
 */

$section_id       = get_sub_field('section_id') ?: 'stories-slider-' . uniqid();
$section_class    = get_sub_field('section_class');
$heading          = get_sub_field('heading') ?: 'Stories';
$heading_tag      = get_sub_field('heading_tag') ?: 'h2';
$read_all_link    = get_sub_field('read_all_link');
$background_image       = get_sub_field('background_image');
$slider_background_image = get_sub_field('slider_background_image');
$posts_per_page         = get_sub_field('posts_per_page') ?: 8;
$show_excerpt     = get_sub_field('show_excerpt');

$allowed_tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span'];
if (!in_array($heading_tag, $allowed_tags, true)) {
    $heading_tag = 'h2';
}

$background_style = '';
if (!empty($background_image) && !empty($background_image['url'])) {
    $background_style = "background-image: url('" . esc_url($background_image['url']) . "'); background-size: cover; background-position: center; background-repeat: no-repeat;";
}

$stories_query = new WP_Query([
    'post_type'           => 'post',
    'posts_per_page'      => (int) $posts_per_page,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
    'category_name'       => 'stories',
]);

$has_posts = $stories_query->have_posts();
$slide_images = [];
$default_slider_bg = '';
if (!empty($slider_background_image) && !empty($slider_background_image['url'])) {
    $default_slider_bg = $slider_background_image['url'];
}
if ($has_posts) {
    foreach ($stories_query->posts as $p) {
        $slide_images[] = get_the_post_thumbnail_url($p->ID, 'large') ?: '';
    }
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
      style="<?php echo esc_attr($background_style); ?>"
    class="px-4 pt-6 pb-12 sm:px-6 lg:px-10 xl:px-16 <?php echo esc_attr($section_class); ?>"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div
        class="mx-auto max-w-[1440px] px-4 py-8 sm:px-6 lg:px-24 lg:py-[72px]"
      
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <<?php echo esc_html($heading_tag); ?>
                id="<?php echo esc_attr($section_id); ?>-heading"
                class="font-display text-[2.25rem] font-bold tracking-[-0.04em] text-brand-blue sm:text-[2.75rem]"
            >
                <?php echo esc_html($heading); ?>
            </<?php echo esc_html($heading_tag); ?>>

            <?php if (!empty($read_all_link['url']) && !empty($read_all_link['title'])) : ?>
                <a
                    href="<?php echo esc_url($read_all_link['url']); ?>"
                    target="<?php echo esc_attr($read_all_link['target'] ?: '_self'); ?>"
                    class="hidden md:flex h-[42px] justify-center items-center gap-2 py-4 pl-6 pr-4 font-['Public_Sans'] text-[14px] font-bold leading-5 text-[var(--Blue-SR-500,#00628F)] rounded-full bg-white hover:bg-[var(--Turquoise-50,#CBF3F6)] active:bg-[var(--Turquoise-100,#75E0E6)] focus:outline-none focus-visible:ring-0 focus-visible:border-[3px] focus-visible:border-[var(--Turquoise-500,#1C959B)] focus-visible:bg-white transition-colors duration-200"
                >
                    <?php echo esc_html($read_all_link['title']); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="M1 5.66667H10.3333M10.3333 5.66667L5.66667 1M10.3333 5.66667L5.66667 10.3333" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>

        <div class="stories-slider-wrap relative mt-4 rounded-[12px] shadow-story" data-slide-images="<?php echo esc_attr(wp_json_encode($slide_images)); ?>" data-default-bg="<?php echo esc_attr($default_slider_bg); ?>">
            <!-- Background: default image until article hovered (crossfade) -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-center bg-no-repeat bg-cover transition-opacity duration-500 stories-bg stories-bg-a" style="background-color: #081226;<?php echo $default_slider_bg ? " background-image: url('" . esc_url($default_slider_bg) . "');" : ''; ?>"></div>
                <div class="absolute inset-0 bg-center bg-no-repeat bg-cover opacity-0 transition-opacity duration-500 stories-bg stories-bg-b" style="background-color: #081226;"></div>
            </div>

            <div class="relative z-10 px-4 pt-10 pb-24 sm:px-8 lg:px-8 min-h-[740px] flex items-end">
                <?php if ($has_posts) : ?>
                    <button
                        type="button"
                        class="stories-prev absolute left-3 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] transition hover:opacity-90 xl:-left-6 md:flex"
                        aria-label="Previous stories"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M15 8H1M1 8L8 15M1 8L8 1" stroke="#1D2939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="stories-next absolute right-3 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] transition hover:opacity-90 xl:-right-6 md:flex"
                        aria-label="Next stories"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="#1D2939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                <?php endif; ?>

                <div class="mx-auto w-full max-w-[1328px]">
                    <?php if ($has_posts) : ?>
                        <div class="js-stories-slider">
                            <?php $slide_index = 0; while ($stories_query->have_posts()) : $stories_query->the_post(); ?>
                                <?php
                                $post_id    = get_the_ID();
                                $image_url  = get_the_post_thumbnail_url($post_id, 'medium_large');
                                $image_id   = get_post_thumbnail_id($post_id);
                                $image_alt  = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
                                $image_alt  = $image_alt ?: get_the_title($post_id);
                                ?>
                                <div class="px-2 stories-slide" data-slide-index="<?php echo esc_attr((string) $slide_index); ?>">
                                    <article class="flex h-full gap-4 rounded-[12px] bg-white p-5 shadow-md">
                                        <?php if ($image_url) : ?>
                                            <img
                                                src="<?php echo esc_url($image_url); ?>"
                                                alt="<?php echo esc_attr($image_alt); ?>"
                                                loading="lazy"
                                                class="h-[100px] w-[100px] shrink-0 rounded-[8px] object-cover"
                                            />
                                        <?php endif; ?>

                                        <div class="flex flex-col flex-1 gap-4 justify-between items-start min-w-0">
                                            <div>
                                                <h3 class="text-base font-semibold leading-6 text-brand-ink">
                                                    <?php the_title(); ?>
                                                </h3>

                                                <?php if ($show_excerpt) : ?>
                                                    <p class="mt-2 text-sm leading-5 text-brand-body">
                                                        <?php echo esc_html(wp_trim_words(get_the_excerpt(), 22, '...')); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>

                                            <a
                                                href="<?php the_permalink(); ?>"
                                                class="inline-flex justify-center items-center px-6 py-3 mt-2 w-full text-sm font-bold text-white rounded-full md:w-fit btn-primary"
                                                aria-label="<?php echo esc_attr(get_the_title()); ?>"
                                            >
                                                Read Story
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            <?php $slide_index++; endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    <?php else : ?>
                        <p class="text-white">No story posts found in the stories category.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <?php if (!empty($read_all_link['url']) && !empty($read_all_link['title'])) : ?>
            <div class="mt-6 md:hidden">
                <a
                    href="<?php echo esc_url($read_all_link['url']); ?>"
                    target="<?php echo esc_attr($read_all_link['target'] ?: '_self'); ?>"
                    class="inline-flex items-center gap-2 text-[1rem] font-semibold text-brand-blue transition hover:opacity-80"
                >
                    <?php echo esc_html($read_all_link['title']); ?>
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($has_posts) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var section = document.getElementById('<?php echo esc_js($section_id); ?>');
    if (!section || typeof jQuery === 'undefined' || typeof jQuery.fn.slick === 'undefined') {
        return;
    }

    var $section = jQuery(section);
    var $slider = $section.find('.js-stories-slider');
    var $wrap = $section.find('.stories-slider-wrap');

    if (!$slider.length || $slider.hasClass('slick-initialized')) {
        return;
    }

    var slideImages = [];
    var defaultBg = '';
    try {
        var raw = $wrap.attr('data-slide-images');
        if (raw) slideImages = JSON.parse(raw);
        defaultBg = $wrap.attr('data-default-bg') || '';
    } catch (e) {}

    var bgActiveA = true;
    var bgInitialized = false;

    function setStoriesBg(url) {
        var bgStyle = url ? 'url(' + url + ')' : 'none';
        var $a = $wrap.find('.stories-bg-a');
        var $b = $wrap.find('.stories-bg-b');

        if (!bgInitialized) {
            $a.css('background-image', bgStyle);
            $a.removeClass('opacity-0');
            $b.addClass('opacity-0');
            bgInitialized = true;
            return;
        }

        if (bgActiveA) {
            $b.css('background-image', bgStyle);
            $a.addClass('opacity-0');
            $b.removeClass('opacity-0');
            bgActiveA = false;
        } else {
            $a.css('background-image', bgStyle);
            $a.removeClass('opacity-0');
            $b.addClass('opacity-0');
            bgActiveA = true;
        }
    }

    $slider.slick({
        slidesToShow: 3,
        slidesToScroll: 3,
        infinite: true,
        dots: false,
        arrows: true,
        prevArrow: $section.find('.stories-prev'),
        nextArrow: $section.find('.stories-next'),
        adaptiveHeight: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
        ]
    });

    $slider.on('init', function () {
        setStoriesBg(defaultBg);
    });

    $section.on('mouseenter', '.stories-slide', function () {
        var idx = parseInt(jQuery(this).attr('data-slide-index'), 10);
        if (!isNaN(idx) && slideImages[idx]) {
            setStoriesBg(slideImages[idx]);
        }
    });

    $wrap.on('mouseleave', function () {
        setStoriesBg(defaultBg);
    });
});
</script>
<?php endif; ?>