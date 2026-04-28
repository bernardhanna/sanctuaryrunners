<?php
use StoutLogic\AcfBuilder\FieldsBuilder;

$forms_opts = new FieldsBuilder('contact_forms_settings', [
  'label' => 'Contact Forms',
]);

$forms_opts
  // Email defaults
  ->addText('email_from_name', [
    'label'         => 'Default From Name',
    'default_value' => get_bloginfo('name'),
  ])
  ->addEmail('email_from_address', [
    'label'         => 'Default From Email',
    'instructions'  => 'Use a domain address e.g. no-reply@sanctuaryrunners.ie',
    'default_value' => 'no-reply@sanctuaryrunners.ie',
  ])

  // CAPTCHA provider
  ->addSelect('captcha_provider', [
    'label'         => 'Captcha Provider',
    'instructions'  => 'Choose a captcha provider (or None).',
    'choices'       => [
      'none'         => 'None',
      'recaptcha_v3' => 'Google reCAPTCHA v3',
      'turnstile'    => 'Cloudflare Turnstile',
    ],
    'default_value' => 'none',
    'ui'            => 1,
  ])
  // Newsletter / Brevo integration
  ->addTrueFalse('newsletter_enabled', [
    'label'         => 'Enable Newsletter Signup',
    'instructions'  => 'Enable Brevo newsletter subscriptions from theme forms and newsletter blocks.',
    'default_value' => 0,
    'ui'            => 1,
  ])
  ->addText('brevo_api_key', [
    'label'             => 'Brevo API Key',
    'instructions'      => 'Your Brevo v3 API key. If empty, MATRIX_BREVO_KEY constant is used when defined.',
    'conditional_logic' => [[['field' => 'newsletter_enabled','operator'=>'==','value'=>'1']]],
  ])
  ->addText('brevo_list_ids', [
    'label'             => 'Default Brevo List IDs',
    'instructions'      => 'Comma-separated list IDs (for example: 2,14,29). You can use this, the selector below, or both.',
    'conditional_logic' => [[['field' => 'newsletter_enabled','operator'=>'==','value'=>'1']]],
  ])
  ->addSelect('brevo_list_ids_select', [
    'label'             => 'Select Brevo Lists',
    'instructions'      => 'Fetched from Brevo using your API key. You can combine this with manual IDs above.',
    'choices'           => [],
    'multiple'          => 1,
    'ui'                => 1,
    'allow_null'        => 1,
    'ajax'              => 0,
    'conditional_logic' => [[['field' => 'newsletter_enabled','operator'=>'==','value'=>'1']]],
  ])
  ->addText('brevo_default_confirm_message', [
    'label'             => 'Newsletter Success Message',
    'default_value'     => 'Thanks, you are subscribed!',
    'conditional_logic' => [[['field' => 'newsletter_enabled','operator'=>'==','value'=>'1']]],
  ])
  ->addText('brevo_error_message', [
    'label'             => 'Newsletter Error Message',
    'default_value'     => 'Sorry, something went wrong. Please try again.',
    'conditional_logic' => [[['field' => 'newsletter_enabled','operator'=>'==','value'=>'1']]],
  ])

  // Google reCAPTCHA v3 keys
  ->addText('recaptcha_site_key', [
    'label'            => 'reCAPTCHA Site Key',
    'conditional_logic'=> [[['field' => 'captcha_provider','operator'=>'==','value'=>'recaptcha_v3']]],
  ])
  ->addText('recaptcha_secret_key', [
    'label'            => 'reCAPTCHA Secret Key',
    'conditional_logic'=> [[['field' => 'captcha_provider','operator'=>'==','value'=>'recaptcha_v3']]],
  ])

  // Cloudflare Turnstile keys
  ->addText('turnstile_site_key', [
    'label'            => 'Turnstile Site Key',
    'conditional_logic'=> [[['field' => 'captcha_provider','operator'=>'==','value'=>'turnstile']]],
  ])
  ->addText('turnstile_secret_key', [
    'label'            => 'Turnstile Secret Key',
    'conditional_logic'=> [[['field' => 'captcha_provider','operator'=>'==','value'=>'turnstile']]],
  ])
  ->addTrueFalse('enable_form_webhook_sync', [
    'label'         => 'Enable Spreadsheet Webhook Sync',
    'instructions'  => 'Send each successful form submission to a webhook (Zapier/Make/Power Automate) for auto spreadsheet updates.',
    'default_value' => 0,
    'ui'            => 1,
  ])
  ->addUrl('form_webhook_url', [
    'label'             => 'Spreadsheet Webhook URL',
    'instructions'      => 'Paste your automation webhook URL (Zapier/Make/Power Automate or Google Apps Script Web App URL).',
    'conditional_logic' => [[['field' => 'enable_form_webhook_sync','operator'=>'==','value'=>'1']]],
  ])
  ->addText('form_webhook_secret', [
    'label'             => 'Webhook Secret (optional)',
    'instructions'      => 'If set, sent as X-Theme-Webhook-Secret header. You can auto-generate below.',
    'conditional_logic' => [[['field' => 'enable_form_webhook_sync','operator'=>'==','value'=>'1']]],
  ])
  ->addTrueFalse('regenerate_form_webhook_secret', [
    'label'             => 'Generate / Regenerate Webhook Secret',
    'instructions'      => 'Turn on and save to generate a new secure secret. It auto-resets back to Off after saving.',
    'default_value'     => 0,
    'ui'                => 1,
    'conditional_logic' => [[['field' => 'enable_form_webhook_sync','operator'=>'==','value'=>'1']]],
  ]);

