# Moodle Plugins Publishing Checklist (Pro)

## Edition Identity

- Repository: `moodle-local_courseprogresspro`
- Moodle plugin folder: `courseprogresspro`
- Moodle component: `local_courseprogresspro`

## Package info

- Release: `0.7.3`
- Version: `2026031402`
- Requires: Moodle `4.1+`

## Pre-upload validation

1. Install in clean Moodle 4.1+.
2. Verify upgrade path.
3. Verify `Enable plugin` setting works (on/off).
4. Verify student UI is generic (no Lite/Pro label in course view).
5. Purge caches and verify UI behavior (bar + pending modal).
6. Run plugin checks (`moodle-plugin-ci`) if available.

## Assets

- Screenshots: `screenshots/courseprogresspro_01.png`, `screenshots/courseprogresspro_02.png`, `screenshots/courseprogresspro_03.png`

## Zip format

Top-level folder must be:

`courseprogresspro/`
