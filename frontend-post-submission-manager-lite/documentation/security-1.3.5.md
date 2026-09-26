# Lite 1.3.5 security patch

Addresses Wordfence vendor report `0af8ff06-68e2-4a0c-9de4-d06a6a2e86c9`
(CVE-2026-96649). This document is patch-review material, not a release or
confirmation of Wordfence approval.

## Changes

- Uploader upload/drag/cancel/failure labels are escaped as text before insertion
  into the HTML templates. A single replacement pass preserves literal template
  tokens and JavaScript replacement sequences in labels.
- Initialization is scoped to `form.fpsml-front-form` and uses the current DOM
  element, avoiding duplicate-ID retargeting. This is defense in depth, not a
  trust boundary: forged wrappers still receive safely rendered labels.
- Upload-limit error messages use text rendering.
- The shared HTML sanitizer rejects class attributes containing the uploader
  marker, including entity-encoded tokens. Other attributes and ordinary rich
  text remain intact. Existing KSES class checks and filter callbacks are retained.
- Versioned frontend assets advance to 1.3.5. Existing stored content is protected
  by the JavaScript fix; no destructive content migration is required. Purge any
  CDN/page cache that continues to serve the previous asset URLs during rollout.

Custom uploader labels containing HTML now display that markup literally. AJAX
contracts, permissions, form serialization, and shortcode names are unchanged.

## Regression checks

Use the repository's existing Playwright/WordPress Playground dependencies:

```sh
npm install --no-package-lock
npx playwright install chromium
npm run test:e2e
FPSML_TEST_WP=6.0 npm run test:e2e -- tests/e2e/uploader-security.spec.ts
```

Set `FPSML_BROWSER_CHANNEL=chrome` to use installed Chrome. Set `FPSML_TEST_PORT`
when running independent Playground instances concurrently. Tests isolate guest
and administrator sessions and use disposable WordPress instances, not the local
MAMP database. WordPress 6.0 uses PHP 7.4; the default uses PHP 8.3.

Coverage includes malicious labels, literal replacement tokens, forged wrappers,
duplicate IDs, upload-limit messages, stored legacy payloads, guest submission,
administrator pending preview, authorized create/edit, multiple forms, featured
image upload, and KSES preservation/rejection cases. The existing first-success
release gate also runs. Its Playground mount/disposal calls were updated for the
current dependency API; its flex assertion accepts CSS blockification.

## Verification evidence

- Baseline commit `2df606a` (Lite 1.3.4), mounted into an isolated Playground:
  an anonymous AJAX submission stored the entity-encoded label; visiting the
  resulting published post executed a harmless `window.fpsmlXss` marker.
- Patched current WordPress/PHP 8.3: six browser tests passed, including the
  existing release gate and five security/functional regressions.
- Patched WordPress 6.0/PHP 7.4: all five security/functional tests passed.
- All 81 plugin PHP files passed PHP 8.4.7 lint. The changed sanitizer also
  passed PHP 7.1.12 lint. Both changed JavaScript files passed `node --check`,
  and the diff passed `git diff --check`.
- A PHP 7.0 runtime was not available for an exact-version execution test.
  The patch does not add syntax or APIs newer than the advertised minimum.

No production data was used or modified. The patch has not been published or
submitted to Wordfence.

## Vendor response draft

We have prepared a Lite 1.3.5 patch for CVE-2026-96649. The patch escapes all
uploader text tokens at the HTML rendering boundary, scopes uploader initialization
to frontend forms, safely renders upload-limit messages, and prevents submitted
HTML from retaining the uploader marker class. The sink fix also protects
previously stored payloads without requiring posts to be rewritten. Please review
the attached patch and regression results before marking the report resolved.

Attach the reviewed diff and completed test results when submitting. Publishing
and the vendor response are separate from the implementation work.
