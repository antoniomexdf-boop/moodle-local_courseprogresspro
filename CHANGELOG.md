# Changelog

All notable changes to `local_courseprogresspro` are documented in this file.

## [0.7.29] - 2026-03-16

### Fixed

- Recreated the AMD build files after deletion and replaced file-level PHP docblocks in `version.php` and `settings.php` with inline comments to match reviewer guidance.

## [0.7.28] - 2026-03-16

### Fixed

- Moved the privacy provider namespace declaration to the correct position before executable code and refreshed the AMD build pair for a new review upload.

## [0.7.27] - 2026-03-16

### Fixed

- Updated the AMD build artifact and added `amd/build/progress.min.js.map` so the distribution package includes the expected generated build pair.

## [0.7.26] - 2026-03-16

### Changed

- Replaced the modal card width strategy with `flex: 0 1 38rem` plus `max-width: 38rem` to preserve responsive sizing without relying on an explicit width declaration.

## [0.7.25] - 2026-03-15

### Changed

- Restored standard Moodle execution guards in `lib.php` and the privacy provider, and expanded the AMD build boilerplate to better align with plugin-directory review expectations.

## [0.7.24] - 2026-03-15

### Changed

- Expanded PHPDoc coverage in `lib.php` and normalized JSDoc return annotations in `amd/src/progress.js` to better match Moodle review expectations.

## [0.7.23] - 2026-03-15

### Changed

- Hardened the release package for Moodle Plugins review by restoring explicit version metadata markers in `version.php` and adding a Moodle boilerplate header to the AMD build artifact.

### Fixed

- Removed the explicit `width` declaration from the modal card to use the most conservative CSS form for plugin-directory validation.

## [0.7.21] - 2026-03-15

### Changed

- Published a fresh review iteration after reconfirming the AMD JSDoc coverage, reduced `buildContext()` complexity, explicit promise returns, and conservative CSS width handling.

## [0.7.20] - 2026-03-15

### Fixed

- Removed a duplicated `width: 100%` declaration introduced while consolidating the latest stylesheet fixes.

## [0.7.19] - 2026-03-15

### Fixed

- Unified the AMD source with the latest manual review improvements and restored the modal card `width: 100%` style needed for the reported stylelint issue.

## [0.7.18] - 2026-03-15

### Fixed

- Restored the full Moodle boilerplate metadata block in `settings.php`.
- Rebuilt `amd/build/progress.min.js` as a compact single-line artifact aligned with the current AMD source.

## [0.7.17] - 2026-03-15

### Fixed

- Completed the PHPDoc parameter list for `local_courseprogresspro_should_count_cm()` to satisfy Moodle PHPDoc checks.

## [0.7.16] - 2026-03-15

### Changed

- Applied the latest stylesheet revision for the progress widget and pending timeline UI.

## [0.7.15] - 2026-03-15

### Changed

- Reviewed and incorporated the latest manual adjustments in `progress.js`, `styles.css`, `version.php`, `settings.php`, and `lib.php`, then refreshed the release package metadata.

## [0.7.14] - 2026-03-15

### Changed

- Prepared a fresh review-ready package after confirming the AMD and CSS fixes that address the last reported `grunt` and `stylelint` issues.

## [0.7.13] - 2026-03-15

### Changed

- Normalized AMD JSDoc wording to a more canonical review-friendly style while keeping the previous code-quality fixes in place.

## [0.7.12] - 2026-03-15

### Fixed

- Replaced inline file-level docblocks in `version.php` and `settings.php` with simple comments, removed flagged `defined('MOODLE_INTERNAL') || die();` lines from `lib.php` and the privacy provider, and corrected callback spacing in `lib.php`.

## [0.7.11] - 2026-03-15

### Fixed

- Updated AMD JSDoc blocks to the expected `@returns` style and further reduced `buildContext()` complexity with small helper functions.
- Kept the promise chain explicit and synchronized the build artifact after the frontend code-quality pass.

## [0.7.10] - 2026-03-15

### Fixed

