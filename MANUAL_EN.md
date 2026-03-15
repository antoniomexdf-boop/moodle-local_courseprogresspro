# Course Progress Pro - User and Admin Manual (English)

## Overview

`local_courseprogresspro` provides an advanced progress interface for course pages.
This distribution package ships with English strings only for Moodle plugin directory compliance.

## Repository

- GitHub: https://github.com/antoniomexdf-boop/moodle-local_courseprogresspro

## Author and Contact

- Author: Jesus Antonio Jimenez Avina
- Email: antoniomexdf@gmail.com

## Features

- Progress bar and percentage
- Main progress source aligned to Moodle course completion by default
- Pending actions summary
- Pending actions button
- Pending timeline/modal with links and availability state
- Pending timeline keeps direct access links visible for activities and section fallbacks
- Quiz items appear as one pending action with remaining-question detail
- Completion-aware resource counting mode
- Course sections are never counted as progress because they do not expose reliable completion evidence
- Global plugin enable/disable

## Student Experience

Students see a generic progress UI; edition labels are not shown in the course interface.
The main progress indicator follows official Moodle course progress by default.
Pending items are presented as what remains to be completed, not as a second competing percentage.
The pending summary is integrated into the action button for a cleaner layout.

## Requirements

- Moodle 4.1+

## Installation

1. Copy plugin into `moodle/local/courseprogresspro`.
2. Complete installation as admin.
3. Purge caches.

## Configuration

Path:
`Site administration > Plugins > Local plugins > Course Progress Pro`

Available settings:

- Enable plugin
- Main progress source
- Count resources
- Require completion tracking for resources
- Quiz calculation mode
- Show numeric percentage
- Show pending actions button
- Header text

Notes:
- Course sections are used only as context and fallback navigation in the pending timeline.
- Course sections are not counted as completed or pending progress units.

## Screenshots

![Pro Progress Bar](screenshots/courseprogresspro_01.png)
![Pro Progress Bar - Alternate View](screenshots/courseprogresspro_02.png)
![Pro Pending Timeline](screenshots/courseprogresspro_03.png)

## Privacy

No personal data is stored.

## Release

- Release: `0.7.18`
- Version: `2026031511`
