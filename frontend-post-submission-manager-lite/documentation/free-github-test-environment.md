# Free-plan GitHub test environment

Both repository workflows are deliberately manual-only. Pull requests and pushes do not start runners, so routine development cannot consume GitHub Actions minutes in the background.

## Run a check

1. Open the repository's **Actions** tab.
2. Choose **PHP syntax check** or **Browser smoke test**.
3. Select **Run workflow** and the feature branch.
4. Check **Confirm this run is within the included GitHub Actions quota** only after verifying that included minutes remain.

The browser job uses one Ubuntu runner, one Chromium browser, one Playwright worker, no version matrix, and a 15-minute hard timeout. Duplicate runs cancel automatically. Its screenshots and failure traces are retained for one day.

The PHP job uses two short Ubuntu jobs for PHP 7.4 and 8.3, each with a five-minute timeout. It is also manual-only.

## Cost guardrails

- Do not add `push`, `pull_request`, or scheduled triggers without an explicit budget decision.
- Do not add macOS or Windows runners, browser matrices, sharding, retries, or long artifact retention.
- Keep repository Actions spending disabled or capped at zero in the organization billing settings. Repository workflows cannot enforce the organization-level billing cap themselves.
- If included Actions minutes are exhausted, run `npm install`, `npx playwright install chromium`, and `npm run test:e2e` on a local machine instead of enabling paid usage.

These controls avoid accidental paid usage. A manual GitHub-hosted run still consumes included Actions minutes, so the confirmation checkbox is a release gate rather than a claim that minutes remain.
