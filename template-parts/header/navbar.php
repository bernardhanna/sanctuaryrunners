<?php
// Logo: prefer WP Site Logo, fallback to ACF option 'logo'
$theme_logo_id = get_theme_mod('custom_logo');
$acf_logo_id   = get_field('logo', 'option');
$logo_id       = $theme_logo_id ?: $acf_logo_id;

$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
$logo_alt = $logo_id ? (get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name')) : get_bloginfo('name');

// Optional: phone + CTA
$nav_settings   = get_field('navigation_settings_start', 'option') ?: [];
$phone_number   = $nav_settings['phone_number'] ?? null;
$contact_button = $nav_settings['contact_button'] ?? null;
$donate_button  = $nav_settings['donate_button'] ?? null;
$donate_icon    = $nav_settings['donate_icon'] ?? null;

use Log1x\Navi\Navi;

$primary_navigation = Navi::make()->build('primary');

// Split primary menu evenly (left/right) for the centered logo layout
$left_menu_items  = [];
$right_menu_items = [];
if ($primary_navigation->isNotEmpty()) {
  $items = $primary_navigation->toArray();
  $count = count($items);
  $left_count = (int) floor($count / 2);
  $left_menu_items  = array_slice($items, 0, $left_count);
  $right_menu_items = array_slice($items, $left_count);
}
?>

<section
  id="site-nav"
  x-data="{
    isOpen: false,
    activeDropdown: null,
    searchOpen: false,
    toggleDropdown(index) {
      this.activeDropdown = (this.activeDropdown === index ? null : index);
    },
    checkWindowSize() {
      if (window.innerWidth > 1084) {
        this.isOpen = false;
        this.activeDropdown = null;
      }
    }
  }"
  x-init="window.addEventListener('resize', () => checkWindowSize())"
  class="p-5 bg-transparent"
  style="z-index: 1000;"
  x-effect="isOpen ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
>
      <nav
        class="flex flex-wrap gap-10 justify-between items-center py-2.5 px-5 w-full rounded-lg bg-white min-h-[100px] shadow-[0px_0px_30px_rgba(36,0,68,0.05)] mx-auto max-w-[1280px]"
        role="navigation max-w-[1280px] mx-auto"
        aria-label="Main navigation"
      >
        <!-- Logo Section -->
        <div class="flex flex-col items-start my-auto max-w-[250px]">
          <a
            href="<?php echo esc_url(home_url('/')); ?>"
            style="z-index: 1000;"
            class="flex justify-start btn"
            aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> - Go to homepage"
          >
            <?php if ($logo_url) : ?>
              <div class="flex shrink-0 h-auto w-auto max-w-[124px]">
                <img
                  src="<?php echo esc_url($logo_url); ?>"
                  alt="<?php echo esc_attr($logo_alt); ?>"
                  class="object-contain z-50 w-full h-full"
                />
              </div>
            <?php else : ?>
              <div class="flex shrink-0 h-20 w-[124px] items-center justify-center bg-gray-200 rounded">
                <span class="text-xl font-bold text-slate-700"><?php echo get_bloginfo('name'); ?></span>
              </div>
            <?php endif; ?>
          </a>
        </div>
