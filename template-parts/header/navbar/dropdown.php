<?php
$item      = $args['item'] ?? null;
$index     = $args['index'] ?? 0;

// Guard
if (!$item) {
    return;
}

// Ensure children are 0-based arrays so third-tier index matches
$item_children = !empty($item->children) && is_iterable($item->children)
    ? array_values($item->children)
    : [];

// Get dropdown images from theme options
$nav_settings    = get_field('navigation_settings_start', 'option') ?: [];
$dropdown_images = $nav_settings['dropdown_images'] ?? [];

// Create images map by menu item ID
$imagesMap = [];
if ($dropdown_images) {
    foreach ($dropdown_images as $dropdown_image) {
        $menu_item_id = $dropdown_image['menu_item'] ?? null;
        $image        = $dropdown_image['image'] ?? null;

        if ($menu_item_id && $image) {
            $imagesMap[$menu_item_id] = $image;
        }
    }
}

/**
 * Get the WP menu-item ID that was used in the repeater.
 * Navi exposes both ->id and ->ID depending on version.
 */
$item_id = $item->ID ?? $item->id ?? null;

/** @var array|null $img */
$img = $imagesMap[$item_id] ?? null;

$img_url   = is_array($img) && !empty($img['url'])   ? $img['url']   : '';
$img_alt   = is_array($img) && !empty($img['alt'])   ? $img['alt']   : ($item->label . ' image');
$img_title = is_array($img) && !empty($img['title']) ? $img['title'] : $img_alt;
?>

<div
    x-show="activeDropdown === <?php echo (int) $index; ?>"
    x-cloak
    x-transition.opacity.duration.200ms
    class="fixed left-0 top-[120px] w-full z-40"
    @click.away="activeDropdown = null"
    @keydown.escape.window="activeDropdown = null"
    role="region"
    aria-label="<?php echo esc_attr($item->label); ?> submenu"
    style="display: none;"
