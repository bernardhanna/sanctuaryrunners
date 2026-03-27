<?php
// Logo: prefer WP Site Logo, fallback to ACF option 'logo'
$theme_logo_id = get_theme_mod('custom_logo');
$acf_logo_id   = get_field('logo', 'option');
$logo_id       = $theme_logo_id ?: $acf_logo_id;

$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
$logo_alt = $logo_id ? (get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name')) : get_bloginfo('name');

// Optional: phone + CTA
$nav_settings   = get_field('navigation_settings_start', 'option') ?: [];
$contact_button = $nav_settings['contact_button'] ?? null;
$donate_button  = $nav_settings['donate_button'] ?? null;
$donate_icon    = $nav_settings['donate_icon'] ?? null;
$show_country_picker = !array_key_exists('show_country_picker', $nav_settings) || (bool) $nav_settings['show_country_picker'];

use Log1x\Navi\Navi;
$primary_navigation = Navi::make()->build('primary');
?>

<section
  id="site-nav"
  x-data="{
    isOpen: false,
    activeDropdown: null,
    searchOpen: false,
    searchTriggerEl: null,
    lastScroll: 0,
    isVisible: true,

    toggleDropdown(index) {
      this.activeDropdown = (this.activeDropdown === index ? null : index);
    },

    checkWindowSize() {
      if (window.innerWidth > 1084) {
        this.isOpen = false;
        this.activeDropdown = null;
      }
    },

    handleScroll() {
      const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

      if (currentScroll <= 20) {
        this.isVisible = true;
      } else if (currentScroll > this.lastScroll) {
        this.isVisible = false;
      } else {
        this.isVisible = true;
      }

      this.lastScroll = currentScroll;
    },

    openSearch() {
      this.searchTriggerEl = document.activeElement;
      this.searchOpen = true;
      this.$nextTick(() => this.$refs.searchInput?.focus());
    },

    closeSearch() {
      this.searchOpen = false;
      this.$nextTick(() => this.searchTriggerEl?.focus());
    },

    shouldSkipNavOffset(target) {
      if (!target) return true;
      return !!target.closest('[data-disable-nav-offset=true], .no-nav-offset');
    },

    setContentOffset() {
  this.$nextTick(() => {
    document.querySelectorAll('[data-nav-offset=true]').forEach((el) => {
      el.style.paddingTop = '';
      el.removeAttribute('data-nav-offset');
    });

    const target =
      document.querySelector('main > section:first-of-type') ||
      document.querySelector('main section:first-of-type') ||
      document.querySelector('.site-main > section:first-of-type') ||
      document.querySelector('.site-main section:first-of-type');

    if (target && !this.shouldSkipNavOffset(target)) {
      const baseOffsetRem = window.innerWidth < 1024 ? 5 : 7.5;
      const rootFontSize = parseFloat(window.getComputedStyle(document.documentElement).fontSize) || 16;
      let offsetPx = Math.round(baseOffsetRem * rootFontSize);

      target.style.paddingTop = `${offsetPx}px`;
      target.setAttribute('data-nav-offset', 'true');

      const navRect = this.$refs.siteNavInner?.getBoundingClientRect();
      const anchor = target.querySelector('nav, h1, h2') || target.firstElementChild || target;

      if (navRect && anchor) {
        const desiredTop = Math.ceil(navRect.bottom) + 8;
        const anchorTop = Math.ceil(anchor.getBoundingClientRect().top);

        if (anchorTop < desiredTop) {
          // Keep the requested base offset, only add a small corrective bump if needed.
          const bumpPx = Math.min(48, desiredTop - anchorTop);
          offsetPx += bumpPx;
          target.style.paddingTop = `${offsetPx}px`;
        }
      }

      document.documentElement.style.setProperty('--site-nav-offset', `${offsetPx}px`);
    } else {
      document.documentElement.style.removeProperty('--site-nav-offset');
    }
  });
}
  }"
  x-init="
    checkWindowSize();
    setContentOffset();
    handleScroll();

    window.addEventListener('resize', () => {
      checkWindowSize();
      setContentOffset();
    });

    window.addEventListener('scroll', () => handleScroll());
  "
  x-effect="(isOpen || searchOpen) ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
  :class="isVisible ? 'translate-y-0' : '-translate-y-full'"
  class="fixed top-0 left-0 w-full z-[1000] px-5 pt-5 bg-transparent transition-transform duration-300"
