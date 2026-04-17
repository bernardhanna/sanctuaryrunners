---
name: Multisite Conversion Rollout
overview: Convert the current single-site WordPress install into a subdirectory multisite network, clone Ireland into UK/Global, and validate theme/plugin behavior (especially matrix-donations) with a repeatable checklist.
todos:
  - id: preflight-backup-baseline
    content: Prepare backup and baseline smoke checks before multisite conversion
    status: pending
  - id: enable-subdirectory-network
    content: Enable WordPress multisite (subdirectories) and validate core routing
    status: pending
  - id: create-and-clone-sites
    content: Create UK/Global subsites and clone Ireland content/settings
    status: pending
  - id: configure-theme-options-per-site
    content: Set per-site ACF options, menus, and country switch links
    status: pending
  - id: validate-plugin-compatibility
    content: Verify matrix-donations/forms behavior and per-site external integrations
    status: pending
  - id: domain-mapping-and-final-qa
    content: Plan optional custom domain mapping and complete accessibility + functional QA
    status: pending
isProject: false
---

# Multisite Conversion and 3-Site Duplication Plan

## Scope and Assumptions

- Network type: **subdirectories** (`/ie`, `/uk`, `/global`).
- Initial content strategy: **clone current Ireland site to both new sites**, then localize.
- Future possibility: assign different full domains to each subsite later (domain mapping step included).

## Phase 1: Preflight and Safety

- Full database + files backup (before any network changes).
- Confirm production-like environment supports multisite rewrites.
- Freeze plugin/theme updates during conversion window.
- Capture baseline smoke checks: homepage, forms, donations, navigation, search, media.

## Phase 2: Enable WordPress Multisite Core

- Update `wp-config.php` and `wp-admin` network installer steps (no theme/plugin code edits required initially).
- Enable subdirectory network and regenerate `.htaccess` rewrite rules.
- Re-login and verify Network Admin appears.
- Ensure current site remains functional as primary (Ireland).

## Phase 3: Create and Clone Subsites

- Create 2 subsites: UK, Global.
- Clone Ireland content/settings into each site (posts/pages/menus/widgets/options/media references) using a controlled migration method.
- Re-save permalinks per site.
- Rebuild menus per site where IDs changed.
- Remove or replace Irish-only references in copied content (place names, contact details, CTAs, legal copy, internal links).
- Clean up Events and Blog content so UK/Global do not unintentionally reuse Irish posts.

## Phase 4: Theme/UX Multisite Configuration

- Configure per-site ACF Options values (logos, nav buttons, legal/footer links, contact settings, country-specific text).
- Configure country switch links in `template-parts/header/navbar/language-dropdown.php` via options so each country points to the right subsite URL.
- Ensure each site uses the correct localized contact form block setup (country mode, recipient routing, autoresponder copy, consent text).
- Validate hero/nav breakpoint behavior and shared tokenized styling still match across all 3 sites.

## Phase 5: Plugin Compatibility and Hard Checks

- Audit activation mode (network vs per-site) for critical plugins, especially `matrix-donations`.
- For `matrix-donations`, verify per-site options + DB table bootstrap behavior in:
  - `wp-content/plugins/matrix-donations/matrix-donations.php`
  - `wp-content/plugins/matrix-donations/includes/class-settings.php`
- Confirm each subsite has correct donation pages, Stripe keys/webhook endpoint, Salesforce settings, and email recipients.
- Verify forms, reCAPTCHA/Turnstile, email/autoresponder behavior per site.

## Phase 6: URL and Domain Strategy

- Keep subdirectory paths for launch (`/ie`, `/uk`, `/global`).
- If needed later, map custom domains per subsite in Network Admin while preserving path-based fallback.
- Re-test canonical URLs, redirects, sitemaps, robots, and analytics tags after mapping.

## Phase 7: QA, Accessibility, and Go-Live Checklist

- Functional QA on each subsite: nav, search, forms, stories, blog, CPTs, donation journey.
- Content QA pass focused on de-Irish-izing cloned pages where needed (titles, body copy, categories, event metadata, media captions, links).
- Accessibility regression pass (focus states, keyboard nav, contrast, form labels/errors).
- Performance/cache and CDN checks per subsite.
- Final sign-off checklist and rollback path.

## Deliverables

- A repeatable runbook for converting and cloning future country sites quickly.
- A per-site configuration matrix (what must differ vs remain shared).
- A multisite plugin-compatibility checklist focused on donations and forms.

## Content Localization Checklist

Use this during post-clone cleanup for UK and Global sites.

| Area | What to Localize | UK Status | Global Status | Owner |
|---|---|---|---|---|
| Site Identity | Site title/tagline, logo alt text, favicon, social sharing defaults | TODO | TODO |  |
| Navigation | Header/footer menu labels, country switch links, CTA links | TODO | TODO |  |
| Contact Forms | Country mode, recipient emails, autoresponder text, consent copy | TODO | TODO |  |
| Contact Details | Addresses, phone numbers, support emails, office/location references | TODO | TODO |  |
| Blog | Remove Irish-only categories/posts, curate local stories, archive descriptions | TODO | TODO |  |
| Events | Remove Irish event carryover, update event links/dates/locations | TODO | TODO |  |
| Running Groups | Map markers/content and links aligned to each site’s geography | TODO | TODO |  |
| SEO Metadata | Page titles, meta descriptions, OG images, canonical checks | TODO | TODO |  |
| Legal Pages | Privacy/terms/cookie text and regional legal references | TODO | TODO |  |
| Donations | Stripe/Salesforce settings, webhook URLs, donation page copy | TODO | TODO |  |
| Media Library | Replace Irish-specific images/captions where inappropriate | TODO | TODO |  |
| Internal Links | Remove `/ie` hardcoded links and Irish-specific URL references | TODO | TODO |  |