>
    <!-- Overlay only below navbar -->
  <!--   <div
        class="fixed left-0 right-0 top-[132px] bottom-0 z-40 bg-black/30"
        aria-hidden="true"
    ></div> -->

    <!-- Content wrapper -->
    <div class="relative z-50 w-full">
        <div class="flex flex-row gap-0 justify-start w-full max-w-[1440px] mx-auto">

            <!-- Megamenu Container -->
            <div
                class="flex overflow-hidden flex-wrap pl-14 w-full max-w-[1168px] mx-auto text-sm leading-none text-sky-800 bg-white rounded-lg shadow-lg max-md:pl-5"
                role="navigation"
                aria-label="<?php echo esc_attr($item->label); ?> menu"
                x-data="{ activeTier3Index: null }"
                @mouseleave="activeTier3Index = null; activeDropdown = null"
            >
                <!-- Title -->
                <header class="flex gap-2 justify-center items-center pl-8 w-44 h-full text-3xl font-light leading-none whitespace-nowrap grow shrink">
                    <h2 class="flex-1 self-stretch my-auto text-sky-800 shrink basis-0">
                        <?php echo esc_html($item->label); ?>
                    </h2>
                </header>

                <!-- Second-level nav -->
                <?php if ($item_children) : ?>
                    <section
                        class="flex flex-col grow shrink justify-center self-start px-8 min-h-[336px] w-[241px] max-md:px-5"
                        aria-label="Main navigation items"
                    >
                        <ul class="py-14 space-y-2" role="list">
                            <?php foreach ($item_children as $child_index => $child) : ?>
                                <?php
                                $child_children = !empty($child->children) && is_iterable($child->children)
                                    ? array_values($child->children)
                                    : [];
                                ?>
                                <li>
                                    <?php if ($child_children) : ?>
                                        <a
                                            href="<?php echo esc_url($child->url); ?>"
                                            class="btn flex flex-col justify-center items-start gap-0.5 self-stretch py-2 pr-3 pl-3.5 w-full rounded-[100px] text-left whitespace-nowrap border-0 outline-none shadow-none transition-colors duration-200 hover:!border-0 hover:!outline-none hover:!shadow-none hover:!bg-[#008BCC] focus:bg-[#008BCC]"
                                            :class="activeTier3Index === <?php echo (int) $child_index; ?> ? 'bg-[#008BCC]' : ''"
                                            aria-label="<?php echo esc_attr($child->label); ?>"
                                            :aria-expanded="activeTier3Index === <?php echo (int) $child_index; ?> ? 'true' : 'false'"
                                            aria-controls="tier3-<?php echo (int) $index; ?>-<?php echo (int) $child_index; ?>"
                                            @mouseenter="activeTier3Index = <?php echo (int) $child_index; ?>"
                                            <?php if (!empty($child->target)) : ?>target="<?php echo esc_attr($child->target); ?>"<?php endif; ?>
                                        >
                                            <div class="flex items-center w-full">
                                                <span
                                                    class="flex-1 my-auto shrink basis-0 font-sans text-[14px] font-normal not-italic leading-[20px] transition-colors duration-200"
                                                    :class="activeTier3Index === <?php echo (int) $child_index; ?> ? 'text-white' : 'text-[var(--Blue-SR-500,#00628F)]'"
                                                >
                                                    <?php echo esc_html($child->label); ?>
                                                </span>

                                                <i
                                                    class="text-sm fa-solid fa-chevron-right shrink-0 transition-colors duration-200 ml-auto"
                                                    :class="activeTier3Index === <?php echo (int) $child_index; ?> ? 'text-white' : 'text-[var(--Blue-SR-500,#00628F)]'"
                                                    aria-hidden="true"
                                                ></i>
                                            </div>
                                        </a>
                                    <?php else : ?>
                                        <a
                                            href="<?php echo esc_url($child->url); ?>"
                                            class="btn flex flex-col justify-center py-2 pr-3 pl-3.5 w-full rounded-[100px] text-left border-0 outline-none shadow-none transition-colors duration-200 whitespace-nowrap font-sans text-[14px] font-normal not-italic leading-[20px] text-[var(--Blue-SR-500,#00628F)] hover:!bg-[#75e0e6] hover:!border-0 hover:!outline-none hover:!shadow-none focus:bg-[#75e0e6]"
                                            <?php if (!empty($child->target)) : ?>target="<?php echo esc_attr($child->target); ?>"<?php endif; ?>
                                            @mouseenter="activeTier3Index = null"
                                        >
                                            <span class="self-stretch my-auto"><?php echo esc_html($child->label); ?></span>
                                        </a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <!-- Right column -->
                <div class="flex grow shrink justify-center min-w-60 w-[563px] max-md:max-w-full overflow-hidden">
                    <!-- Default image -->
                    <?php if ($img_url) : ?>
                        <div
                            x-show="activeTier3Index === null"
                            x-transition.opacity.duration.150ms
                            class="flex flex-col justify-center px-5 w-full"
                            style="display: none;"
                        >
                            <img
                                src="<?php echo esc_url($img_url); ?>"
                                alt="<?php echo esc_attr($img_alt); ?>"
                                title="<?php echo esc_attr($img_title); ?>"
                                class="object-cover w-full rounded-lg aspect-[2.02] min-h-[calc(100%-48px)] max-h-[288px] max-md:max-w-full"
                                loading="lazy"
                            />
                        </div>
                    <?php endif; ?>

                    <!-- Third-tier panels -->
                    <?php if ($item_children) : ?>
                        <?php foreach ($item_children as $child_index => $child) : ?>
                            <?php
                            $child_children_tier3 = !empty($child->children) && is_iterable($child->children)
                                ? array_values($child->children)
                                : [];
                            ?>
                            <?php if ($child_children_tier3) : ?>
                                <aside
                                    id="tier3-<?php echo (int) $index; ?>-<?php echo (int) $child_index; ?>"
                                    x-show="activeTier3Index === <?php echo (int) $child_index; ?>"
                                    x-transition.opacity.duration.150ms
                                    class="grow shrink pr-6 bg-sky-50 w-[563px] max-md:max-w-full min-h-[336px]"
                                    aria-label="<?php echo esc_attr($child->label); ?> submenu"
                                    @mouseenter="activeTier3Index = <?php echo (int) $child_index; ?>"
                                    style="display: none;"
                                >
                                    <div class="flex flex-col items-start py-6 pr-6 pl-4 w-full bg-sky-50 rounded-lg max-md:pr-5 max-md:max-w-full">
                                        <header class="flex flex-col justify-center py-2 pr-3 pl-3.5 font-bold text-sky-800">
                                            <span class="self-stretch my-auto flex items-center" aria-hidden="true">
                                                <i class="text-sm fa-solid fa-chevron-left shrink-0 text-blue-900 mr-2" aria-hidden="true"></i>
                                                <?php echo esc_html($child->label); ?>
                                            </span>
                                        </header>

                                        <nav
                                            class="flex flex-col items-start pl-5 mt-2 max-w-full w-[182px]"
                                            aria-label="<?php echo esc_attr($child->label); ?> submenu items"
                                        >
                                            <ul class="space-y-1" role="list">
                                                <?php foreach ($child_children_tier3 as $grandchild) : ?>
                                                    <li>
                                                        <a
                                                            href="<?php echo esc_url($grandchild->url); ?>"
                                                            class="btn flex flex-col justify-center items-start py-2 pr-3 pl-3.5 w-full max-w-[162px] rounded-[100px] text-left hover:bg-white focus:bg-white transition-colors duration-200 whitespace-nowrap text-sky-800"
                                                            <?php if (!empty($grandchild->target)) : ?>target="<?php echo esc_attr($grandchild->target); ?>"<?php endif; ?>
                                                        >
                                                            <span class="self-stretch my-auto"><?php echo esc_html($grandchild->label); ?></span>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </aside>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>