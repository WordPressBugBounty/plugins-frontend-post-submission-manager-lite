# FPSM privacy-safe activation measurement specification

Status: review-ready specification only; no instrumentation implemented.
Date: 2026-09-03. Initial implementation target: Lite; shared event vocabulary may later be adapted to Pro after separate source review.
Source baseline: Lite main eeb5cff9d440e4effe8489a2f53490c63e5d1e9e (1.3.4).
Owner: agent. No merge, release, publication, paid service or remote telemetry is authorized by this document.

## 1. Scope and decision

Prepare a small, local-only measurement module in a future, separately approved implementation package. Its purpose is to answer whether setup reached a verified first successful submission on an opted-in installation. It does not deliver global free-to-paid conversion reporting.

Proposed conservative default: local measurement OFF until a site administrator enables it. This is a product proposal for review, not a claim that local aggregation is legally required to be opt-in. Do not treat prior example counters as approved code or consent.

No collection before enablement, no historical backfill, no personal event log, no telemetry endpoint, remote cron upload, tracking pixel, SDK or external asset. Existing forms must work identically with measurement disabled, unavailable or failing. Existing functional dismissal metadata remains separate and unchanged.

The current package changes documentation only. It does not add a settings screen, change CTA behavior or alter canonical Figma. The existing Lite desktop was inspected as context; future settings and contextual-prompt desktop/mobile states require their own design package before implementation.

## 2. Source-grounded event contract

Paths below are relative to the pinned Lite baseline. Names describe the proposed contract, not existing instrumentation.

| Event | Evidence and precise trigger | Count rule / exclusions |
| --- | --- | --- |
| measurement_started | Successful administrator enable action after plugin is already active | Once per measurement epoch; NOT a fresh-install or activation count |
| quick_start_seen | Visible Quick Start on Forms screen; future scoped script confirms >=50% visibility for >=1 continuous second while document is visible | Site milestone once per epoch; hidden/background/rerender does not count |
| quick_start_dismissed | class-fpsml-admin.php::dismiss_first_success(), after capability/nonce checks and confirmed persistence | Site milestone once per epoch; failed save or invalid nonce never counts; do not reset existing per-user dismissal |
| form_configured | class-fpsml-ajax-admin.php::process_form_edit(), after validated save and verified DB outcome | Site milestone once per epoch; DB false or nonexistent target is failure. Zero affected rows requires read-back equality of intended persisted fields before accepting unchanged save |
| shortcode_published | FUTURE dedicated server-side page/post save observer; saved published content contains a valid fpsm shortcode alias resolving to an enabled form | Site milestone once per epoch; exclude autosaves, revisions, drafts and previews. Process current saved content transiently, store no content/alias/ID. Does not prove frontend rendering |
| first_submission_succeeded | includes/cores/ajax-process-form.php, existing fpsml_form_submission_success hook; action === insert and a positive persisted post ID | Site milestone once per epoch, new accepted posts only; update/delete/upload/captcha failures excluded. Draft/pending accepted posts count, publication/payment completion not implied |
| pro_cta_seen | Future scoped visible-element detector on the actual Lite upgrade CTA | Site milestone once per epoch, using same visibility rule as guide |
| pro_cta_clicked | Intentional activation of the same CTA after its visible state is observed, including keyboard activation | Site milestone once per epoch. Not purchase, checkout or revenue. Normal navigation must not wait for recording |

Source details:
- Guide rendering: includes/classes/admin/class-fpsml-admin.php::render_first_success_panel and includes/views/backend/first-success.php. Eligibility is manage_options and existing per-user dismissal state.
- The current form save ignores the return value of $wpdb->update and unconditionally sets response status 200 on that branch. Do not wire measurement to that response alone. Correct outcome handling/read-back needs explicit inclusion in the later implementation PR and failure-injection coverage.
- Submission entry point: includes/classes/class-fpsml-ajax.php::ajax_form_process includes includes/cores/ajax-process-form.php. Its before-process action is NOT success. The success hook provides action insert/update; inspect transient arguments, never persist their identifiers or payload.
- includes/classes/class-fpsml-shortcode.php::output_form_shortcode is rendering, not proof of publishing. There is no verified publication observer in this scope. Shortcode selection/copy must never stand in for publication. If the observer is deferred, report this event as unavailable, not false/zero.
- includes/classes/admin/class-fpsml-activation.php::update_install_date stores an existing install date, not a measurement cohort. Do not repurpose it or count reactivation as a new install.
- includes/views/backend/upgrade-banner.php uses FPSML_UPGRADE_LINK with noopener noreferrer. Keep it a normal link. Pro CTA events describe interest only.
- No shortcode-copy feature or generic client-controlled success endpoint is assumed.

