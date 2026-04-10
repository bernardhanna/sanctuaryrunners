# Silktide Accessibility Report (Updated Checklist)

Date: 2026-04-10  
Source: `tests/silktide/checks (2).csv` (74 checks)

## Obvious fixes applied for this checklist

1. Global ID de-duplication and label-target repair
   - Detects duplicate DOM `id` values.
   - Reassigns duplicate IDs and updates related `label[for]` references.
   - Targets:
     - Fix duplicate IDs
     - Labels must have a unique for attribute
     - Fix missing ARIA label IDs
     - Ensure labels point to valid IDs

2. Missing iframe title fallback
   - Adds a fallback `title` for any iframe missing one.
   - Targets:
     - Check that each frame has an appropriate title

3. Programmatic field-purpose hints
   - Auto-adds `autocomplete` hints for common field names (`first_name`, `email`, `phone`, `postcode`, etc.) when missing.
   - Targets:
     - Identify the purpose of fields programmatically

4. Previously applied baseline fixes still included
   - Auto-wiring labels to controls when obvious.
   - New-tab link labeling and rel hardening.
   - Keyboard focus visibility and selected-state visibility.
   - Existing skip-link behavior.

## Progress bucket summary

- `fixed_or_improved_in_theme`: 12
- `needs_theme_or_page_fix`: 37
- `manual_review`: 13
- `manual_pdf`: 8
- `needs_design_contrast_review`: 4

Detailed row-by-row tracking is in:
- `tests/silktide/checks-2-progress.csv`

## Notes

- This pass intentionally fixes only obvious/global accessibility issues.
- Remaining checks in `needs_theme_or_page_fix` likely require template-specific changes and content-specific decisions.
- Re-run Silktide next and use the delta to validate exactly which rows reduced.

## /category/media/ targeted pass

For `http://localhost:10101/category/media/`:

- Updated `template-parts/flexi/blog_listing.php` to improve obvious accessibility gaps:
  - stronger contrast for filter chips and search text/placeholder
  - explicit search input `id` + `label` pairing
  - keyboard focusability for clickable post cards (`tabindex`, `role="link"`, label)
  - removed mouse-drag-only chip slider behavior to avoid drag-and-drop dependency

- Expected impact:
  - Ensure form controls have labels
  - Ensure text has sufficient contrast (AA)
  - Ensure controls clearly indicate when selected
  - Ensure functionality is available to keyboard users

## /events/page/3 targeted pass

For `/events/page/3/`:

- Updated `archive.php` to route event archives to `template-parts/flexi/events_listing.php` (instead of the blog listing template).
- Updated `template-parts/flexi/events_listing.php` to improve obvious accessibility gaps:
  - stronger contrast for filter chips and search text/placeholder
  - explicit search input `id` + `label` pairing
  - keyboard focusability for clickable event cards (`tabindex`, `role="link"`, label)
  - removed mouse-drag-only chip slider behavior to avoid drag-and-drop dependency

- Expected impact:
  - Allow users to quickly skip to content (supported by shared header skip-link + archive now using intended events template)
  - Fix missing ARIA label IDs
  - Ensure form controls have labels
  - Ensure text has sufficient contrast (AA)
  - Ensure controls clearly indicate when selected
  - Ensure drag and drop movements have an accessible alternative

## Get involved targeted pass

For `get_involved` / `get_involved_form_001` templates:

- Updated `template-parts/flexi/get_involved.php`:
  - switched involvement grid wrapper to semantic list (`ul`/`li`)
  - improved icon semantics (decorative icon handling with empty `alt` + `aria-hidden`)
  - removed verbose window-target wording from button `aria-label`

- Updated `template-parts/flexi/get_involved_form_001.php`:
  - added explicit form instruction IDs and wired with `aria-describedby`
  - grouped existing-member radio controls in `fieldset` + `legend`
  - added explicit IDs and `for` labels for key consent checkboxes/radios
  - added renewal-form instruction copy for better field guidance

- Expected impact:
  - Ensure form controls have labels
  - Labels must have a unique for attribute
  - Wrap items with the same name inside a fieldset
  - Ensure lists are marked up correctly
  - Ensure instructions are provided for appropriate fields

## Paginated category/blog pass

For paginated archive/index views (category pages, blog index pages):

- Added explicit `<main id="main-content">` wrappers where missing:
  - `archive.php`
  - `home.php`
  - `index.php`
- Removed remaining drag-only cursor affordance from blog filter chip row:
  - `template-parts/flexi/blog_listing.php`

- Expected impact:
  - Allow users to quickly skip to content
  - Ensure drag and drop movements have an accessible alternative
  - Improve consistency for whole-page semantic structure on paginated templates


## HTML/ID issue test file follow-up

Source: `tests/silktide/html-and-id-issues (1).csv`

Applied fixes:
- Fixed footer heading ID mismatch in `template-parts/footer/footer.php` (`footer-col-2`).
- Replaced non-unique `aria-labelledby="form-heading"` usage in `template-parts/flexi/get_involved_form_001.php` with section-scoped IDs.

Notes:
- `question-1`/`question-3` entries from the test CSV were not found in current PHP templates, likely from previously rendered markup/state. Re-scan to confirm they are gone with current code.