if (!function_exists('matrix_generate_webhook_secret')) {
  function matrix_generate_webhook_secret(): string {
    try {
      return 'sr_webhook_' . bin2hex(random_bytes(24));
    } catch (\Throwable $e) {
      return 'sr_webhook_' . wp_generate_password(48, false, false);
    }
  }
}

if (!function_exists('matrix_maybe_generate_form_webhook_secret')) {
  function matrix_maybe_generate_form_webhook_secret($post_id): void {
    if ($post_id !== 'options' || !function_exists('get_field') || !function_exists('update_field')) {
      return;
    }

    $sync_enabled = (bool) get_field('enable_form_webhook_sync', 'option');
    if (!$sync_enabled) {
      return;
    }

    $current_secret = trim((string) get_field('form_webhook_secret', 'option'));
    $should_regenerate = (bool) get_field('regenerate_form_webhook_secret', 'option');

    if ($current_secret === '' || $should_regenerate) {
      update_field('form_webhook_secret', matrix_generate_webhook_secret(), 'option');
    }

    if ($should_regenerate) {
      update_field('regenerate_form_webhook_secret', 0, 'option');
    }
  }
}
add_action('acf/save_post', 'matrix_maybe_generate_form_webhook_secret', 20);

if (!function_exists('matrix_get_brevo_api_key')) {
  function matrix_get_brevo_api_key(): string {
    $api_key = function_exists('get_field') ? (string) get_field('brevo_api_key', 'option') : '';
    if ($api_key === '' && defined('MATRIX_BREVO_KEY')) {
      $api_key = (string) MATRIX_BREVO_KEY;
    }
    return trim($api_key);
  }
}

if (!function_exists('matrix_get_brevo_list_choices')) {
  function matrix_get_brevo_list_choices(string $api_key): array {
    if ($api_key === '') {
      return [];
    }

    $cache_key = 'matrix_brevo_list_choices_' . md5($api_key);
    $cached = get_transient($cache_key);
    if (is_array($cached)) {
      return $cached;
    }

    $response = wp_remote_get('https://api.brevo.com/v3/contacts/lists?limit=50', [
      'timeout' => 12,
      'headers' => [
        'accept' => 'application/json',
        'api-key' => $api_key,
      ],
    ]);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
      return [];
    }

    $data = json_decode((string) wp_remote_retrieve_body($response), true);
    if (!is_array($data) || empty($data['lists']) || !is_array($data['lists'])) {
      return [];
    }

    $choices = [];
    foreach ($data['lists'] as $list) {
      $id = isset($list['id']) ? absint($list['id']) : 0;
      if ($id <= 0) {
        continue;
      }
      $name = isset($list['name']) ? sanitize_text_field((string) $list['name']) : ('List ' . $id);
      $choices[(string) $id] = sprintf('%s (%d)', $name, $id);
    }

    set_transient($cache_key, $choices, 15 * MINUTE_IN_SECONDS);
    return $choices;
  }
}

add_filter('acf/load_field/name=brevo_list_ids_select', function ($field) {
  $field['choices'] = [];

  if (!function_exists('get_field')) {
    return $field;
  }

  $api_key = matrix_get_brevo_api_key();
  $choices = matrix_get_brevo_list_choices($api_key);

  // Preserve already-saved values in case API is unavailable.
  $saved_values = get_field('brevo_list_ids_select', 'option');
  if (is_array($saved_values)) {
    foreach ($saved_values as $saved_id) {
      $saved_id = (string) absint($saved_id);
      if ($saved_id !== '' && !isset($choices[$saved_id])) {
        $choices[$saved_id] = 'List ID ' . $saved_id;
      }
    }
  }

  $field['choices'] = $choices;
  return $field;
});

return $forms_opts;
