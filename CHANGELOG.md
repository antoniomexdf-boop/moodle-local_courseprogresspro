# Changelog

All notable changes to `local_courseprogresspro` are documented in this file.

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
