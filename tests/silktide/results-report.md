# Silktide Accessibility Remediation Report

Date: 2026-04-10
Scope: `tests/silktide/checks.csv` (109 checks)

## What was fixed in this pass (theme-level)

1. Global form label wiring
   - Added a frontend script that pairs unlabeled `label` elements with nearby controls and auto-generates stable IDs when missing.
   - This directly targets:
     - Ensure form controls have labels
     - Fix missing ARIA label IDs
     - Ensure labels in the document point to valid IDs

2. New-tab link accessibility hardening
   - Added a frontend script that updates all `a[target="_blank"]` links to:
     - enforce `rel="noopener noreferrer"`
     - add/augment `aria-label` with `(opens in a new tab)`
   - This targets:
     - Ensure links explain they open in a new tab
     - Ensure links can be used by screen readers

3. Keyboard focus and selected-state visibility
   - Added global `:focus-visible` styles for links, buttons, and form controls.
   - Added visible checked-state ring for checkboxes/radios.
   - This targets:
     - Ensure controls clearly indicate when they are selected
     - Ensure focus is not fully/partly obscured

4. Existing skip-link support verified
   - `header.php` already includes a skip link to `#main-content` and focus behavior.
   - This supports:
     - Allow users to quickly skip to content

5. Obvious broken-link and spelling fixes
   - Updated default “contact us form” copy to include a direct link to:
     - `https://sanctuaryrunners.s1.matrix-test.com/contact/`
   - Corrected obvious typo:
     - `No group in you area?` -> `No group in your area?`
   - Corrected obvious internal spelling typos in comments:
     - `licenece` -> `license`
     - `RECIEVED` -> `RECEIVED`

## Progress by remediation category

### Fixed in code this pass
- Ensure form controls have labels
- Fix missing ARIA label IDs
- Ensure labels in the document fragment point to valid IDs
- Ensure labels in the document point to valid IDs
- Ensure links explain they open in a new tab
- Ensure controls clearly indicate when they are selected
- Check placeholder links work as expected (obvious cases only)
- Check and fix misspellings (obvious cases only)

### Requires content/editorial updates (cannot be fully solved in theme code)
- Check and fix misspellings
- Review potential grammar errors
- Check that headings and labels are descriptive
- Ensure content is not too difficult to understand
- Improve weak alternative text / ensure alternative text is appropriate
- Minimize thin pages

### Requires policy/legal/compliance updates
- Add an analytics solution to every page
- Review data collected and stored via forms
- Review cookies and add to privacy policy
- Link every page to a privacy policy
- Add a cookie disclaimer to every page
- Review privacy of technologies used

### Requires server/platform/infrastructure changes
- Specify a Content Security Policy for all pages
- Use Strict Transport Security for all pages
- Ensure cookies are only sent over SSL
- Review all network requests
- Ensure pages appear to load quickly

### Requires PDF source document remediation
- Ensure PDFs have a title
- Tag all PDFs
- Specify headings for every PDF
- Ensure PDF content is in a meaningful sequence
- Check PDFs have sufficient text contrast
- Ensure PDFs are machine readable
- Ensure PDFs specify a default language

### Requires per-page/manual UX review
- Ensure HTML is in a meaningful sequence
- Ensure users can pause or hide animated content
- Ensure pages with interruptions can be postponed/suppressed
- Ensure pages with inactivity time limits do not cause data loss
- Ensure users can control the visual presentation of text
- Ensure there are multiple ways to access a page

## Files changed

- `functions.php`
- `assets/css/app.css`
- `template-parts/flexi/runners_map.php`
- `acf-fields/partials/blocks/acf_runners_map.php`
- `template-parts/flexi/group_map_section.php`
- `acf-fields/partials/blocks/acf_group_map_section.php`
- `inc/woocommerce.php`

## Recommended next run

1. Re-run Silktide scan.
2. Export updated check results.
3. Compare against this report and move any improved checks into "fixed in code".
4. Triage remaining checks into:
   - content team
   - legal/compliance
   - infrastructure/devops
   - theme/page-template follow-up