- Added JSDoc coverage to the AMD source functions, simplified `buildContext()`, removed nested ternary logic, and made the render promise chain return or throw explicitly.
- Replaced the modal card `width` shorthand with `width` plus `max-width` for broader CSS compatibility.

## [0.7.9] - 2026-03-15

### Changed

- Removed repository-only guidance documents from the distributable package to keep the submission focused on plugin code and essential end-user documentation.

## [0.7.8] - 2026-03-15

### Changed

- Removed remaining non-English distribution documentation and trimmed package documentation for Moodle review submission.

## [0.7.7] - 2026-03-15

### Changed

- Clarified in settings and documentation that course sections are never counted as progress units and are only used as navigation context in the pending timeline.

## [0.7.6] - 2026-03-14

### Fixed

- Updated remaining source headers to align with Moodle plugin review expectations.
- Removed the bundled Spanish language pack from the distributable package so releases ship with `lang/en` only.

## [0.7.5] - 2026-03-14

### Fixed

- Removed hard-coded user-facing fallback text from the AMD renderer so visible UI labels now depend on Moodle language strings passed from PHP.
- Kept the JS build artifact synchronized after the string API compliance update.

## [0.7.4] - 2026-03-14

### Fixed

- Added explicit Moodle GPL header metadata to remaining source files that were still missing `@copyright` and `@license` markers.

## [0.7.3] - 2026-03-14

### Changed

- Moved the pending summary text into the pending-actions button to simplify the student-facing layout.
- Kept the pending-actions modal behavior unchanged while making the button copy more descriptive.

## [0.7.2] - 2026-03-14

### Changed

- Removed the completed-actions line from the student widget to avoid mixing Moodle official progress counts with the plugin pending timeline counts.
- Removed the related admin setting so the student-facing widget now focuses on official progress plus what remains.

## [0.7.1] - 2026-03-14

### Changed

- Refined student-facing wording so the main progress is clearly the official course progress while the modal focuses on what remains.
- Quiz pending items now present as a single pending action with remaining-question detail instead of sounding like separate actions.

## [0.7.0] - 2026-03-14

### Changed

- Main progress bar now follows Moodle course completion by default to avoid conflicting percentages for students.
- Added a pending actions summary under the main progress indicator.
- Added an admin setting to switch between Moodle completion and the legacy custom progress mode.

## [0.6.9] - 2026-03-14

### Fixed

- Added standard Moodle/GPL boilerplate headers to source files flagged for plugin review.
- Replaced string-built HTML rendering in the AMD source with a Mustache template rendered through `core/templates`.

## [0.6.8] - 2026-03-14

### Fixed

- Removed manual `$PAGE->requires->css()` loading for plugin `styles.css`.
- Replaced the AMD build artifact with a compact synchronized `progress.min.js` matching the current source module.

## [0.6.7] - 2026-03-13

### Changed

- Pending timeline now keeps direct links visible even when an item is not yet available.
- Added section fallback links when a pending item does not expose a direct activity URL.
- Added clearer availability messaging inside pending timeline cards.

## [0.6.6] - 2026-03-13

### Changed

- Removed log-based course module view detection from progress calculations.
- Added an explicit resource mode so resources can be counted only when Moodle completion tracking is configured.

### Fixed

- Avoided reading `logstore_standard_log` during progress rendering to reduce performance risk on large sites.

## [0.6.5] - 2026-03-08

### Fixed

- Isolated Pro DOM/CSS/JS namespace (`local-courseprogresspro`) to avoid runtime/UI conflicts when Lite and Pro are installed together.

### Changed

- Updated release metadata for Moodle upgrade detection.

## [0.6.4] - 2026-03-08

### Fixed

- Enforced `enabled` setting in all render hooks to fully disable output when turned off.

### Changed

- Updated default header label to student-friendly text: "Your course progress" / "Tu avance en el curso".

## [0.6.3] - 2026-03-08

### Added

- Pro timeline with direct activity links and availability status.
- Screenshot assets for Pro edition.

### Changed

- Component and folder naming aligned to Pro schema.
- Added global plugin enable/disable setting.
- Student-facing labels remain generic (no Lite/Pro in course UI).
