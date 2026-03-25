<?php
$section_id = 'content-section-two-' . uniqid();
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$description = get_sub_field('description');
$body_content = get_sub_field('body_content');
$image = get_sub_field('image');
$image_alt = get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'Content image';
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
    <div class="flex flex-col items-center pt-12 pb-12 mx-auto w-full max-w-container max-lg:px-5">
        <div class="flex overflow-hidden flex-col justify-center self-stretch px-10 max-md:px-5">

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

            <div class="flex flex-wrap gap-10 items-start mt-6 w-full max-md:max-w-full">

                <?php if (!empty($body_content)): ?>
                    <div class="flex-1 text-base leading-6 shrink basis-0 text-sky-950 max-md:max-w-full wp_editor">
                        <?php echo wp_kses_post($body_content); ?>
                    </div>
                <?php endif; ?>

                <?php if ($image): ?>
                    <div class="overflow-hidden bg-gray-100 rounded-lg w-[448px] max-md:max-w-full">
                        <div class="flex relative flex-col w-full min-h-[448px] max-md:max-w-full">
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

.wp_editor p:last-child{
    margin-bottom: 0;
}

</style>