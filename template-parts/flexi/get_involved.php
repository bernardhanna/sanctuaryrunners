<?php
$section_id = 'get-involved-' . uniqid();
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
$donate_button = get_sub_field('donate_button');
$background_color = get_sub_field('background_color');
$involvement_items = get_sub_field('involvement_items');

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
    class="relative flex overflow-hidden bg-[linear-gradient(313deg,_#059DED_24.08%,_#28B2FA_63%)] <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center pt-5 pb-5 mx-auto w-full lg:py-12 max-w-container max-lg:px-5">
        <div class="grid grid-cols-1 gap-10 justify-center items-start px-8 w-full lg:grid-cols-[20%_80%] max-md:px-5">

            <!-- Left Column: Heading and Donate Button -->
            <div class="flex flex-col items-center md:items-start w-full">

                <?php if (!empty($heading)): ?>
                    <<?php echo esc_attr($heading_tag); ?>
                        id="<?php echo esc_attr($section_id); ?>-heading"
                        class="mb-4 text-3xl font-semibold leading-none text-white"
                    >
                        <?php echo esc_html($heading); ?>
                    </<?php echo esc_attr($heading_tag); ?>>
                <?php endif; ?>

                <?php if ($donate_button && is_array($donate_button) && isset($donate_button['url'], $donate_button['title'])): ?>
                    <a
                        href="<?php echo esc_url($donate_button['url']); ?>"
                        class="flex gap-2 justify-center items-center w-full md:w-fit px-8 py-4 text-base font-bold leading-none text-sky-800 bg-white rounded-full transition-colors duration-300 hover:bg-sky-50 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-sky-800 max-md:px-5"
                        target="<?php echo esc_attr($donate_button['target'] ?? '_self'); ?>"
                        aria-label="<?php echo esc_attr($donate_button['title']); ?> - Opens in <?php echo ($donate_button['target'] === '_blank') ? 'new window' : 'same window'; ?>"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="none">
                            <path d="M17.7139 0C21.2338 0 24 2.7546 24 6.28613C23.9997 10.5945 20.1141 14.126 14.2285 19.4629L12.5713 20.9717L10.9141 19.4629L10.8486 19.4033C11.9208 18.1933 12.5712 16.6012 12.5713 14.8574C12.5713 11.0703 9.50098 8 5.71387 8C4.24595 8.00009 2.88587 8.46154 1.77051 9.24707C1.36069 8.28069 1.14264 7.30523 1.14258 6.28613C1.14258 2.7546 3.90876 0 7.42871 0C9.41712 4.31932e-05 11.3256 0.925668 12.5713 2.37695C13.8169 0.925657 15.7255 0.00013013 17.7139 0ZM5.71387 9.14258C8.86984 9.14258 11.4287 11.7015 11.4287 14.8574C11.4286 18.0133 8.86975 20.5713 5.71387 20.5713C2.55818 20.5711 0.000150418 18.0131 0 14.8574C0 11.7016 2.55809 9.1428 5.71387 9.14258Z" fill="#009DE6"/>
                        </svg>

                        <span class="text-sky-800">
                            <?php echo esc_html($donate_button['title']); ?>
                        </span>
                    </a>
                <?php endif; ?>

            </div>

            <!-- Right Column: Involvement Items Grid -->
            <?php if ($involvement_items): ?>
                <div class="flex flex-col justify-center w-full">
                    <div class="grid grid-cols-1 gap-8 md:gap-16 w-full md:grid-cols-2">
                        <?php foreach ($involvement_items as $index => $item):
                            $item_icon = $item['icon'];
                            $item_icon_alt = get_post_meta($item_icon, '_wp_attachment_image_alt', true) ?: 'Icon';
                            $item_title = $item['title'];
                            $item_link = $item['link'];
                            $item_description = $item['description'];
                            $item_id = $section_id . '-item-' . ($index + 1);
                        ?>
                            <article class="flex gap-4 items-start w-full">
                                <!-- Icon -->
                                <div
                                  
                                    role="img"
                                    aria-label="<?php echo esc_attr($item_title); ?> icon"
                                >
                                    <?php if ($item_icon): ?>
                                        <?php echo wp_get_attachment_image($item_icon, 'thumbnail', false, [
                                            'alt' => esc_attr($item_icon_alt),
                                            'class' => 'w-full h-auto object-contain',
                                        ]); ?>
                                    <?php else: ?>
                                        <div class="w-7 h-8 border border-sky-500 border-solid" aria-hidden="true"></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="flex flex-col flex-1 text-base text-white">
                                    <?php if ($item_link && is_array($item_link) && isset($item_link['url'], $item_link['title'])): ?>
                                        <div class="mb-2 font-bold leading-none">
                                            <a
                                                href="<?php echo esc_url($item_link['url']); ?>"
                                                class="flex gap-1 items-center text-white transition-colors duration-300 hover:text-yellow-300 focus:text-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 focus:ring-offset-transparent btn"
                                                target="<?php echo esc_attr($item_link['target'] ?? '_self'); ?>"
                                                aria-label="<?php echo esc_attr($item_link['title']); ?> - <?php echo esc_attr($item_description); ?>"
                                            >
                                                <span><?php echo esc_html($item_title); ?></span>
                                                <svg
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="w-6 h-6"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M5 12H19M19 12L12 5M19 12L12 19"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <h3 class="mb-2 font-bold leading-none text-white">
                                            <?php echo esc_html($item_title); ?>
                                        </h3>
                                    <?php endif; ?>

                                    <?php if (!empty($item_description)): ?>
                                        <p class="leading-6 text-white">
                                            <?php echo esc_html($item_description); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
