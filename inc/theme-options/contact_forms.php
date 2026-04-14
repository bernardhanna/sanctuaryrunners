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
    'instructions'      => 'If set, sent as X-Theme-Webhook-Secret header.',
    'conditional_logic' => [[['field' => 'enable_form_webhook_sync','operator'=>'==','value'=>'1']]],
  ]);

return $forms_opts;
