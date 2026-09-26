# FPSM Lite local measurement smoke test

Status: partial full-plugin evidence from disposable WordPress Playground runs on 2026-09-04 and 2026-09-05. The PR branch was overlaid on the published Lite package and remained active on WordPress 7.1/PHP 8.3.33 and WordPress 6.8.8/PHP 7.4.33. PHP 7.0, fresh-install packaging, multisite, uninstall and responsive browser checks remain unrun.

| ID | Scenario | Expected result | Status |
| --- | --- | --- | --- |
| LM-01 | Fresh install and upgrade | Measurement is off; no option or external request | Partial — default-off module state passed; full-plugin fresh/upgrade paths unrun |
| LM-02 | Administrator enables | Non-autoloaded valid state, fresh 64-hex epoch token, 90-day expiry | Passed |
| LM-03 | Subscriber, unauthenticated and invalid-nonce controls | Rejected; state unchanged | Partial — subscriber UI/action and invalid nonce passed; unauthenticated request unrun |
| LM-04 | Successful new frontend submission | `first_submission_succeeded` becomes true after a persisted insert | Passed — a real `guest_post_form` frontend submission created draft post 6 and changed the enabled epoch milestone from false to true |
| LM-05 | Edit, validation failure and failed insert | Milestone remains unchanged | Partial — update and nonexistent post were ignored; real validation/insert failures unrun |
| LM-06 | Repeated and concurrent successful inserts | Boolean remains true; state remains valid | Partial — repeated success hooks remained boolean true and schema-valid; true concurrent inserts remain unrun |
| LM-07 | Disable/reset racing an event write | Old epoch cannot be recreated or modify a new epoch | Partial — post-reset hook could not recreate state; a stale serialized compare-and-swap affected zero rows and preserved the new epoch; true concurrent race remains unrun |
| LM-08 | Same-day disable/reset and re-enable | New token differs; delayed old-token retry is rejected | Unrun |
| LM-09 | Malformed option and unknown milestone | Fail closed without changing form behavior | Passed on PHP 8.3 — array dates, impossible dates, reversed dates and an unknown milestone key all returned the off state with no warning or fatal; a valid state remained enabled |
| LM-10 | Disabled and expired state | No recording; lazy expiry removes measurement state | Passed |
| LM-11 | Multisite sites A/B | Independent opt-in and state | Unrun |
| LM-12 | Uninstall | Removes only `fpsml_measurement_v1`; forms remain | Passed single-site — uninstall removed the measurement option while preserving the FPSML form-row count and WordPress post count; multisite uninstall remains covered by LM-11 |
| LM-13 | Settings desktop/mobile/200% zoom | Controls usable; no horizontal overflow caused by the card | Unrun |
| LM-14 | Keyboard and screen reader labels | Logical focus, visible focus, expanded state and status announcements | Partial — reset disclosure, focus transfer/return and status semantics passed; full traversal unrun |
| LM-15 | `WP_DEBUG` and browser console | No plugin-origin warning, notice, fatal or console error | Passed on the PHP 7.4 full-plugin settings run with `WP_DEBUG` and `WP_DEBUG_LOG` enabled: no debug log was created and no plugin-origin console warning/error appeared; earlier intentionally failing pre-fix harness errors are excluded from the corrected PHP 8.3 result |
| LM-16 | Network inspection | No measurement endpoint, pixel, SDK, cron upload or automatic external request | Passed by static source scan; runtime network capture unrun |

No GitHub Actions were dispatched. The browser console contained only a browser-extension metadata error, excluded because its URL was `chrome-extension://`; there were no WordPress or FPSML page errors.

Remaining release-gate run: install the complete branch build, then execute LM-01, remaining LM-03–LM-09 coverage, LM-11–LM-13, full LM-14–LM-16, and the PHP 7.4 leg. Preserve screenshots/logs outside the public repository when they may reveal private URLs or data.

Review correction (2026-09-05): malformed date values are rejected before `preg_match()`, impossible calendar dates fail `checkdate()`, and reversed date ranges fail closed. Six PHP 8.3 state cases passed. The allowlisted, action-specific retry buttons for enable, disable and reset rendered and completed successfully through real WordPress admin-post nonce flows. PHP 7.4 full-plugin loading and malformed-state rejection passed on WordPress 6.8.8. WordPress Playground did not provide an active client for PHP 7.0, so that declared-minimum compatibility leg remains unrun.

Additional evidence (2026-09-05): repeated milestone writes stayed idempotent; a stale compare-and-swap could not overwrite a fresh epoch; the single-site uninstall path removed only the measurement option and preserved forms/posts. The PHP 7.4 debug-enabled desktop settings page rendered at a 1348px viewport with equal card client/scroll widths (758px), indicating no card-level desktop overflow. Mobile, tablet and 200% scaling remain unrun.
