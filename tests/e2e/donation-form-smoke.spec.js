const { test, expect } = require('@playwright/test');

const donationPath = process.env.DONATION_FORM_PATH || '/donate/';
const shouldSubmit = process.env.DONATION_FORM_SUBMIT === '1';

test('smoke: donation form renders and can be filled', async ({ page }) => {
  await page.goto(donationPath, { waitUntil: 'domcontentloaded', timeout: 45_000 });

  const form = page.locator('form.salesformForm_donation, form.lead-form__styled').first();
  const formCount = await page.locator('form.salesformForm_donation, form.lead-form__styled').count();

  if (formCount === 0) {
    test.skip(true, `No donation form found on ${donationPath}. Set DONATION_FORM_PATH to the correct page.`);
  }

  await expect(form).toBeVisible();

  const nameInput = form.locator('input[name*="name"], input[name*="first"], input[type="text"]').first();
  const emailInput = form.locator('input[type="email"], input[name*="email"]').first();
  const amountInput = form.locator('input:not([type="hidden"])[name*="amount"], input:not([type="hidden"])[id*="amount"], input[type="number"]').first();

  if (await nameInput.count()) {
    await nameInput.fill('Matrix Smoke Donor');
  }
  if (await emailInput.count()) {
    await emailInput.fill('matrix.donation.smoke@example.com');
  }
  if (await amountInput.count()) {
    await amountInput.fill('10');
  }

  if (!shouldSubmit) {
    test.info().annotations.push({
      type: 'note',
      description: 'Set DONATION_FORM_SUBMIT=1 to submit donation form in live/headed mode.',
    });
    return;
  }

  const submitBtn = form.locator('button[type="submit"], input[type="submit"]').first();
  if (await submitBtn.count()) {
    await submitBtn.click();
    await page.waitForTimeout(1500);
  }
});
