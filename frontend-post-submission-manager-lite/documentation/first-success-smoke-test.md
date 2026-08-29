# FPSM Lite First Success smoke-test matrix

Run these checks on a disposable WordPress site with `WP_DEBUG` enabled. Record the WordPress, PHP, browser, and plugin versions with screenshots or video evidence.

| ID | Scenario | Expected result |
|---|---|---|
| FS-01 | Fresh activation as an administrator | Forms screen shows the four-step guide and seeded Guest Post Form CTA. |
| FS-02 | Existing installation with seeded forms | Existing forms and stored settings remain unchanged; guide appears once per administrator. |
| FS-03 | Installation without `guest_post_form` alias | Guide selects the oldest available form without a PHP warning. |
| FS-04 | Installation with no form rows | Guide renders the public Lite documentation fallback without a broken link. |
| FS-05 | Primary CTA | Opens the selected existing form editor. |
| FS-06 | Help CTA | Opens `https://wpshuffle.com/docs/frontend-post-submission-manager-lite/`. |
| FS-07 | Valid dismissal nonce | Guide is hidden only for the current administrator and remains hidden after refresh. |
| FS-08 | Invalid or missing dismissal nonce | Request is rejected and no user-meta dismissal is stored. |
| FS-09 | Subscriber dismissal attempt | Request is denied and no user-meta dismissal is stored. |
| FS-10 | Dismissal storage failure | Guide remains visible and displays the recoverable error state. |
| FS-11 | Keyboard-only navigation | Dismiss and both CTAs have visible focus; focus order follows the visual order. |
| FS-12 | Responsive and zoom | Layout remains usable at 320px, 768px, 1440px, and 200% browser zoom without horizontal clipping. |
| FS-13 | Existing form edit/save | Editing and saving the seeded form still works. |
| FS-14 | Shortcode submission | Published shortcode renders and a test submission reaches the configured post status. |
| FS-15 | Upgrade and rollback | Activating Pro or rolling Lite back preserves forms, options, user meta, hooks, shortcodes, and template paths. |
| FS-16 | Pro payment upgrade callout | Displays the verified PayPal feature, links to the existing Pro destination, remains responsive, and exposes visible keyboard focus. |

Do not approve merge or release until FS-01 through FS-16 pass or every unrun check has an explicit owner, reason, and release decision.
