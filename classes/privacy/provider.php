<?php
/**
 * Privacy provider for local_courseprogresspro.
 *
 * @package   local_courseprogresspro
 * @copyright 2026 Jesus Antonio Jimenez Avina <antoniomexdf@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_courseprogresspro\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\null_provider;

/**
 * Privacy API provider for local_courseprogresspro.
 *
 * This plugin does not store any personal data.
 *
 * @package    local_courseprogresspro
 * @copyright  2026 Jesus Antonio Jimenez Avina <antoniomexdf@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements null_provider {
    /**
     * Returns reason why this plugin has no personal data.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