<div class="flex">
        <!-- Desktop Navigation Menu -->
        <?php if ($primary_navigation->isNotEmpty()) : ?>
          <ul
            id="primary-menu"
            class="hidden gap-1 items-center my-auto text-sm font-bold leading-none text-sky-800 min-w-60 max-md:max-w-full lg:flex"
            role="menubar"
          >
            <?php foreach ($primary_navigation->toArray() as $index => $item) : ?>
              <li
                class="relative group <?php echo esc_attr($item->classes); ?> <?php echo $item->active ? 'current-item' : ''; ?>"
                role="none"
                <?php if ($item->children) : ?>
                  @mouseenter="activeDropdown = <?php echo $index; ?>"
                  @mouseleave="activeDropdown = null"
                <?php endif; ?>
              >
                <div class="flex flex-col justify-center py-2 pr-3 pl-3.5 my-auto rounded-[100px]">
                  <div class="flex gap-1 items-center">
                    <a
                      href="<?php echo esc_url($item->url); ?>"
                      class="my-auto text-sky-800 hover:text-sky-900 btn <?php echo $item->active ? 'active-item font-semibold' : ''; ?>"
                      role="menuitem"
                      <?php if ($item->children) : ?>
                        aria-haspopup="true"
                        aria-expanded="false"
                        x-bind:aria-expanded="activeDropdown === <?php echo $index; ?>"
                      <?php endif; ?>
                    >
                      <?php echo esc_html($item->label); ?>
                    </a>
                    <?php if ($item->children) : ?>
                      <button
                        type="button"
                        class="p-1 ml-1 rounded btn"
                        @click="toggleDropdown(<?php echo $index; ?>)"
                        aria-label="Toggle <?php echo esc_attr($item->label); ?> submenu"
                      >
                        <img
                          src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/d0df69e40d4acbe67aaf2dd9aefce4391f13a037?placeholderIfAbsent=true"
                          alt=""
                          class="object-contain shrink-0 my-auto aspect-square w-[17px]"
                          role="presentation"
                        />
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
                <?php if ($item->children) : ?>
                  <?php get_template_part('template-parts/header/navbar/dropdown', null, ['item' => $item, 'index' => $index]); ?>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else : ?>
          <!-- Default menu items when no menu is set -->
          <ul
            class="hidden gap-1 items-center my-auto text-sm font-bold leading-none text-sky-800 min-w-60 max-md:max-w-full lg:flex"
            role="menubar"
          >
            <li role="none">
              <div class="flex flex-col justify-center py-2 pr-3 pl-3.5 my-auto rounded-[100px]">
                <div class="flex gap-1 items-center">
                  <a href="#" class="my-auto text-sky-800 hover:text-sky-900 btn" role="menuitem">
                    About
                  </a>
                  <button type="button" class="p-1 ml-1 rounded btn" aria-label="Toggle About submenu">
                    <img
                      src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/d0df69e40d4acbe67aaf2dd9aefce4391f13a037?placeholderIfAbsent=true"
                      alt=""
                      class="object-contain shrink-0 my-auto aspect-square w-[17px]"
                      role="presentation"
                    />
                  </button>
                </div>
              </div>
            </li>
            <li role="none">
              <div class="flex flex-col justify-center py-2 pr-3 pl-3.5 my-auto rounded-[100px]">
                <div class="flex gap-1 items-center">
                  <a href="#" class="my-auto text-sky-800 hover:text-sky-900 btn" role="menuitem">
                    What we do
                  </a>
                  <button type="button" class="p-1 ml-1 rounded btn" aria-label="Toggle What we do submenu">
                    <img
                      src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/d0df69e40d4acbe67aaf2dd9aefce4391f13a037?placeholderIfAbsent=true"
                      alt=""
                      class="object-contain shrink-0 my-auto aspect-square w-[17px]"
                      role="presentation"
                    />
                  </button>
                </div>
              </div>
            </li>
            <li role="none">
              <div class="flex flex-col justify-center py-2 pr-3 pl-3.5 my-auto rounded-[100px]">
                <div class="flex gap-1 items-center">
                  <a href="#" class="my-auto text-sky-800 hover:text-sky-900 btn" role="menuitem">
                    Find nearby runners
                  </a>
                </div>
              </div>
            </li>
            <li role="none">
              <div class="flex flex-col justify-center py-2 pr-3 pl-3.5 my-auto whitespace-nowrap rounded-[100px]">
                <div class="flex gap-1 items-center">
                  <a href="#" class="my-auto text-sky-800 hover:text-sky-900 btn" role="menuitem">
                    Latest
                  </a>
                  <button type="button" class="p-1 ml-1 rounded btn" aria-label="Toggle Latest submenu">
                    <img
                      src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/d0df69e40d4acbe67aaf2dd9aefce4391f13a037?placeholderIfAbsent=true"
                      alt=""
                      class="object-contain shrink-0 my-auto aspect-square w-[17px]"
                      role="presentation"
                    />
                  </button>
                </div>
              </div>
            </li>
          </ul>
        <?php endif; ?>

        <!-- Mobile Menu Toggle -->
        <?php get_template_part('template-parts/header/navbar/mobile'); ?>

        <!-- Search and Buttons Section -->
        <div class="flex gap-4 items-center my-auto min-w-60 max-lg:hidden">
          <!-- Language dropdown (opens on hover) -->
          <?php get_template_part('template-parts/header/navbar/language-dropdown'); ?>

          <div class="flex gap-2 justify-center items-center w-12 h-12 border border-gray-300 border-solid min-h-12 rounded-[1000px]">
            <button
              type="button"
              class="flex justify-center items-center w-full h-full btn"
              aria-label="Search site"
              aria-expanded="false"
              x-bind:aria-expanded="searchOpen"
              @click="searchOpen = true"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M14 14L11.1 11.1M12.6667 7.33333C12.6667 10.2789 10.2789 12.6667 7.33333 12.6667C4.38781 12.6667 2 10.2789 2 7.33333C2 4.38781 4.38781 2 7.33333 2C10.2789 2 12.6667 4.38781 12.6667 7.33333Z" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 items-center my-auto text-sm font-bold leading-none min-w-60">
            <!-- Donate Button -->
            <a
              href="<?php echo esc_url($donate_button && !empty($donate_button['url']) ? $donate_button['url'] : '#'); ?>"
              target="<?php echo esc_attr(($donate_button && !empty($donate_button['target'])) ? $donate_button['target'] : '_self'); ?>"
              class="btn flex gap-2 justify-center items-center px-4 py-3 my-auto text-sky-800 whitespace-nowrap border border-sky-800 border-solid min-h-[42px] rounded-[100px] w-fit hover:bg-sky-50 transition-colors duration-200"
              role="button"
            >
              <?php if ($donate_icon && !empty($donate_icon['url'])) : ?>
                <img
                  src="<?php echo esc_url($donate_icon['url']); ?>"
                  alt=""
                  class="object-contain w-4 shrink-0 aspect-square"
                  role="presentation"
                />
              <?php else : ?>
                <img
                  src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/cb3a3b809769d8b5aff9ca811be4ecf57a76c106?placeholderIfAbsent=true"
                  alt=""
                  class="object-contain w-4 shrink-0 aspect-square"
                  role="presentation"
                />
              <?php endif; ?>
              <span class="text-sky-800"><?php echo esc_html($donate_button && !empty($donate_button['title']) ? $donate_button['title'] : 'Donate'); ?></span>
            </a>

            <!-- Get Involved Button -->
            <?php if ($contact_button) : ?>
              <a
                href="<?php echo esc_url($contact_button['url']); ?>"
                target="<?php echo esc_attr($contact_button['target'] ?: '_self'); ?>"
                class="flex gap-2 justify-center items-center px-4 py-3 my-auto btn-primary"
                role="button"
              >
                <img
                  src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/bdfd6879aee0783dcef3f821bf3d6059e52cf826?placeholderIfAbsent=true"
                  alt=""
                  class="object-contain w-4 shrink-0 aspect-square"
                  role="presentation"
                />
                <span><?php echo esc_html($contact_button['title']); ?></span>
              </a>
            <?php else : ?>
              <a
                href="#"
                class="flex gap-2 justify-center items-center px-4 py-3 my-auto btn-primary"
                role="button"
              >
                <img
                  src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/bdfd6879aee0783dcef3f821bf3d6059e52cf826?placeholderIfAbsent=true"
                  alt=""
                  class="object-contain w-4 shrink-0 aspect-square"
                  role="presentation"
                />
                <span>Get involved</span>
              </a>
            <?php endif; ?>
          </div>
          </div>
        </div>
      </nav>

      <!-- Search overlay (WordPress site search) -->
      <div
        x-show="searchOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-start justify-center  pt-[8rem] px-4"
        @click.self="searchOpen = false"
        @keydown.escape.window="searchOpen = false"
        role="dialog"
        aria-label="Search"
        aria-modal="true"
      >
        <div
          class="relative p-4 w-full max-w-2xl bg-white rounded-lg shadow-xl"
          @click.stop
        >
          <form
            method="get"
            action="<?php echo esc_url(home_url('/')); ?>"
            role="search"
            class="flex gap-2 items-center"
            aria-label="<?php esc_attr_e('Search this site', 'matrix-starter'); ?>"
          >
            <label for="header-search-input" class="sr-only"><?php esc_html_e('Search', 'matrix-starter'); ?></label>
            <input
              type="search"
              id="header-search-input"
              name="s"
              placeholder="<?php esc_attr_e('Search this site…', 'matrix-starter'); ?>"
              class="flex-1 min-w-0 px-4 py-3 text-base border border-gray-300 rounded-[100px] focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
              autocomplete="off"
              autofocus
              x-ref="searchInput"
              x-init="$watch('searchOpen', open => { if (open) $nextTick(() => $refs.searchInput?.focus()); })"
            />
            <button
              type="submit"
              class="flex gap-2 items-center px-5 py-3 btn-primary shrink-0"
              aria-label="<?php esc_attr_e('Search', 'matrix-starter'); ?>"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M14 14L11.1 11.1M12.6667 7.33333C12.6667 10.2789 10.2789 12.6667 7.33333 12.6667C4.38781 12.6667 2 10.2789 2 7.33333C2 4.38781 4.38781 2 7.33333 2C10.2789 2 12.6667 4.38781 12.6667 7.33333Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span><?php esc_html_e('Search', 'matrix-starter'); ?></span>
            </button>
          </form>
          <button
            type="button"
            class="absolute -top-[1rem] -right-[1rem] p-2 text-white rounded-full transition-colors btn hover:text-gray-700 hover:bg-gray-100 bg-black"
            aria-label="<?php esc_attr_e('Close search', 'matrix-starter'); ?>"
            @click="searchOpen = false"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
</section>

<script>
  // Re-enable Headroom on the SAME element as before
  document.addEventListener('DOMContentLoaded', function () {
    if (window.Headroom) {
      var el = document.getElementById('site-nav');
      if (el) {
        var headroom = new Headroom(el);
        headroom.init();
      }
    }
  });
</script>