## 3. Payload and storage contract

One non-autoloaded site option, proposed name fpsml_measurement_v1. This is local state, NOT a wire payload. No per-user measurement metadata is needed because the unit is the site/epoch. Each multisite blog uses its own option; no network rollup or automatic network-wide enrollment.

Example initial enabled state (synthetic):
```json
{
  "schema_version": 1,
  "enabled": true,
  "epoch_token": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
  "started_on": "2026-09-03",
  "expires_on": "2026-12-02",
  "cohort": "unknown",
  "milestones": {
    "measurement_started": true,
    "quick_start_seen": false,
    "quick_start_dismissed": false,
    "form_configured": false,
    "shortcode_published": null,
    "first_submission_succeeded": false,
    "pro_cta_seen": false,
    "pro_cta_clicked": false
  }
}
```

All object keys are closed allowlists; no additional properties. schema_version is integer 1. enabled and supported milestones are booleans. epoch_token is exactly 64 lowercase hexadecimal characters generated from 32 cryptographically secure random bytes at every enable action; the value above is synthetic documentation data, not a reusable token. It is synchronization state, not an analytics dimension: never include it in reports, exports, logs, URLs, campaign parameters or remote requests. null means an event detector is not implemented/available; never coerce it to false in reports. Dates use UTC calendar dates and exist only for retention, not individual-event timing. cohort enum is fresh_verified, existing, unknown. Default unknown: seeded forms or no recorded first submission do not prove a fresh site. Use fresh_verified only in a controlled fresh fixture or after a future approved, auditable install-classification design. Do not infer historical usage from content scans.

No IDs, names, email, site URL/hash, IP, cookies, licenses, titles, content, filenames, referrers, query strings, exact event timestamps or free text. WordPress already stores operational content/IDs; this module must not duplicate them. Local coarse retention dates are the explicit time-data exception.

Missing option means off. Disabled minimal state is {"schema_version":1,"enabled":false}; no epoch token or milestone history. Every successful Enable action initializes a new epoch with a fresh token, including disable/reset followed by re-enable on the same UTC date. Version upgrades and reactivation do not reset an existing epoch. Reset deletes measurement history and the token, then turns measurement off; an explicit enable starts anew. Disable does the same. Do not change forms, settings, functional dismissal flags or existing install dates.

At expiry, stop recording immediately and clear history on the next plugin request or scheduled cleanup opportunity; do not auto-reenable. A dormant/offline site's physical deletion cannot be guaranteed at exactly 90 days: disclose lazy cleanup, and enforce logical expiry before all reads/writes. Uninstall removes only this module's option, regardless of unrelated content-retention settings, and must not delete forms. No retroactive collection after expiry.

Implementation safeguards: non-autoloaded creation; strict schema validation; prepared compare-and-swap update with bounded retries and option-cache invalidation, so concurrent milestones do not overwrite each other. Every server and client event writer must carry the epoch token it observed, re-read the option, and atomically commit only when enabled is true, logical expiry has not passed and the stored token still matches. A retry must re-read state and abort—not initialize or re-create—when the option is missing, disabled, expired or has a different token. This generation fence makes same-day reset/disable/re-enable states distinct and prevents delayed previous-epoch writes from crossing epochs. A reset/disable must win over an in-flight event. Fail closed on unknown schema or malformed values. Storage errors are non-blocking and do not alter form responses or navigation. Treat this as an implementation acceptance requirement, not an already-tested guarantee.

## 4. Metrics without denominator errors

A site milestone is boolean, not an administrator count, action total or global install statistic. Several administrators on one site still contribute one site/epoch. No ratios may mix site milestones with per-admin impressions or repeated clicks.

