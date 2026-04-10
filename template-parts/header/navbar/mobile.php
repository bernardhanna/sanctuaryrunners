<?php
// Import Navi if not already imported
use Log1x\Navi\Navi;

// Build menus if not already provided
if (!isset($primary_navigation)) {
  $primary_navigation = Navi::make()->build('primary');
}
if (!isset($secondary_navigation)) {
  $secondary_navigation = Navi::make()->build('secondary');
}

// Options
$enable_hamburger     = get_field('enable_hamburger', 'option');
$hamburger_style      = get_field('hamburger_style', 'option');
$mobile_menu_effect   = get_field('mobile_menu_effect', 'option') ?: 'slide_up';
$mobile_menu_width    = (int) (get_field('mobile_menu_width', 'option') ?: 100);
$mobile_menu_bg       = get_field('mobile_menu_background', 'option') ?: '#FFFFFF';
$sticky_menu          = get_field('sticky_menu', 'option');

// Re-read CTA settings in this template scope for mobile action buttons.
$nav_settings   = get_field('navigation_settings_start', 'option') ?: [];
$contact_button = $nav_settings['contact_button'] ?? null;
$donate_button  = $nav_settings['donate_button'] ?? null;
$donate_icon    = $nav_settings['donate_icon'] ?? null;

// Transition mapping for panel
$effect_classes = [
  'slide_up'    => 'translate-y-full',
  'slide_left'  => '-translate-x-full',
  'slide_right' => 'translate-x-full',
  'fullscreen'  => 'translate-y-full',
];
$transition_class = $effect_classes[$mobile_menu_effect] ?? 'translate-y-full';

// Validate hamburger style
$valid_styles = [
  'hamburger--3dx','hamburger--3dx-r','hamburger--3dy','hamburger--3dy-r','hamburger--3dxy','hamburger--3dxy-r',
  'hamburger--arrow','hamburger--arrow-r','hamburger--arrowalt','hamburger--arrowalt-r','hamburger--arrowturn','hamburger--arrowturn-r',
  'hamburger--boring','hamburger--collapse','hamburger--collapse-r','hamburger--elastic','hamburger--elastic-r',
  'hamburger--emphatic','hamburger--emphatic-r','hamburger--minus','hamburger--slider','hamburger--slider-r',
  'hamburger--spin','hamburger--spin-r','hamburger--spring','hamburger--spring-r','hamburger--stand','hamburger--stand-r',
  'hamburger--squeeze','hamburger--vortex','hamburger--vortex-r',
];
if (!in_array($hamburger_style, $valid_styles, true)) {
  $hamburger_style = 'hamburger--spin';
}

// Mobile panel should always open full width.
$menu_width_style = "width: 100%; left: 0;";

// Prepare data for Alpine (Navi array is fine to JSON)
$menu_array = $primary_navigation->toArray();
?>

<?php if ($enable_hamburger): ?>
  <!-- Mobile Hamburger -->
  <button
    :class="{ 'is-active z-50 bg-transparent hover:bg-transparent flex items-center justify-center': isOpen }"
    class="hamburger mobile-hamburger-custom <?php echo esc_attr($hamburger_style); ?> min-[1201px]:hidden"
    type="button"
    aria-label="Menu"
    aria-expanded="false"
    @click="isOpen = !isOpen"
  >
    <span class="hamburger-box">
      <span class="hamburger-inner"></span>
    </span>
  </button>
<?php endif; ?>

<style>
  #site-nav .mobile-hamburger-custom .hamburger-box {
    width: 32px;
  }

  #site-nav .mobile-hamburger-custom .hamburger-inner,
  #site-nav .mobile-hamburger-custom .hamburger-inner::before,
  #site-nav .mobile-hamburger-custom .hamburger-inner::after {
    width: 32px !important;
    height: 3px !important;
    border-radius: 10px !important;
    background: #00628F !important;
  }
</style>

