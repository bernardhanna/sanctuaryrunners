<?php

$section_id = 'content-editor-' . wp_generate_uuid4();

$heading_text = get_sub_field('heading_text');
$heading_tag = get_sub_field('heading_tag');

$show_subheading = get_sub_field('show_subheading');
$subheading_wysiwyg = get_sub_field('subheading_wysiwyg');

$column_1 = get_sub_field('column_1');
$column_2 = get_sub_field('column_2');
$show_column_3 = get_sub_field('show_column_3');
$column_3 = get_sub_field('column_3');

$background_color = get_sub_field('background_color');
$heading_color = get_sub_field('heading_color');
$subheading_color = get_sub_field('subheading_color');
$body_text_color = get_sub_field('body_text_color');

$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size = get_sub_field('screen_size');
        $padding_top = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if ($screen_size !== '' && $padding_top !== '' && $padding_top !== null) {
            $padding_classes[] = $screen_size . ':pt-[' . $padding_top . 'rem]';
        }
        if ($screen_size !== '' && $padding_bottom !== '' && $padding_bottom !== null) {
            $padding_classes[] = $screen_size . ':pb-[' . $padding_bottom . 'rem]';
        }
    }
}
$padding_class_string = implode(' ', $padding_classes);

$allowed_tags = ['h1','h2','h3','h4','h5','h6','span','p'];
if (!in_array($heading_tag, $allowed_tags, true)) {
    $heading_tag = 'h3';
}

$heading_id = $section_id . '-heading';
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="flex overflow-hidden relative max-md:overflow-visible"
    aria-labelledby="<?php echo esc_attr($heading_id); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container pt-10 pb-12 max-lg:px-5 <?php echo esc_attr($padding_class_string); ?>">
        <div class="w-full max-w-[60rem] flex flex-col gap-6">
            <<?php echo esc_html($heading_tag); ?>
                id="<?php echo esc_attr($heading_id); ?>"
                class="w-full break-words text-left text-[1.5rem] font-[700] leading-[2rem] font-['Public Sans']"
                style="color: <?php echo esc_attr($heading_color); ?>;"
            >
                <?php echo esc_html($heading_text); ?>
            </<?php echo esc_html($heading_tag); ?>>

            <?php if (!empty($show_subheading) && !empty($subheading_wysiwyg)) { ?>
                <div
                    class="wp_editor w-full break-words text-left text-[1.25rem] font-[400] leading-[1.625rem] font-['Public Sans'] mb-0 [&_p]:mb-0"
                    style="color: <?php echo esc_attr($subheading_color); ?>;"
                >
                    <?php echo wp_kses_post($subheading_wysiwyg); ?>
                </div>
            <?php } ?>

            <div class="flex flex-col gap-6 md:gap-14 w-full md:flex-row md:items-start">
                <?php if (!empty($column_1)) { ?>
                    <div
                        class="wp_editor w-full md:w-1/2 whitespace-normal break-words text-left text-[1rem] font-[400] leading-[1.375rem] font-['Public Sans']"
                        style="color: <?php echo esc_attr($body_text_color); ?>;"
                    >
                        <?php echo wp_kses_post($column_1); ?>
                    </div>
                <?php } ?>

                <?php if (!empty($column_2)) { ?>
                    <div
                        class="wp_editor w-full md:w-1/2 whitespace-normal break-words text-left text-[1rem] font-[400] leading-[1.375rem] font-['Public Sans']"
                        style="color: <?php echo esc_attr($body_text_color); ?>;"
                    >
                        <?php echo wp_kses_post($column_2); ?>
                    </div>
                <?php } ?>
            </div>

            <?php if (!empty($show_column_3) && !empty($column_3)) { ?>
                <div
                    class="wp_editor w-full whitespace-normal break-words text-left text-[1rem] font-[400] leading-[1.375rem] font-['Public Sans']"
                    style="color: <?php echo esc_attr($body_text_color); ?>;"
                >
                    <?php echo wp_kses_post($column_3); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>