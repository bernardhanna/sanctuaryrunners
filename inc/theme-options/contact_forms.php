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
      'none'         => 'Nonea',
      'recaptcha_v3' => 'Google reCAPTCHA v3',
      'turnstile'    => 'Cloudflare Turnstile',
    ],
    'default_value' => 'none',
    'ui'            => 1,
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

return $forms_opts;
