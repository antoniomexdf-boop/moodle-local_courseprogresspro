# Changelog

All notable changes to `local_courseprogresspro` are documented in this file.

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