<?php if ($enable_hamburger && $primary_navigation->isNotEmpty()) : ?>
  <?php
  // Minimal recursive encoder for a Navi item -> plain array (label, url, children[])
    if (!function_exists('matrix_encode_menu_subtree')) {
      function matrix_encode_menu_subtree($node) {
        $label = isset($node->label) ? (string) $node->label : '';
        // Decode HTML entities → plain text, then strip any tags for safety
        $label = wp_strip_all_tags( wp_specialchars_decode( $label, ENT_QUOTES ) );

        $out = [
          'label'    => $label,
          'url'      => isset($node->url) ? (string) $node->url : '',
          'children' => [],
        ];

        if (!empty($node->children) && is_iterable($node->children)) {
          foreach ($node->children as $child) {
            $out['children'][] = matrix_encode_menu_subtree($child);
          }
        }
        return $out;
      }
    }

    $render_mobile_nav_actions = function () use ($contact_button, $donate_button, $donate_icon) {
      ?>
      <div class="pt-4 pb-[calc(env(safe-area-inset-bottom,0px)+1rem)]">
        <?php if (!empty($contact_button['url']) && !empty($contact_button['title'])): ?>
          <a
            href="<?php echo esc_url($contact_button['url']); ?>"
            class="flex gap-2 justify-center items-center px-4 w-full h-12 text-white font-bold rounded-full bg-[linear-gradient(313deg,#059DED_24.08%,#28B2FA_63%)] transition-all duration-200 hover:shadow-[0_0_0_4px_var(--Mint-500,#87CEB7)]"
            @click="isOpen = false"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M11.6667 4.00008C12.5871 4.00008 13.3333 3.25389 13.3333 2.33341C13.3333 1.41294 12.5871 0.666748 11.6667 0.666748C10.7462 0.666748 10 1.41294 10 2.33341C10 3.25389 10.7462 4.00008 11.6667 4.00008Z" fill="white"/>
              <path d="M10.4729 7.61176C10.6493 7.8668 10.902 8.06298 11.1969 8.17377C11.4918 8.28455 11.8145 8.30458 12.1215 8.23116L14.6665 7.61251L14.2958 6.16476L11.7508 6.78341L10.6924 5.24088C10.5807 5.07748 10.437 4.93709 10.2695 4.82773C10.102 4.71838 9.91394 4.64221 9.71612 4.60358L6.76431 4.02896C6.43409 3.96478 6.09134 4.00796 5.78886 4.15184C5.48637 4.29573 5.24094 4.53234 5.09038 4.82522L3.81787 7.29982L5.19093 7.96773L6.46345 5.49238L7.97312 5.7864L4.06961 12.1125H0.666504V13.605H4.06961C4.60532 13.605 5.10957 13.3274 5.38587 12.8804L6.85794 10.4953L10.8252 11.267L12.2189 15.3333L13.6741 14.8609L12.2811 10.7953C12.1949 10.5458 12.0428 10.3228 11.8399 10.1485C11.637 9.97433 11.3905 9.85508 11.1253 9.80278L8.79281 9.3498L10.1528 7.14535L10.4729 7.61176Z" fill="white"/>
            </svg>
            <span><?php echo esc_html($contact_button['title']); ?></span>
          </a>
        <?php endif; ?>

        <?php if (!empty($donate_button['url']) && !empty($donate_button['title'])): ?>
          <a
            href="<?php echo esc_url($donate_button['url']); ?>"
            class="flex gap-2 justify-center items-center px-4 mt-3 w-full min-h-[48px] h-12 font-bold text-[#00628F] rounded-full border border-sky-800 transition-all duration-200 hover:shadow-[0_0_0_4px_var(--Turquoise-500,#1C959B)]"
            @click="isOpen = false"
          >
            <?php if (!empty($donate_icon['url'])) : ?>
              <img src="<?php echo esc_url($donate_icon['url']); ?>" class="w-4" alt="" />
            <?php endif; ?>
            <span><?php echo esc_html($donate_button['title']); ?></span>
          </a>
        <?php endif; ?>
      </div>
      <?php
    };

  ?>
  <div
    x-data='{
      // Flyout state (hamburger’s isOpen lives on the parent)
      flyLevel: 0,          // 0: top, 1: second tier, 2: flattened descendants
      secondItems: [],      // items under the selected top-level item
      flattened: [],        // flattened descendants of selected second-tier item
      selectedTopLabel: "",
      selectedSecondLabel: "",

      openSecondFrom(el) {
        try {
          this.secondItems = JSON.parse(el.dataset.children || "[]");
        } catch(e) {
          this.secondItems = [];
        }
        this.selectedTopLabel = el.dataset.label || "";
        this.selectedSecondLabel = "";
        this.flyLevel = 1;
      },

      // True if this item has children (third tier or deeper) — show chevron and allow expand
      hasDescendants(node) {
        if (!node || !Array.isArray(node.children)) return false;
        return node.children.length > 0;
      },

      openFlatten(index) {
        const node = this.secondItems[index] || null;
        this.selectedSecondLabel = (node && node.label) ? node.label : "";
        const flatten = (n) => {
          let out = [];
          if (!n || !Array.isArray(n.children)) return out;
          n.children.forEach(ch => {
            if (ch.url && ch.label) out.push({ url: ch.url, label: ch.label });
            if (Array.isArray(ch.children) && ch.children.length) {
              out = out.concat(flatten(ch));
            }
          });
          return out;
        };
        // de-dupe by URL
        const seen = new Set();
        this.flattened = flatten(node).filter(i => i.url && !seen.has(i.url) && seen.add(i.url));
        this.flyLevel = 2;
      },

      back() {
        if (this.flyLevel === 2) {
          this.flyLevel = 1;
          this.flattened = [];
        } else if (this.flyLevel === 1) {
          this.flyLevel = 0;
          this.secondItems = [];
          this.selectedTopLabel = "";
        }
      }
    }'
    x-show="isOpen"
    :class="{ '<?php echo esc_attr($transition_class); ?>': !isOpen, 'translate-x-0 translate-y-0': isOpen }"
    class="fixed inset-0 z-40 w-screen h-screen <?php echo esc_attr($transition_class); ?> bg-white transition-transform duration-500 ease-out overflow-hidden"
    style="background-color: <?php echo esc_attr($mobile_menu_bg); ?>; width: 100vw; left: 0; right: 0;"
    x-transition:enter="transition ease-out duration-500"
    x-transition:leave="transition ease-in duration-300"
    @click.away="isOpen = false"
    @keydown.escape="isOpen = false"
  >
    <nav class="flex relative flex-col h-full" aria-label="Mobile navigation">
      <div class="px-5 pt-[7.5rem] pb-5 border-none">
        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" role="search" class="relative mt-5 w-full">
          <label for="mobile-help-search" class="sr-only">Search the site</label>
          <input
            id="mobile-help-search"
            type="search"
            name="s"
            placeholder="How can we help you?"
            class="w-full h-[52px] px-6 rounded-[1000px] border border-[#D0D5DD] text-[#00628F] placeholder:text-[#00628F] font-['Public_Sans'] text-[16px] not-italic font-normal leading-[22px] focus:outline-none focus:ring-2 focus:ring-sky-500"
          />
          <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M14 14L11.1 11.1M12.6667 7.33333C12.6667 10.2789 10.2789 12.6667 7.33333 12.6667C4.38781 12.6667 2 10.2789 2 7.33333C2 4.38781 4.38781 2 7.33333 2C10.2789 2 12.6667 4.38781 12.6667 7.33333Z" stroke="#00628F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </form>

        <template x-if="flyLevel > 0">
          <button
            type="button"
            class="flex gap-2 justify-start items-center px-4 mt-4 w-full h-[44px] text-[14px] leading-5 font-bold font-['Public_Sans'] text-white rounded-full bg-[#008BCC]"
            @click="back()"
            aria-label="Back to previous menu"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="7" height="11" viewBox="0 0 7 11" fill="none" aria-hidden="true">
              <path d="M5.25 9.5L1 5.25L5.25 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span x-text="flyLevel === 1 ? selectedTopLabel : selectedSecondLabel"></span>
          </button>
        </template>
      </div>

      <div class="relative flex-1 overflow-hidden">
        <!-- LEVEL 0: Top-level -->
        <div
          class="absolute inset-0 flex flex-col px-5 pt-4 pb-3 transition-transform duration-300"
          :class="flyLevel === 0 ? 'translate-x-0' : '-translate-x-full'"
        >
          <div class="flex-1 overflow-y-auto">
            <ul role="list">
              <?php $top_items = $menu_array; ?>
              <?php foreach ($top_items as $i => $item): ?>
                <?php
                $subtree = [];
                if (!empty($item->children)) {
                  foreach ($item->children as $child) {
                    $subtree[] = matrix_encode_menu_subtree($child);
                  }
                }
                $data_children = esc_attr(wp_json_encode($subtree, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP));
                ?>
                <li class="<?php echo esc_attr($item->classes); ?> <?php echo $item->active ? 'current-item' : ''; ?>">
                  <div class="flex justify-between items-center py-4 px-3">
                    <a
                      href="<?php echo esc_url($item->url); ?>"
                      class="font-['Public_Sans'] text-[14px] font-bold leading-5 text-[#1D3C94]"
                      @click="isOpen = false"
                    >
                      <?php echo esc_html($item->label); ?>
                    </a>

                    <?php if (!empty($item->children)) : ?>
                      <button
                        type="button"
                        class="ml-4 text-[#123B63]"
                        data-label="<?php echo esc_attr($item->label); ?>"
                        data-children="<?php echo $data_children; ?>"
                        @click.prevent="openSecondFrom($el)"
                        aria-label="View sub-menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                          <path d="M6.375 12.75L10.625 8.5L6.375 4.25" stroke="#1D3C94" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </button>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php $render_mobile_nav_actions(); ?>
          </div>
        </div>

        <!-- LEVEL 1: Second-tier of selected top item -->
        <div
          class="absolute inset-0 flex flex-col px-5 pt-4 pb-3 bg-white transition-transform duration-300"
          :class="flyLevel === 1 ? 'translate-x-0' : (flyLevel < 1 ? 'translate-x-full' : '-translate-x-full')"
          style="display:block;"
        >
          <div class="flex-1 overflow-y-auto">
            <ul role="list">
              <template x-for="(child, cidx) in secondItems" :key="cidx">
                <li>
                  <div
                    class="flex justify-between items-center py-4 px-3"
                  >
                    <a :href="child.url" class="font-['Public_Sans'] text-[14px] font-bold leading-5 text-[#1D3C94]" @click="isOpen = false" x-text="child.label"></a>

                    <template x-if="hasDescendants(child)">
                      <button
                        type="button"
                        class="ml-4 text-[#123B63]"
                        @click.prevent="openFlatten(cidx)"
                        aria-label="View deeper items">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                          <path d="M6.375 12.75L10.625 8.5L6.375 4.25" stroke="#1D3C94" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </button>
                    </template>
                  </div>
                </li>
              </template>
            </ul>
            <?php $render_mobile_nav_actions(); ?>
          </div>
        </div>

        <!-- LEVEL 2: Flattened descendants (3rd/4th/5th) -->
        <div
          class="absolute inset-0 flex flex-col px-5 pt-4 pb-3 bg-white transition-transform duration-300"
          :class="flyLevel === 2 ? 'translate-x-0' : 'translate-x-full'"
          style="display:block;"
        >
          <div class="flex-1 overflow-y-auto">
            <ul role="list">
              <template x-for="(leaf, lidx) in flattened" :key="lidx">
                <li>
                  <div
                    class="py-4 px-3"
                  >
                    <a :href="leaf.url" class="block font-['Public_Sans'] text-[14px] font-bold leading-5 text-[#1D3C94]" @click="isOpen = false" x-text="leaf.label"></a>
                  </div>
                </li>
              </template>
              <template x-if="!flattened.length">
                <li class="py-6 px-3 text-sm text-gray-500">No additional items.</li>
              </template>
            </ul>
            <?php $render_mobile_nav_actions(); ?>
          </div>
        </div>
      </div>
    </nav>
  </div>
<?php endif; ?>
