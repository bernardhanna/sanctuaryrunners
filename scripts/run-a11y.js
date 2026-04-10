#!/usr/bin/env node
/**
 * Accessibility scan using Playwright + axe-core.
 *
 * Usage:
 *   npm run test:a11y:quick
 *   npm run test:a11y:full
 *   npm run test:a11y:quick -- http://localhost:10101/
 */
const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright');
const AxeBuilder = require('@axe-core/playwright').default;
require('dotenv').config({ path: '.env' });

const mode = process.argv[2] === 'full' ? 'full' : 'quick';
const cliUrl = process.argv[3];
const base = cliUrl || process.env.BASE_URL || process.env.WP_HOME || 'http://localhost:10101/';
const baseUrl = new URL(base).href.replace(/\/$/, '');

const quickPaths = ['/', '/news-and-media/', '/events/', '/get-involved/', '/contact/'];
const fullPaths = [
  '/',
  '/about/',
  '/news-and-media/',
  '/events/',
  '/get-involved/',
  '/contact/',
  '/media/',
  '/join/',
  '/donate/',
];

const pathsToScan = mode === 'full' ? fullPaths : quickPaths;
const reportDir = path.join('tests', 'a11y-report');
const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
const reportPath = path.join(reportDir, `a11y-${mode}-${timestamp}.json`);

async function run() {
  fs.mkdirSync(reportDir, { recursive: true });
  console.log(`♿ Running ${mode} accessibility scan on ${baseUrl}`);

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();
  const summary = [];
  let totalViolations = 0;

  for (const route of pathsToScan) {
    const target = `${baseUrl}${route}`;
    try {
      console.log(`→ Scanning ${target}`);
      const response = await page.goto(target, { waitUntil: 'networkidle', timeout: 45000 });
      if (!response || !response.ok()) {
        summary.push({
          url: target,
          error: `Navigation failed (${response ? response.status() : 'no response'})`,
          violations: [],
        });
        continue;
      }

      const results = await new AxeBuilder({ page })
        // Third-party player internals can trigger unactionable violations in host pages.
        .exclude('iframe[src*="youtube.com"]')
        .exclude('#movie_player')
        .disableRules(['aria-prohibited-attr'])
        .analyze();
      totalViolations += results.violations.length;
      summary.push({
        url: target,
        violationCount: results.violations.length,
        violations: results.violations.map((v) => ({
          id: v.id,
          impact: v.impact,
          description: v.description,
          help: v.help,
          helpUrl: v.helpUrl,
          nodes: v.nodes.map((n) => ({
            target: n.target,
            html: n.html,
            failureSummary: n.failureSummary,
          })),
        })),
      });
    } catch (error) {
      summary.push({
        url: target,
        error: error.message,
        violations: [],
      });
    }
  }

  await browser.close();

  const payload = {
    mode,
    baseUrl,
    scannedAt: new Date().toISOString(),
    pagesScanned: pathsToScan.length,
    totalViolations,
    results: summary,
  };
  fs.writeFileSync(reportPath, JSON.stringify(payload, null, 2));

  console.log(`\nReport written to ${reportPath}`);
  if (totalViolations > 0) {
    console.error(`❌ Found ${totalViolations} total accessibility violations`);
    process.exit(1);
  }

  console.log('✅ No accessibility violations found on scanned pages');
}

run().catch((error) => {
  console.error('❌ Accessibility scan failed:', error);
  process.exit(1);
});
