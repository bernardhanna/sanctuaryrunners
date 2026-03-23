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

use Log1x\Navi\Navi;
$primary_navigation = Navi::make()->build('primary');
?>

<section
  id="site-nav"
  x-data="{
    isOpen: false,
    activeDropdown: null,
    searchOpen: false,
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

    if (target) {
      const offset = window.innerWidth < 1024 ? 60 : 120;
      target.style.paddingTop = `${offset}px`;
      target.setAttribute('data-nav-offset', 'true');
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
  x-effect="isOpen ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
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
          <div class="max-w-[124px]">
            <img
              src="<?php echo esc_url($logo_url); ?>"
              alt="<?php echo esc_attr($logo_alt); ?>"
              class="w-full h-full object-contain"
            />
          </div>
        <?php endif; ?>
      </a>
    </div>

    <!-- CENTER MENU -->
    <div class="hidden lg:flex flex-1 justify-center px-6">
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
    <div class="hidden lg:flex shrink-0 gap-4 items-center">

      <?php get_template_part('template-parts/header/navbar/language-dropdown'); ?>

      <!-- SEARCH -->
      <div class="group w-12 h-12 flex items-center justify-center border border-gray-300 rounded-full hover:bg-[var(--Blue-SR-500,#00628F)] hover:shadow-[0_0_24px_0_#C2EDFF] transition-all duration-200">
        <button type="button" @click="searchOpen = true" aria-label="Open search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none">
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
    x-show="searchOpen"
    x-cloak
    class="fixed inset-0 flex justify-center items-start pt-[8rem] bg-black/30"
    @click.self="searchOpen = false"
  >
    <div class="bg-white p-4 rounded-lg w-full max-w-[800px]">
      <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex gap-2">
        <input
          type="search"
          name="s"
          placeholder="Search..."
          class="flex-1 px-4 py-3 border rounded-full"
        />
        <button type="submit" class="btn-primary px-5">Search</button>
      </form>
    </div>
  </div>
</section>