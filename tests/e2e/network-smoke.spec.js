const { test, expect } = require('@playwright/test');

const sitePaths = ['/', '/uk/', '/au/', '/global/'];

for (const path of sitePaths) {
  test(`smoke: site responds at ${path}`, async ({ page, baseURL }) => {
    const response = await page.goto(path, { waitUntil: 'commit', timeout: 45_000 });
    expect(response, `No response for ${path}`).not.toBeNull();
    expect(response.status(), `Unexpected status for ${path}`).toBeLessThan(400);

    const html = await response.text();
    expect(html.length).toBeGreaterThan(100);
    expect(html).not.toMatch(/Error establishing a database connection/i);
    expect(html).not.toMatch(/There has been a critical error on this website/i);

    if (baseURL) {
      const expectedPrefix = `${baseURL.replace(/\/$/, '')}${path === '/' ? '' : path}`;
      expect(page.url().startsWith(expectedPrefix)).toBeTruthy();
    }
  });

  test(`smoke: wp-json responds at ${path}wp-json/`, async ({ request }) => {
    const response = await request.get(`${path}wp-json/`, { timeout: 45_000 });
    expect(response.status()).toBeLessThan(400);
    const body = await response.text();
    expect(body.length).toBeGreaterThan(10);
  });
}
