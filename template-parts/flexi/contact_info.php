<?php
$section_id = 'contact-info-' . uniqid();
$heading = get_sub_field('heading');
$heading_tag = get_sub_field('heading_tag');
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
    class="relative flex overflow-hidden bg-[linear-gradient(313deg,_#059DED_24.08%,_#28B2FA_63%)] <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="flex flex-col items-center px-24 pt-10 pb-14 mx-auto w-full max-w-container max-md:px-5">

        <?php if (!empty($heading)): ?>
            <<?php echo esc_attr($heading_tag); ?>
                id="<?php echo esc_attr($section_id); ?>-heading"
                class="self-center mb-8 text-3xl font-semibold leading-none text-white"
            >
                <?php echo esc_html($heading); ?>
            </<?php echo esc_attr($heading_tag); ?>>
        <?php endif; ?>

        <?php if (have_rows('contact_locations')): ?>
            <div class="flex flex-wrap gap-8 items-start w-full text-sm max-md:max-w-full" role="list">
                <?php while (have_rows('contact_locations')): the_row();
                    $office_name = get_sub_field('office_name');
                    $address = get_sub_field('address');
                    $phone = get_sub_field('phone');
                    $email = get_sub_field('email');
                    $directions_link = get_sub_field('directions_link');
                    $location_icon = get_sub_field('location_icon');
                    $phone_icon = get_sub_field('phone_icon');
                    $email_icon = get_sub_field('email_icon');

                    $location_icon_alt = get_post_meta($location_icon, '_wp_attachment_image_alt', true) ?: 'Location icon';
                    $phone_icon_alt = get_post_meta($phone_icon, '_wp_attachment_image_alt', true) ?: 'Phone icon';
                    $email_icon_alt = get_post_meta($email_icon, '_wp_attachment_image_alt', true) ?: 'Email icon';
                ?>
                    <article class="flex-1 p-8 bg-white rounded-lg shrink basis-0 min-w-60 max-md:px-5" role="listitem">

                        <!-- Office Address Section -->
                        <div class="flex gap-2 items-start w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                            <div class="flex flex-col flex-1 shrink basis-0 min-w-60">
                                <?php if (!empty($office_name) || !empty($address)): ?>
                                    <address class="not-italic leading-5 text-slate-900">
                                        <?php if (!empty($office_name)): ?>
                                            <strong class="block font-bold text-sky-800">
                                                <?php echo esc_html($office_name); ?>
                                            </strong>
                                        <?php endif; ?>
                                        <?php if (!empty($address)): ?>
                                            <span class="wp_editor">
                                                <?php echo wp_kses_post($address); ?>
                                            </span>
                                        <?php endif; ?>
                                    </address>
                                <?php endif; ?>

                                <?php if ($directions_link && is_array($directions_link) && isset($directions_link['url'], $directions_link['title'])): ?>
                                    <div class="flex flex-col justify-center self-start pt-0.5 mt-1 font-bold leading-none text-sky-800 whitespace-nowrap">
                                        <div class="flex gap-1 items-center">
                                            <a
                                                href="<?php echo esc_url($directions_link['url']); ?>"
                                                class="self-stretch my-auto text-sky-800 hover:text-sky-900 focus:outline-none focus:ring-2 focus:ring-sky-800 focus:ring-offset-2 btn"
                                                target="<?php echo esc_attr($directions_link['target'] ?? '_self'); ?>"
                                                aria-label="Get directions to <?php echo esc_attr($office_name); ?>"
                                            >
                                                <?php echo esc_html($directions_link['title']); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Phone Section -->
                        <?php if (!empty($phone)): ?>
                            <div class="flex gap-2 items-center mt-4 w-full font-bold leading-none text-sky-800">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M21.9994 16.92V19.92C22.0006 20.1985 21.9435 20.4742 21.832 20.7294C21.7204 20.9845 21.5567 21.2136 21.3515 21.4019C21.1463 21.5901 20.904 21.7335 20.6402 21.8227C20.3764 21.9119 20.0968 21.9451 19.8194 21.92C16.7423 21.5856 13.7864 20.5342 11.1894 18.85C8.77327 17.3147 6.72478 15.2662 5.18945 12.85C3.49942 10.2412 2.44769 7.271 2.11944 4.18001C2.09446 3.90347 2.12732 3.62477 2.21595 3.36163C2.30457 3.09849 2.44702 2.85669 2.63421 2.65163C2.82141 2.44656 3.04925 2.28271 3.30324 2.17053C3.55722 2.05834 3.83179 2.00027 4.10945 2.00001H7.10945C7.59475 1.99523 8.06524 2.16708 8.43321 2.48354C8.80118 2.79999 9.04152 3.23945 9.10944 3.72001C9.23607 4.68007 9.47089 5.62273 9.80945 6.53001C9.94399 6.88793 9.97311 7.27692 9.89335 7.65089C9.8136 8.02485 9.62831 8.36812 9.35944 8.64001L8.08945 9.91001C9.513 12.4136 11.5859 14.4865 14.0894 15.91L15.3594 14.64C15.6313 14.3711 15.9746 14.1859 16.3486 14.1061C16.7225 14.0263 17.1115 14.0555 17.4694 14.19C18.3767 14.5286 19.3194 14.7634 20.2794 14.89C20.7652 14.9585 21.2088 15.2032 21.526 15.5775C21.8431 15.9518 22.0116 16.4296 21.9994 16.92Z" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                <div class="flex flex-col flex-1 items-start self-stretch my-auto shrink basis-0 min-w-60">
                                    <div class="flex flex-col justify-center pt-0.5">
                                        <div class="flex gap-1 items-center">
                                            <a
                                                href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"
                                                class="self-stretch my-auto text-sky-800 hover:text-sky-900 focus:outline-none focus:ring-2 focus:ring-sky-800 focus:ring-offset-2 btn"
                                                aria-label="Call <?php echo esc_attr($office_name); ?> at <?php echo esc_attr($phone); ?>"
                                            >
                                                <?php echo esc_html($phone); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Email Section -->
                        <?php if (!empty($email)): ?>
                            <div class="flex gap-2 items-center mt-4 w-full font-bold leading-none text-sky-800 whitespace-nowrap">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6M22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6M22 6L12 13L2 6" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                                <div class="flex flex-col flex-1 items-start self-stretch my-auto shrink basis-0 min-w-60">
                                    <div class="flex flex-col justify-center pt-0.5">
                                        <div class="flex gap-1 items-center">
                                            <a
                                                href="mailto:<?php echo esc_attr($email); ?>"
                                                class="self-stretch my-auto text-sky-800 hover:text-sky-900 focus:outline-none focus:ring-2 focus:ring-sky-800 focus:ring-offset-2 btn"
                                                aria-label="Email <?php echo esc_attr($office_name); ?> at <?php echo esc_attr($email); ?>"
                                            >
                                                <?php echo esc_html($email); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </article>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