>
  <nav
    x-ref="siteNavInner"
    class="flex items-center justify-between py-2.5 px-5 w-full rounded-lg bg-white min-h-[100px] shadow-[0px_0px_30px_rgba(36,0,68,0.05)] mx-auto max-w-[1280px]"
    role="navigation"
    aria-label="Main navigation"
  >

    <!-- LOGO -->
    <div class="flex shrink-0 items-center max-w-[250px]">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="flex btn">
        <?php if ($logo_url) : ?>
          <div class="max-w-[124px]" style="z-index: 99999999999999;">
            <img
              src="<?php echo esc_url($logo_url); ?>"
              alt="<?php echo esc_attr($logo_alt); ?>"
              class="object-contain w-full h-full"
            />
          </div>
        <?php endif; ?>
      </a>
    </div>

    <!-- CENTER MENU -->
    <div class="hidden flex-1 justify-center px-6 lg:flex">
      <?php if ($primary_navigation->isNotEmpty()) : ?>
        <ul id="primary-menu" class="flex gap-1 items-center text-sm font-bold text-sky-800">
          <?php foreach ($primary_navigation->toArray() as $index => $item) : ?>
            <li
              class="relative group <?php echo esc_attr($item->classes); ?>"
              <?php if ($item->children) : ?>
                @mouseenter="activeDropdown = <?php echo $index; ?>"
                @mouseleave="activeDropdown = null"
              <?php endif; ?>
            >
              <div class="flex items-center py-1 hover:bg-[#75E0E6]">
                <a
                  href="<?php echo esc_url($item->url); ?>"
                  class="text-sky-800 hover:text-sky-900   px-3.5 py-2 transition-colors duration-300  <?php echo $item->active ? 'font-semibold' : ''; ?>"
                >
                  <?php echo esc_html($item->label); ?>
                </a>

                <?php if ($item->children) : ?>
                  <button
                    type="button"
                    class="relative -left-2 p-1"
                    @click.stop="toggleDropdown(<?php echo $index; ?>)"
                  >
                    <img
                      src="https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/d0df69e40d4acbe67aaf2dd9aefce4391f13a037"
                      class="w-[17px]"
                      alt=""
                    />
                  </button>
                <?php endif; ?>
              </div>

              <?php if ($item->children) : ?>
                <?php get_template_part('template-parts/header/navbar/dropdown', null, ['item' => $item, 'index' => $index]); ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <!-- MOBILE -->
    <?php get_template_part('template-parts/header/navbar/mobile'); ?>

    <!-- RIGHT SIDE -->
    <div class="hidden gap-4 items-center lg:flex shrink-0">

      <?php if ($show_country_picker) : ?>
        <?php get_template_part('template-parts/header/navbar/language-dropdown'); ?>
      <?php endif; ?>

      <!-- SEARCH -->
      <div class="group w-12 h-12 flex items-center justify-center border border-gray-300 rounded-full hover:bg-[var(--Blue-SR-500,#00628F)] hover:shadow-[0_0_24px_0_#C2EDFF] transition-all duration-200">
        <button type="button" @click="openSearch()" aria-label="Open search dialog" :aria-expanded="searchOpen.toString()" aria-controls="site-search-dialog">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" aria-hidden="true" focusable="false">
            <path
              d="M14 14L11.1 11.1M12.6 7.3A5.3 5.3 0 1 1 2 7.3a5.3 5.3 0 0 1 10.6 0Z"
              class="transition-colors duration-200 group-hover:stroke-white"
              stroke="#00628F"
              stroke-width="2"
              stroke-linecap="round"
            />
          </svg>
        </button>
      </div>

      <!-- BUTTONS -->
      <div class="flex gap-2 items-center text-sm font-bold">

        <!-- DONATE -->
        <a
          href="<?php echo esc_url($donate_button['url'] ?? '#'); ?>"
          class="flex items-center gap-2 px-4 py-3 border border-sky-800 rounded-full transition-all duration-200 hover:shadow-[0_0_0_4px_var(--Turquoise-500,#1C959B)]"
        >
          <?php if ($donate_icon) : ?>
            <img src="<?php echo esc_url($donate_icon['url']); ?>" class="w-4" alt="" />
          <?php endif; ?>
          <span><?php echo esc_html($donate_button['title'] ?? 'Donate'); ?></span>
        </a>

        <!-- CTA -->
        <?php if ($contact_button) : ?>
          <a
            href="<?php echo esc_url($contact_button['url']); ?>"
            class="flex items-center gap-2 px-4 py-3 text-white font-bold rounded-[100px] bg-[linear-gradient(313deg,#059DED_24.08%,#28B2FA_63%)] transition-all duration-200 hover:shadow-[0_0_0_4px_var(--Mint-500,#87CEB7)]"
          >
            <span><?php echo esc_html($contact_button['title']); ?></span>
          </a>
        <?php endif; ?>

      </div>

    </div>

  </nav>

  <!-- SEARCH OVERLAY -->
  <div
    id="site-search-dialog"
    x-show="searchOpen"
    x-cloak
    role="dialog"
    aria-modal="true"
    aria-labelledby="site-search-title"
    @keydown.escape.window="closeSearch()"
    class="fixed inset-0 flex justify-center items-start pt-[8rem] bg-black/30 h-screen"
    @click.self="closeSearch()"
  >
    <div class="bg-white p-4 rounded-lg w-full max-w-[800px]">
      <h2 id="site-search-title" class="sr-only">Site search</h2>
      <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex gap-2">
        <label for="site-search-input" class="sr-only">Search the site</label>
        <input
          id="site-search-input"
          x-ref="searchInput"
          type="search"
          name="s"
          placeholder="Search..."
          autocomplete="off"
          class="flex-1 px-4 py-3 !border-none"
        />
        <button type="submit" class="px-5 btn-primary">Search</button>
      </form>
    </div>
  </div>
</section>