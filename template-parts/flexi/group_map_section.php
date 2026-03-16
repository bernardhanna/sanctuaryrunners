<?php
/**
 * Frontend: Group Map Section
 * Displays running groups on an interactive map with call-to-action content
 */

if (!defined('ABSPATH')) exit;

// -------------------------------------------------
// ACF: Content
// -------------------------------------------------
$heading               = get_sub_field('heading') ?: 'Find your tribe.';
$heading_tag           = get_sub_field('heading_tag') ?: 'h2';
$description           = get_sub_field('description') ?: 'You don\'t have to run alone. Use our map to find a Sanctuary Runners group near you.<br>Whether you want to run, jog, or just walk and have a coffee, there is a place for you here. Simply enter your city or use your location to see where we meet this week.';
$secondary_heading     = get_sub_field('secondary_heading') ?: 'No group in you area?';
$secondary_heading_tag = get_sub_field('secondary_heading_tag') ?: 'h3';
$start_group_button    = get_sub_field('start_group_button');
$find_groups_button    = get_sub_field('find_groups_button');

// -------------------------------------------------
// ACF: Map settings
// -------------------------------------------------
$map_center_lat = get_sub_field('map_center_lat') ?: 53.349805;
$map_center_lng = get_sub_field('map_center_lng') ?: -6.26031;
$map_zoom       = get_sub_field('map_zoom') ?: 10;

// Default to OSM so the map always renders even without a Jawg token
$tile_provider  = get_sub_field('tile_provider') ?: 'osm';
$tile_api_key   = get_sub_field('tile_api_key') ?: '';

// -------------------------------------------------
// ACF: Design
// -------------------------------------------------
$background_color = get_sub_field('background_color') ?: '#fef3c7';

