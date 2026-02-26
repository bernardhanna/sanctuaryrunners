<?php
$item = $args['item'] ?? null;
$index = $args['index'] ?? 0;

if (!$item || !$item->children) {
    return;
}
?>

<div
  class="absolute left-0 top-full z-50 mt-2 w-64 bg-white rounded-lg border border-gray-200 shadow-lg"
  x-show="activeDropdown === <?php echo $index; ?>"
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0 transform scale-95"
  x-transition:enter-end="opacity-100 transform scale-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100 transform scale-100"
  x-transition:leave-end="opacity-0 transform scale-95"
  @click.away="activeDropdown = null"
  role="menu"
  aria-labelledby="menu-button-<?php echo $index; ?>"
>
  <ul class="py-2" role="none">
    <?php foreach ($item->children as $child) : ?>
      <li role="none">
        <a
          href="<?php echo esc_url($child->url); ?>"
          class="btn block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150 <?php echo $child->active ? 'bg-gray-50 text-gray-900 font-medium' : ''; ?>"
          role="menuitem"
        >
          <?php echo esc_html($child->label); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