For a controlled, fixed cohort with comparable observation windows, define:
- Activation completion = sites with first_submission_succeeded / eligible opted-in sites whose observation window completed.
- Guide-to-submission = sites with both quick_start_seen and first_submission_succeeded / sites with quick_start_seen, with identical eligibility/window.
- CTA interest = sites with pro_cta_seen AND pro_cta_clicked / sites with pro_cta_seen in that same cohort/window.
- Dismissal share = sites with quick_start_seen AND quick_start_dismissed / sites with quick_start_seen.

Use N/A when denominator is zero or detector unavailable. These are descriptive cohort shares, not proof that the guide caused the outcome or that dismissal preceded success. Unordered booleans cannot reconstruct event order. Do not label first observed success as first-ever success on existing/unknown installations.

A single site's local option cannot produce global rates. Do not silently export or add identifiers to combine installations. A controlled agent-run fixture set may verify the formulas without being marketed as customer conversion evidence. Opt-in customer data, if separately authorized in future, is selection-biased and must not represent all installs.

The old per-admin 24-hour experiment definitions are unsupported by this minimised schema: no individual-event timestamps or administrator attribution exist. Revise/reapprove the metric before a customer experiment; do not fabricate a 24-hour baseline.

## 5. Consent, controls and CTA boundary

WordPress.org guideline 7 requires explicit authorized consent for external contact/tracking and clear disclosure. Privacy-by-design guidance supports minimization, transparency and limited retention. These sources do not establish that all local analytics are universally lawful by default.

Proposed local toggle wording: "Record setup milestones on this WordPress site. Nothing is sent to WP Shuffle. Data is removed when you disable or reset measurement, or expires after 90 days." Explain lazy cleanup for inactive sites nearby. State that all plugin features remain available with measurement off.

Future controls: Enable, Disable and Reset, restricted to manage_options, distinct action-specific nonces, allowlisted inputs, translated strings and context-appropriate escaping. Every browser-originated measurement request—including guide visibility and Pro CTA visibility/click signals—must require an authenticated user with current_user_can( "manage_options" ), its event-specific nonce, a fixed allowed event, and the current epoch token; the server must also verify enabled/current-epoch/unexpired state immediately before committing. Nonce validation is CSRF protection, not authentication or authorization, and must never replace the capability check. Prefer fixed event handlers or a closed server-side map over a generic arbitrary-event endpoint. Do not persist the epoch token in browser storage; expose it only to the authorized scoped admin screen for the current request lifecycle. Never accept a client assertion of configuration, publication or submission success. quick_start_dismissed is recorded server-side only after update_user_meta confirms the functional dismissal was persisted; the browser must not assert that milestone. Server recording of real guest submissions does not require a guest to become an administrator.

Future control design must cover off/empty, enabled, disabled, expired, reset confirmation, persistence error, loading and keyboard-focus states at desktop/mobile widths. Reset is destructive only to measurement and must say so. No modal on activation, bundled consent, remote opt-in checkbox, or disabled core feature.

Remote telemetry is excluded. If later proposed, it needs separate product approval, site-owner opt-in, endpoint/security/retention review and precise disclosure. Do not call remote aggregates anonymous merely because IDs are absent: receiver access logs can expose IPs.

Campaign parameters, if separately implemented, must be fixed enums only (for example utm_source=fpsm-lite, utm_medium=plugin, utm_campaign=activation, utm_content=forms-paypal). No site/user/license identifiers, prefetch or automatic external request. Preserve noreferrer. A clicked website still receives a normal network request and its privacy policy applies; this is not a promise of anonymous browsing. No CTA URL changes in this PR.

Sources checked 2026-09-03:
- https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#7-plugins-may-not-track-users-without-their-consent
- https://developer.wordpress.org/plugins/privacy/
- https://developer.wordpress.org/reference/functions/add_option/
- https://developer.wordpress.org/apis/security/nonces/

## 6. Baseline and experiment plan

Current business baseline: unavailable, not zero. Local milestones are not yet implemented. Do not populate KPI Log conversion fields from test fixtures or WordPress.org active-install buckets.

E006 remains Planned. Proposed revised outcome is verified first accepted submission per eligible site within a common observation window; retain the prior 24-hour/admin metric only if a different explicitly approved schema supports it. Before Running: approve implementation scope and metric, pass the acceptance matrix, define eligibility and observation period, document baseline collection and privacy permissions. Agent owns all work.

