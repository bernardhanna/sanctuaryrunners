const { test, expect } = require('@playwright/test');

const formPath = process.env.CONTACT_FORM_PATH || '/contact-us/';
const shouldSubmit = process.env.CONTACT_FORM_SUBMIT === '1';

test('smoke: structured contact form fields and flow', async ({ page }) => {
  await page.goto(formPath, { waitUntil: 'domcontentloaded', timeout: 45_000 });

  const structuredLocator = page.locator('form[data-contact-structured="1"]');
  const fallbackLocator = page.locator('form[data-theme-form]');
  const structuredCount = await structuredLocator.count();
  const fallbackCount = await fallbackLocator.count();

  if (structuredCount === 0) {
    const msg = `No structured contact form found on ${formPath}. Structured forms: ${structuredCount}, fallback forms: ${fallbackCount}.`;
    if (shouldSubmit) {
      throw new Error(`${msg} CONTACT_FORM_SUBMIT=1 requested, so test will not skip.`);
    }
    test.skip(true, `${msg} Set CONTACT_FORM_PATH to a page that includes contact_form_001.`);
  }

  const form = structuredLocator.first();
  await expect(form).toBeVisible();

  await form.locator('input[name="first_name"]').fill('Test');
  await form.locator('input[name="last_name"]').fill('User');
  await form.locator('input[name="email"]').fill('matrix.contact.smoke+test@example.com');
  await form.locator('input[name="phone"]').fill('+353851234567');

  await form.locator('select[name="subject_topic_select"]').selectOption({ label: 'Other' });
  await expect(form.locator('input[name="subject_topic_other"]')).toBeVisible();
  await form.locator('input[name="subject_topic_other"]').fill('Automated smoke enquiry');

  await form.locator('textarea[name="message"]').fill('Automated Playwright smoke test submission for structured contact form.');
  await form.locator('input[name="area_town"]').fill('Cork');
  const countyRegion = form.locator('[name="county_region"]');
  const countyRegionTag = await countyRegion.evaluate((el) => el.tagName.toLowerCase());
  if (countyRegionTag === 'select') {
    await countyRegion.selectOption({ label: 'Cork' }).catch(async () => {
      const firstRealOption = await countyRegion.locator('option').nth(1).getAttribute('value');
      if (firstRealOption) {
        await countyRegion.selectOption(firstRealOption);
      }
    });
  } else {
    await countyRegion.fill('Cork');
  }

  await form.locator('select[name="heard_about_select"]').selectOption({ label: 'Other' });
  await form.locator('input[name="heard_about_other"]').fill('Automated test');

  await form.locator('input[name="privacy_consent"]').check();
  await form.locator('input[name="keeping_in_touch_consent"]').check();

  await expect(form.locator('input[name="subject_topic"]')).toHaveValue('');
  await expect(form.locator('input[name="heard_about_us"]')).toHaveValue('');

  if (!shouldSubmit) {
    test.info().annotations.push({
      type: 'note',
      description: 'Set CONTACT_FORM_SUBMIT=1 to submit and send email.',
    });
    return;
  }

  const submit = form.locator('button[type="submit"]');
  await submit.click();
  await page.waitForTimeout(1500);
});
