<?php
$section_id = 'content-section-two-' . uniqid();
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$description = get_sub_field('description');
$body_content = get_sub_field('body_content');
$content_button = get_sub_field('content_button');
$image = get_sub_field('image');
$image_alt = get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'Content image';
$reverse_layout = (bool) get_sub_field('reverse_layout');
$background_color = get_sub_field('background_color');

$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
    }
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative flex overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pb-5 mx-auto w-full md:pt-12 max-w-container max-lg:px-5">
        <div class="flex overflow-hidden flex-col justify-center self-stretch px-10 max-md:px-0">
            <div class="grid grid-cols-1 gap-10 items-center mt-6 w-full md:grid-cols-2 max-md:max-w-full">

                <article class="w-full self-center max-md:max-w-full <?php echo $reverse_layout ? 'md:order-2' : 'md:order-1'; ?>">
                    <?php if (!empty($heading)): ?>
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="text-2xl font-bold leading-none text-sky-800 max-md:max-w-full"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($description)): ?>
                        <div class="mt-6 text-xl leading-7 text-slate-600 max-md:max-w-full wp_editor">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($body_content)): ?>
                        <div class="mt-6 text-base leading-6 text-sky-950 max-md:max-w-full wp_editor">
                            <?php echo wp_kses_post($body_content); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (is_array($content_button) && !empty($content_button['url']) && !empty($content_button['title'])): ?>
                        <div class="mt-6">
                            <a
                                href="<?php echo esc_url($content_button['url']); ?>"
                                target="<?php echo esc_attr($content_button['target'] ?? '_self'); ?>"
                                <?php if (($content_button['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                                class="inline-flex gap-2 justify-center items-center px-6 py-3 mx-auto text-sm font-bold text-white rounded-[100px] w-full md:w-fit border-[3px] border-[var(--Turquoise-500,#1C959B)] bg-[var(--Blue-SR-400,#008BCC)] hover:bg-[var(--Blue-SR-500,#00628F)] hover:text-white focus:outline-none focus-visible:ring-0 focus-visible:border-[3px] focus-visible:border-[var(--Turquoise-500,#1C959B)] focus-visible:bg-[var(--Blue-SR-500,#00628F)] focus-visible:text-white transition-colors duration-200 btn-primary"
                                aria-label="<?php echo esc_attr($content_button['title']); ?>"
                            >
                                <?php echo esc_html($content_button['title']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </article>

                <?php if ($image): ?>
                    <div class="overflow-hidden  rounded-lg w-full max-md:max-w-full <?php echo $reverse_layout ? 'md:order-1' : 'md:order-2'; ?>">
                        <div class="flex relative flex-col w-full xl:min-h-[448px] max-md:max-w-full">
                            <?php
                            echo wp_get_attachment_image($image, 'full', false, [
                                'alt' => esc_attr($image_alt),
                                'class' => 'object-cover w-full h-full rounded-lg',
                                'loading' => 'lazy'
                            ]);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<style>

#<?php echo esc_attr($section_id); ?> .wp_editor p:last-child{
    margin-bottom: 0;
}

#<?php echo esc_attr($section_id); ?> .wp_editor ul {
    list-style: none;
    margin: 0 0 1rem;
    padding: 0;
}

#<?php echo esc_attr($section_id); ?> .wp_editor ul li {
    position: relative;
    padding-left: 1.75rem;
    margin-bottom: 0.75rem;
}

#<?php echo esc_attr($section_id); ?> .wp_editor ul li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0.25rem;
    width: 16px;
    height: 16px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M13.3327 4L5.99935 11.3333L2.66602 8' stroke='%236EC4A9' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: 16px 16px;
}

</style>