// -------------------------------------------------
// ACF: Padding settings (optional repeater)
// -------------------------------------------------
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = (string) get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if ($screen_size !== '' && $padding_top !== null) {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }
        if ($screen_size !== '' && $padding_bottom !== null) {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// -------------------------------------------------
// Unique section ID
// -------------------------------------------------
$section_id = 'group-map-' . wp_generate_uuid4();

// -------------------------------------------------
// Fetch Running Groups (markers)
// IMPORTANT: read coords from postmeta directly (most reliable)
// -------------------------------------------------
$running_group_ids = get_posts([
    'post_type'      => 'running_group',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'fields'         => 'ids',
]);

$groups_payload = [];
foreach ($running_group_ids as $group_id) {
    $lat_raw = get_post_meta($group_id, 'latitude', true);
    $lng_raw = get_post_meta($group_id, 'longitude', true);

    // Fallbacks for legacy/alternate field naming or map-style ACF fields.
    if ($lat_raw === '' || $lat_raw === null) $lat_raw = get_post_meta($group_id, 'lat', true);
    if ($lng_raw === '' || $lng_raw === null) $lng_raw = get_post_meta($group_id, 'lng', true);
    if ($lat_raw === '' || $lat_raw === null) $lat_raw = get_field('latitude', $group_id);
    if ($lng_raw === '' || $lng_raw === null) $lng_raw = get_field('longitude', $group_id);
    if ($lat_raw === '' || $lat_raw === null) $lat_raw = get_field('lat', $group_id);
    if ($lng_raw === '' || $lng_raw === null) $lng_raw = get_field('lng', $group_id);

    if ($lat_raw === '' || $lat_raw === null || $lng_raw === '' || $lng_raw === null) {
        $location = get_field('location', $group_id);
        if (is_array($location)) {
            $lat_raw = $lat_raw === '' || $lat_raw === null ? ($location['lat'] ?? '') : $lat_raw;
            $lng_raw = $lng_raw === '' || $lng_raw === null ? ($location['lng'] ?? '') : $lng_raw;
        }
    }

    $lat = is_string($lat_raw) ? str_replace(',', '.', trim($lat_raw)) : $lat_raw;
    $lng = is_string($lng_raw) ? str_replace(',', '.', trim($lng_raw)) : $lng_raw;

    if ($lat === '' || $lng === '' || !is_numeric($lat) || !is_numeric($lng)) {
        continue;
    }

    $groups_payload[] = [
        'id'           => (int) $group_id,
        'title'        => (string) get_the_title($group_id),
        'lat'          => (float) $lat,
        'lng'          => (float) $lng,
        'address'      => (string) get_field('address', $group_id),
        'meeting_time' => (string) get_field('meeting_time', $group_id),
        'contact_info' => (string) get_field('contact_info', $group_id),
        'url'          => (string) get_permalink($group_id),
    ];
}
?>

<section
    id="<?php echo esc_attr($section_id); ?>"
    class="relative overflow-hidden <?php echo esc_attr(implode(' ', $padding_classes)); ?>"
    style="background-color: <?php echo esc_attr($background_color); ?>;"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
>
    <div class="px-5 py-16 mx-auto w-full max-w-container lg:px-10">

        <div class="grid grid-cols-1 gap-10 items-start lg:grid-cols-12">

            <!-- Text Content -->
            <article class="flex flex-col justify-center p-8 w-full h-full lg:col-span-5" role="article">
                <header>
                    <?php if (!empty($heading)): ?>
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="text-4xl font-bold tracking-tight leading-tight text-sky-800"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($description)): ?>
                        <div class="mt-4 text-base leading-6 text-sky-950 wp_editor">
                            <?php echo wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>
                </header>

                <?php if (!empty($secondary_heading) || !empty($start_group_button)): ?>
                    <div class="mt-6 font-bold">
                        <?php if (!empty($secondary_heading)): ?>
                            <<?php echo esc_attr($secondary_heading_tag); ?> class="text-lg leading-none text-sky-950">
                                <?php echo esc_html($secondary_heading); ?>
                            </<?php echo esc_attr($secondary_heading_tag); ?>>
                        <?php endif; ?>

                        <?php if ($start_group_button && is_array($start_group_button) && !empty($start_group_button['url']) && !empty($start_group_button['title'])): ?>
                            <div class="mt-4">
                                <a
                                    href="<?php echo esc_url($start_group_button['url']); ?>"
                                    class="inline-flex justify-center items-center px-6 py-4 w-full text-sm text-sky-800 rounded-full border border-sky-800 transition-colors duration-300 hover:bg-sky-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-800 btn"
                                    target="<?php echo esc_attr($start_group_button['target'] ?? '_self'); ?>"
                                    aria-label="<?php echo esc_attr($start_group_button['title']); ?>"
                                >
                                    <?php echo esc_html($start_group_button['title']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </article>

            <!-- Map Column -->
            <div class="relative lg:col-span-7">
                <div
                    id="<?php echo esc_attr($section_id); ?>-map"
                    class="w-full h-[504px] md:h-[540px] rounded-lg overflow-hidden"
                    data-leaflet
                    data-provider="<?php echo esc_attr($tile_provider); ?>"
                    data-token="<?php echo esc_attr($tile_api_key); ?>"
                    data-lat="<?php echo esc_attr($map_center_lat); ?>"
                    data-lng="<?php echo esc_attr($map_center_lng); ?>"
                    data-zoom="<?php echo esc_attr($map_zoom); ?>"
                    role="application"
                    aria-label="Interactive map showing running group locations"
                ></div>

                <!-- Safe JSON payload (prevents broken HTML attributes) -->
                <script type="application/json" id="<?php echo esc_attr($section_id); ?>-groups">
<?php echo wp_json_encode($groups_payload); ?>
                </script>

                <?php if ($find_groups_button && is_array($find_groups_button) && !empty($find_groups_button['url']) && !empty($find_groups_button['title'])): ?>
                    <div style="z-index: 1000;" class="flex absolute right-0 left-0 bottom-8 justify-center items-center mx-auto">
                        <a
                            href="<?php echo esc_url($find_groups_button['url']); ?>"
                            class="inline-flex gap-2 justify-center items-center px-6 py-3 mx-auto text-sm text-white bg-sky-600 rounded-full transition-colors duration-300 w-fit hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-600 btn"
                            target="<?php echo esc_attr($find_groups_button['target'] ?? '_self'); ?>"
                            aria-label="<?php echo esc_attr($find_groups_button['title']); ?>"
                        >
                            <?php echo esc_html($find_groups_button['title']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<script>
(function () {
  // Use jsDelivr (less blocked than unpkg on many sites)
  const LEAFLET_CSS = "https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css";
  const LEAFLET_JS  = "https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js";

  function loadLeafletIfNeeded(cb) {
    if (typeof window.L !== "undefined") return cb();

    // CSS
    if (!document.querySelector('link[data-leaflet-css]')) {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = LEAFLET_CSS;
      link.crossOrigin = "";
      link.setAttribute("data-leaflet-css", "1");
      document.head.appendChild(link);
    }

    // JS
    const existing = document.querySelector('script[data-leaflet-js]');
    if (existing) {
      const t = setInterval(() => {
        if (typeof window.L !== "undefined") {
          clearInterval(t);
          cb();
        }
      }, 30);
      setTimeout(() => clearInterval(t), 8000);
      return;
    }

    const script = document.createElement("script");
    script.src = LEAFLET_JS;
    script.defer = true;
    script.crossOrigin = "";
    script.setAttribute("data-leaflet-js", "1");
    script.onload = cb;
    document.body.appendChild(script);
  }

  function initGroupMap(container) {
    if (!container || container.dataset.initialized === "1") return;
    if (typeof window.L === "undefined") return;

    const provider = container.getAttribute("data-provider") || "osm";
    const token    = container.getAttribute("data-token") || "";
    const lat      = parseFloat(container.getAttribute("data-lat") || "53.349805");
    const lng      = parseFloat(container.getAttribute("data-lng") || "-6.26031");
    const zoom     = parseInt(container.getAttribute("data-zoom") || "10", 10);

    // Safe JSON read
    let groups = [];
    const jsonEl = document.getElementById(container.id.replace(/-map$/, "-groups"));
    if (jsonEl) {
      try { groups = JSON.parse(jsonEl.textContent || "[]"); } catch (e) {}
    }

    const map = L.map(container, { scrollWheelZoom: false }).setView([lat, lng], zoom);

    // Tile layer with Jawg fallback if token missing
    let tileUrl, tileOpts = {};
    const wantsJawg = (provider === "jawg-light" || provider === "jawg-dark");

    if (provider === "osm" || (wantsJawg && !token)) {
      tileUrl = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
      tileOpts = { maxZoom: 19, attribution: "&copy; OpenStreetMap" };
    } else if (provider === "jawg-dark") {
      tileUrl = "https://tile.jawg.io/jawg-dark/{z}/{x}/{y}{r}.png?access-token=" + encodeURIComponent(token);
      tileOpts = { maxZoom: 22, attribution: '&copy; <a href="https://www.jawg.io" target="_blank" rel="noopener">Jawg</a>' };
    } else {
      tileUrl = "https://tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png?access-token=" + encodeURIComponent(token);
      tileOpts = { maxZoom: 22, attribution: '&copy; <a href="https://www.jawg.io" target="_blank" rel="noopener">Jawg</a>' };
    }

    L.tileLayer(tileUrl, tileOpts).addTo(map);

    const bounds = [];
    groups.forEach((g) => {
      if (!g) return;

      const glat = Number(g.lat);
      const glng = Number(g.lng);

      if (!Number.isFinite(glat) || !Number.isFinite(glng)) return;

      const marker = L.marker([glat, glng]).addTo(map);
      bounds.push([glat, glng]);

      let popup = `<div style="max-width:240px;">`;
      popup += `<div style="font-weight:700;margin-bottom:4px;">${escapeHtml(g.title || "")}</div>`;
      if (g.address)      popup += `<div style="font-size:12px;margin-bottom:4px;"><strong>Address:</strong> ${escapeHtml(g.address)}</div>`;
      if (g.meeting_time) popup += `<div style="font-size:12px;margin-bottom:4px;"><strong>Meeting Time:</strong> ${escapeHtml(g.meeting_time)}</div>`;
      if (g.contact_info) popup += `<div style="font-size:12px;margin-bottom:6px;"><strong>Contact:</strong> ${escapeHtml(g.contact_info)}</div>`;
      if (g.url)          popup += `<a href="${g.url}" style="font-size:12px;text-decoration:underline;">View Details</a>`;
      popup += `</div>`;

      marker.bindPopup(popup);
    });

    // Auto-fit if we have markers
    if (bounds.length > 1) map.fitBounds(bounds, { padding: [30, 30] });
    if (bounds.length === 1) map.setView(bounds[0], Math.max(zoom, 12));

    container.dataset.initialized = "1";

    // Fix rendering when inside layout containers / after fonts load
    setTimeout(() => map.invalidateSize(), 50);
    setTimeout(() => map.invalidateSize(), 250);
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  document.addEventListener("DOMContentLoaded", function () {
    const el = document.querySelector("#<?php echo esc_js($section_id); ?>-map[data-leaflet]");
    if (!el) return;
    loadLeafletIfNeeded(() => initGroupMap(el));
  });
})();
</script>