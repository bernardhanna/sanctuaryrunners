<?php
$item      = $args['item']   ?? null;
$index     = $args['index']  ?? 0;

// ------------- guard -------------
if (!$item) {
    return;
}

// Ensure children are 0-based arrays so third-tier index matches (Navi may key by id)
$item_children = !empty($item->children) && is_iterable($item->children) ? array_values($item->children) : [];

// Get dropdown images from theme options
$nav_settings = get_field('navigation_settings_start', 'option');
$dropdown_images = $nav_settings['dropdown_images'] ?? [];

// Create images map by menu item ID
$imagesMap = [];
if ($dropdown_images) {
    foreach ($dropdown_images as $dropdown_image) {
        $menu_item_id = $dropdown_image['menu_item'] ?? null;
        $image = $dropdown_image['image'] ?? null;
        if ($menu_item_id && $image) {
            $imagesMap[$menu_item_id] = $image;
        }
    }
}

/**
 * Get the WP menu-item ID that was used in the repeater.
 * Navi exposes both ->id and ->ID depending on version,
 * so ask for whichever exists first.
 */
$item_id = $item->ID ?? $item->id ?? null;

/** @var array|null $img  Full image array or null */
$img = $imagesMap[$item_id] ?? null;

/** Use safe helpers so null never throws notices */
$img_url   = is_array($img) && !empty($img['url'])   ? $img['url']   : '';
$img_alt   = is_array($img) && !empty($img['alt'])   ? $img['alt']   : ($item->label . ' image');
$img_title = is_array($img) && !empty($img['title']) ? $img['title'] : $img_alt;
?>

<div
    class="hidden overflow-hidden fixed left-0 z-50 w-screen bg-transparent group-hover:flex"
    x-show="activeDropdown === <?php echo $index; ?>"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
    @click.away="activeDropdown = null"
    @keydown.escape="activeDropdown = null"
    role="region"
    aria-label="<?php echo esc_attr($item->label); ?> submenu"
>
    <div class="w-full">
        <div class="flex flex-row gap-0 justify-start w-full max-w-[1440px] mx-auto">

            <!-- Megamenu Container: activeTier3Index = which second-level item (with children) is hovered -->
            <div
                class="flex overflow-hidden flex-wrap pl-14 w-full text-sm leading-none text-sky-800 bg-white rounded-lg shadow-lg max-md:pl-5"
                role="navigation"
                aria-label="<?php echo esc_attr($item->label); ?> menu"
                x-data="{ activeTier3Index: null }"
                @mouseleave="activeTier3Index = null"
            >
                <!-- Title (header) -->
                <header class="flex gap-2 justify-center items-center pl-8 w-44 h-full text-3xl font-light leading-none whitespace-nowrap grow shrink">
                    <h2 class="flex-1 self-stretch my-auto text-sky-800 shrink basis-0">
                        <?php echo esc_html($item->label); ?>
                    </h2>
                </header>

                <!-- Second-level nav -->
                <?php if ($item_children) : ?>
                <section
                    class="flex flex-col grow shrink justify-center self-start px-8  font-bold text-blue-900 min-h-[336px] w-[241px] max-md:px-5"
                    aria-label="Main navigation items"
                >
                    <ul class="py-14 space-y-2" role="list">
                        <?php foreach ($item_children as $child_index => $child) : ?>
                            <?php
                            $child_children = !empty($child->children) && is_iterable($child->children) ? array_values($child->children) : [];
                            ?>
                        <li>
                            <?php if ($child_children) : ?>
                            <button
                                type="button"
                                class="btn flex flex-col justify-center items-start gap-0.5 self-stretch py-2 pr-3 pl-3.5 w-full rounded-[100px] text-left transition-colors duration-200 whitespace-nowrap"
                                :class="activeTier3Index === <?php echo $child_index; ?> ? 'bg-[#008BCC] text-white hover:bg-[#0075b3] focus:bg-[#0075b3]' : 'hover:bg-sky-50 focus:bg-sky-50 text-blue-900'"
                                aria-label="<?php echo esc_attr($child->label); ?>"
                                :aria-expanded="activeTier3Index === <?php echo $child_index; ?>"
                                aria-controls="tier3-<?php echo $index; ?>-<?php echo $child_index; ?>"
                                @mouseenter="activeTier3Index = <?php echo $child_index; ?>"
                            >
                                <div class="flex justify-between items-center w-full">
                                    <span class="flex-1 my-auto shrink basis-0" :class="activeTier3Index === <?php echo $child_index; ?> ? 'text-white' : 'text-blue-900'">
                                        <?php echo esc_html($child->label); ?>
                                    </span>
                                    <i class="text-sm fa-solid fa-chevron-right shrink-0" :class="activeTier3Index === <?php echo $child_index; ?> ? 'text-white' : 'text-blue-900'" aria-hidden="true"></i>
                                </div>
                            </button>
                            <?php else : ?>
                            <a
                                href="<?php echo esc_url($child->url); ?>"
                                class="btn flex flex-col justify-center py-2 pr-3 pl-3.5 w-full rounded-[100px] text-left hover:bg-sky-50 focus:bg-sky-50 transition-colors duration-200 whitespace-nowrap text-blue-900 <?php echo $child->active ? 'font-semibold' : ''; ?>"
                                <?php if ($child->target) : ?>target="<?php echo esc_attr($child->target); ?>"<?php endif; ?>
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

                <!-- Right column: image OR third-tier aside (same width, transition between them) -->
                <div class="flex grow shrink justify-center pr-6 min-w-60 w-[563px] max-md:max-w-full overflow-hidden">
                    <!-- Image: visible when no third-tier is hovered -->
                    <?php if ($img_url) : ?>
                    <div
                        x-show="activeTier3Index === null"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="flex flex-col justify-center w-full"
                    >
                        <img
                            src="<?php echo esc_url($img_url); ?>"
                            alt="<?php echo esc_attr($img_alt); ?>"
                            title="<?php echo esc_attr($img_title); ?>"
                            class="object-contain w-full rounded-lg aspect-[2.02] min-h-72 max-md:max-w-full"
                            loading="lazy"
                        />
                    </div>
                    <?php endif; ?>

                    <!-- Third-tier panels (one per second-level item that has children) -->
                    <?php if ($item_children) : ?>
                        <?php foreach ($item_children as $child_index => $child) : ?>
                            <?php
                            $child_children_tier3 = !empty($child->children) && is_iterable($child->children) ? array_values($child->children) : [];
                            ?>
                            <?php if ($child_children_tier3) : ?>
                    <aside
                        id="tier3-<?php echo $index; ?>-<?php echo $child_index; ?>"
                        x-show="activeTier3Index === <?php echo $child_index; ?>"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="grow shrink  pr-6 bg-sky-50 w-[563px] max-md:max-w-full min-h-[336px]"
                        aria-label="<?php echo esc_attr($child->label); ?> submenu"
                        @mouseenter="activeTier3Index = <?php echo $child_index; ?>"
                    >
                        <div class="flex flex-col items-start py-6 pr-6 pl-4 w-full bg-sky-50 rounded-lg max-md:pr-5 max-md:max-w-full">
                            <header class="flex flex-col justify-center py-2 pr-3 pl-3.5 font-bold text-sky-800">
                                <span class="self-stretch my-auto" aria-hidden="true">&lt; <?php echo esc_html($child->label); ?></span>
                            </header>
                            <nav class="flex flex-col items-start pl-5 mt-2 max-w-full w-[182px]" aria-label="<?php echo esc_attr($child->label); ?> submenu items">
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
