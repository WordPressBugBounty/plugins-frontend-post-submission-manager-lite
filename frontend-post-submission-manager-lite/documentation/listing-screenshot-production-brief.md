# Lite listing screenshot production brief

Status: review-ready storyboard; not a published asset package.
Updated: 2026-09-02. Target product: FPSM Lite 1.3.4.

## Publication boundary

The `readme.txt` Screenshots section deliberately retains the existing 12 captions from `main`. The repository's existing screenshot images are unchanged. The proposed captions below are staging material only.

Do not paste these six captions into the publishable readme until the corresponding six installed-plugin captures have been prepared and reviewed. Replace the numbered assets and captions together, accounting for obsolete screenshot-7 through screenshot-12 files. Any merge and any WordPress.org publication require separate explicit approval.

The canonical private design file contains six 1544 x 1000 storyboard frames. They are illustrative capture plans, not installed-plugin evidence. Do not publish the storyboards themselves as product screenshots or expose private design/tracker links.

## Proposed captions

== Screenshots ==

1. Launch your first frontend submission form with the four-step Quick Start guide and ready-to-use forms.
2. Configure fields, post status, notifications, and workflow settings for a seeded form.
3. Publish the form on a WordPress page with its generated shortcode.
4. Accept guest or logged-in submissions with a pre-designed frontend template.
5. Protect submissions with Google reCAPTCHA or Cloudflare Turnstile.
6. Compare Lite's included workflow with advanced Pro capabilities, including PayPal payments.

## Capture plan

| Image | Installed surface | Capture requirements |
| --- | --- | --- |
| 1 | Forms screen with Quick Start | Show all four step labels, centered badges, Configure Guest Post Form, setup help and dismissal. Include seeded form rows when the crop permits. |
| 2 | Guest Post Form editor | Capture actual Basic settings for configured post status; use a separate inset or actual Form settings capture for required fields. Do not invent a combined settings view. |
| 3 | WordPress page editor | Show the actual Shortcode block containing `[fpsm alias="guest_post_form"]` and the editor's publish control. Do not substitute FPSM navigation for the page editor chrome. |
| 4 | Published frontend form | Use a logged-out guest session and Template 1. No WordPress admin sidebar or toolbar. Use neutral test content and no customer data. |
| 5 | Security settings | Capture the implemented Captcha Provider dropdown, with Google reCAPTCHA and Cloudflare Turnstile as supported choices. Leave key fields empty or securely redact values; never publish credentials. |
| 6 | Lite Upgrade to Pro screen | Capture the actual upgrade UI. Clearly label PayPal payments and other advanced workflow capabilities as Pro-only. No unsupported price, urgency or revenue claims. |

## Corrected storyboard review

- Frame 1 now includes all step labels, shortcode, primary/help actions and dismissal.
- Frame 4 no longer shows administrator navigation.
- Frame 5 uses a provider dropdown and empty credential fields, not radio controls.
- Other frames remain illustrative capture directions; actual installed UI must be used for publication.
- Existing brand colors and Urbanist typography remain in the storyboard.

## Agent-owned production checklist

1. Prepare a clean disposable Lite 1.3.4 site with seeded forms; no production data.
2. Capture the six surfaces above at high resolution with consistent framing.
3. Check every caption against its numbered image, including frontend versus admin context.
4. Inspect for identifiers, credentials, unsupported Lite claims, clipped text and unreadable scaling.
5. Export optimized PNG assets and preview the full numbered sequence.
6. Prepare the asset/caption change together for review. Do not dispatch the publication workflow.
7. After explicit publication approval, verify the live image/caption pairing.

## Validation and scope

Completed on 2026-09-02: source review of Quick Start and captcha controls; visual inspection of corrected storyboard frames; readme caption restoration.

The listing branch changes documentation only. Plugin PHP, JavaScript, CSS, forms, options, shortcodes, stored data, hooks, template paths, stable tag and release version are unchanged.

PHP lint and WordPress/Playwright runtime checks were not rerun for these documentation-only corrections. No runtime-pass claim is made. No GitHub Actions workflow was intentionally dispatched.

Rollback: revert the documentation commits on this branch. No data migration or product rollback is required.