E008 (measurement quality): in a disposable fixture set, each supported event state must match a predeclared oracle across repeat actions, failed writes, multi-admin use and reset/concurrency scenarios. Primary metric: correctly classified fixture assertions / all assertions. Target 100%; any false success, cross-site contamination or network leak blocks implementation approval. This is a technical experiment, not sales lift.

External reporting stays separate: website campaign visits/clicks require authorized analytics access; purchases/refunds need trusted sales records. WordPress.org downloads include updates and cannot be equated with new installs. No causal revenue claim from launch or local milestones.

## 7. Acceptance matrix for later implementation (all unrun)

| ID | Agent-run scenario and required result |
| --- | --- |
| M01 | Fresh activation and existing upgrade: option absent/off; no measurement writes/network requests |
| M02 | Admin enables: schema valid, non-autoloaded option, dates exactly 90 UTC calendar days apart |
| M03 | Every browser-event route plus enable/reset/disable: subscriber/unauthenticated caller, missing/invalid event-specific nonce, unknown event or wrong/missing epoch token is rejected; state unchanged |
| M04 | Hidden guide, background tab or <1 second exposure: unseen; visible qualifying guide: seen |
| M05 | Reload and second administrator: site milestone remains one; existing dismissal semantics unchanged |
| M06 | Dismiss valid / persistence failure: only the server handler after confirmed update_user_meta success sets the milestone; a forged browser dismissal event cannot set it |
| M07 | Form save changed / unchanged / nonexistent / DB false: count only verified persisted outcome |
| M08 | Published valid shortcode / draft / revision / invalid alias / copy only: only published valid case counts |
| M09 | Accepted new draft/pending post vs edit/delete/captcha failure: only successful insert counts |
| M10 | CTA keyboard/mouse activation: manage_options, event-specific nonce, fixed event and current-epoch checks enforced; interest state correct and navigation unaffected by failed recording |
| M11 | Unknown event/key, forged success, oversized payload and malformed storage: reject safely |
| M12 | Concurrent milestones are retained. In same-day reset, disable and re-enable races, each Enable produces a different token; delayed requests and CAS retries carrying an old token are rejected and cannot resurrect or modify the new epoch |
| M13 | Reset/disable/expiry/reactivation: no backfill or auto-reenable; forms and dismissal flags preserved |
| M14 | Multisite blog A/B: independent enablement and state; network activation does not opt in sites |
| M15 | Database, request, browser-storage, log and report inspection: epoch token remains local synchronization state and no prohibited fields, per-user telemetry or outgoing measurement traffic exists |
| M16 | Keyboard/focus, 320/768/1440px and 200% zoom: future controls usable; capture sanitized evidence |
| M17 | PHP 7.4/8.3 syntax and runtime, clean plugin-origin WP_DEBUG/console; document minimum PHP 7.0 compatibility separately |
| M18 | Upgrade/rollback/uninstall: existing forms/options/hooks/templates unaffected; module data removal scoped |
| M19 | Cohort oracle: N/A for zero/unknown denominators; repeated admins do not inflate site shares |

Use an approved disposable environment and local tools; do not dispatch Actions or buy credits. The repo's PHP/browser workflows are manual with an included-minutes confirmation input. Paid execution is not approved.

## 8. Delivery, remaining decisions and rollback

This specification is complete for review, not implemented or released. No production UI screenshots or new Figma states are claimed. Existing Lite/Pro onboarding remains completed; no release gate is reopened.

Next bounded implementation candidate: local-only opt-in controls plus first accepted-submission site milestone, with M01–M03, M09, M11–M19 coverage. Full funnel detection is separate scope. Before code, approve that bounded scope and create exact control designs. Pro adoption and remote collection remain separate.

Documentation rollback: close this draft PR or revert its single documentation commit; no data migration required. Future instrumentation rollback must stop event recording without changing stored forms or published shortcodes.

Validation performed for this document: source-path/symbol checks, event/schema completeness, example JSON validity, 64-hex epoch-token contract, retention-date arithmetic, synthetic metric fixtures, same-day epoch-fencing model, client-endpoint authorization checklist and private-link scan; privacy/scope review. No PHP lint, WordPress runtime, browser interaction or acceptance-matrix pass is claimed for unimplemented instrumentation.
