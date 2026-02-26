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
          <!-- Search Icons -->
          <div class="flex gap-1 justify-center items-center px-1 my-auto w-12 min-h-12 rounded-[1000px]">
            <button
              type="button"
              class="flex justify-center items-center w-full h-full btn"
              aria-label="Search"
            >
              <img
                src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/39824b071242e5eb0fb53e8a89193884da687d50?placeholderIfAbsent=true"
                alt=""
                class="object-contain shrink-0 w-4 aspect-square shadow-[0px_1px_4px_rgba(0,0,0,0.25)]"
                role="presentation"
              />
              <img
                src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/a12a9f00529c849acb1271c9704ef0513fb6cc17?placeholderIfAbsent=true"
                alt=""
                class="object-contain shrink-0 w-2 aspect-[2] stroke-[2px] stroke-sky-800"
                role="presentation"
              />
            </button>
          </div>

          <div class="flex gap-2 justify-center items-center w-12 h-12 border border-gray-300 border-solid min-h-12 rounded-[1000px]">
            <button
              type="button"
              class="flex justify-center items-center w-full h-full btn"
              aria-label="Additional search options"
            >
              <img
                src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/94aa3e5d1aa638c8bdc4dc9145739711051eeff8?placeholderIfAbsent=true"
                alt=""
                class="object-contain w-4 aspect-square"
                role="presentation"
              />
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 items-center my-auto text-sm font-bold leading-none min-w-60">
            <!-- Donate Button -->
            <a
              href="<?php echo esc_url($nav_settings['donate_link'] ?? '#'); ?>"
              class="btn flex gap-2 justify-center items-center px-4 py-3 my-auto text-sky-800 whitespace-nowrap border border-sky-800 border-solid min-h-[42px] rounded-[100px] w-fit hover:bg-sky-50 transition-colors duration-200"
              role="button"
            >
              <img
                src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/cb3a3b809769d8b5aff9ca811be4ecf57a76c106?placeholderIfAbsent=true"
                alt=""
                class="object-contain w-4 shrink-0 aspect-square"
                role="presentation"
              />
              <span class="text-sky-800">Donate</span>
            </a>

            <!-- Get Involved Button -->
            <?php if ($contact_button) : ?>
              <a
                href="<?php echo esc_url($contact_button['url']); ?>"
                target="<?php echo esc_attr($contact_button['target'] ?: '_self'); ?>"
                class="btn flex gap-2 justify-center items-center px-4 py-3 my-auto text-white min-h-[42px] rounded-[100px] w-fit whitespace-nowrap bg-sky-800 hover:bg-sky-900 transition-colors duration-200"
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
                class="btn flex gap-2 justify-center items-center px-4 py-3 my-auto text-white min-h-[42px] rounded-[100px] w-fit whitespace-nowrap bg-sky-800 hover:bg-sky-900 transition-colors duration-200"
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
