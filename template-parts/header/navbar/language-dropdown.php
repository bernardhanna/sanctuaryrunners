<?php
/**
 * Language dropdown for the navbar.
 * Opens on hover; styled as a white panel with no box shadow.
 * Uses manual country options from theme settings, otherwise a static list.
 */

$languages = [];
$nav_settings = get_field('navigation_settings_start', 'option') ?: [];
$country_picker_options = $nav_settings['country_picker_options'] ?? [];

if (!empty($country_picker_options) && is_array($country_picker_options)) {
    foreach ($country_picker_options as $option) {
        $value = sanitize_title((string) ($option['value'] ?? ''));
        $label = trim((string) ($option['label'] ?? ''));
        $link = $option['link'] ?? null;
        $flag_icon = $option['flag_icon'] ?? null;

        $url = '#';
        $target = '';
        if (is_array($link) && !empty($link['url'])) {
            $url = $link['url'];
            $target = !empty($link['target']) ? $link['target'] : '';
        }

        $flag_url = null;
        if (is_array($flag_icon) && !empty($flag_icon['url'])) {
            $flag_url = $flag_icon['url'];
        }

        if ($value === '' || $label === '') {
            continue;
        }

        $languages[] = [
            'value' => $value,
            'label' => $label,
            'url'   => $url,
            'flag'  => $flag_url,
            'target' => $target,
        ];
    }
}

if (empty($languages)) {
    // Fallback when no manual options are configured
    $languages = [
        ['value' => 'ie', 'label' => 'Ireland', 'url' => '#', 'flag' => 'https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/ab16b90e79c12d127d396904f040cf46e7cb5eaa?placeholderIfAbsent=true', 'target' => ''],
        ['value' => 'uk', 'label' => 'United Kingdom', 'url' => '#', 'flag' => null, 'target' => ''],
        ['value' => 'australia', 'label' => 'Australia', 'url' => '#', 'flag' => 'https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/01e57acce2567c69db9be0aa06c4f52ee1a53efd?placeholderIfAbsent=true', 'target' => ''],
        ['value' => 'global', 'label' => 'Global', 'url' => '#', 'flag' => null, 'target' => ''],
    ];
}

$current = $languages[0] ?? null;

// Auto-select current country by matching the current site URL/host to option links.
$current_site_url = home_url('/');
$current_site_host = (string) wp_parse_url($current_site_url, PHP_URL_HOST);
$current_site_path = (string) wp_parse_url($current_site_url, PHP_URL_PATH);
$current_site_path = untrailingslashit($current_site_path);

$normalize_host = static function ($host) {
    $host = strtolower((string) $host);
    return preg_replace('/^www\./', '', $host);
};

$current_site_host = $normalize_host($current_site_host);

foreach ($languages as $lang) {
    $lang_url = (string) ($lang['url'] ?? '');
    if ($lang_url === '' || $lang_url === '#') {
        continue;
    }

    $lang_host = (string) wp_parse_url($lang_url, PHP_URL_HOST);
    $lang_path = (string) wp_parse_url($lang_url, PHP_URL_PATH);
    $lang_path = untrailingslashit($lang_path);

    if ($normalize_host($lang_host) !== $current_site_host) {
        continue;
    }

    // If host matches and no path is specified, this is the active country.
    if ($lang_path === '' || $lang_path === '/') {
        $current = $lang;
        break;
    }

    // If a path is specified, require exact or nested path match.
    if (
        $current_site_path === $lang_path ||
        strpos($current_site_path . '/', $lang_path . '/') === 0
    ) {
        $current = $lang;
        break;
    }
}
?>

<div
  id="language-dropdown"
  class="relative flex gap-1 justify-center items-center px-1 my-auto w-12 min-h-12 rounded-[1000px]"
  x-data="{ open: false }"
  @mouseenter="open = true"
  @mouseleave="open = false"
>
  <div class="w-full text-sm leading-none text-slate-900">
    <div class="rounded shadow-none">
      <!-- Compact trigger in nav (hover target): flag + arrow -->
      <a
        href="<?php echo esc_url($current['url'] ?? '#'); ?>"
        class="flex gap-1 justify-center items-center px-1 py-2.5 w-full bg-transparent rounded-t transition-colors duration-200"
        <?php if (!empty($current['target'])) : ?>target="<?php echo esc_attr($current['target']); ?>"<?php endif; ?>
        aria-haspopup="listbox"
        :aria-expanded="open"
        aria-labelledby="language-dropdown-label"
        id="language-dropdown-trigger"
        role="combobox"
      >
        <?php if (!empty($current['flag'])) : ?>
          <img
            src="<?php echo esc_url($current['flag']); ?>"
            alt=""
            class="object-contain w-6 h-6 shrink-0"
          />
        <?php else : ?>
          <div class="flex w-6 h-6 bg-gray-200 rounded shrink-0" aria-hidden="true"></div>
        <?php endif; ?>
        <span class="sr-only" id="language-dropdown-label"><?php echo esc_html($current['label'] ?? 'Language'); ?></span>
        <i
          class="text-sm transition-transform duration-200 fa-solid fa-chevron-down shrink-0 text-slate-700"
          :style="open ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'"
          aria-hidden="true"
        ></i>
      </a>

      <!-- Dropdown panel: list of language options only, no shadow -->
      <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute left-0 top-full z-10 mt-0 w-full rounded-b border-t border-gray-100 shadow-none"
        x-cloak
      >
        <ul
          class="rounded-b rounded-t border-t border-gray-100 bg-white min-w-[200px]"
          role="listbox"
          aria-labelledby="language-dropdown-label"
          id="language-dropdown-menu"
        >
          <?php foreach ($languages as $lang) : ?>
            <li role="option" aria-selected="<?php echo ($current['value'] ?? '') === ($lang['value'] ?? '') ? 'true' : 'false'; ?>" tabindex="-1">
              <a
                href="<?php echo esc_url($lang['url']); ?>"
                class="flex items-center px-4 py-2.5 w-full text-left transition-colors duration-200 btn hover:bg-gray-50 focus:bg-gray-50"
                data-value="<?php echo esc_attr($lang['value']); ?>"
                <?php if (!empty($lang['target'])) : ?>target="<?php echo esc_attr($lang['target']); ?>"<?php endif; ?>
              >
                <div class="flex gap-2 items-center">
                  <?php if (!empty($lang['flag'])) : ?>
                    <img src="<?php echo esc_url($lang['flag']); ?>" alt="" class="object-contain w-8 h-8 shrink-0" />
                  <?php else : ?>
                    <div class="flex w-8 h-8 bg-gray-200 rounded shrink-0" aria-hidden="true"></div>
                  <?php endif; ?>
                  <span class="text-slate-900"><?php echo esc_html($lang['label']); ?></span>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>
