<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/* SMS Notifier Block
 * SMS notifier is a one way SMS messaging block that allows managers, teachers and administrators to
 * send text messages to their student and teacher.
 * @package blocks
 * @author: Waqas Ansari
 * @date: 21-May-2019
*/
/**
 * @copyright 2019 3iLogic <info@3ilogic.com>
 */
defined('MOODLE_INTERNAL') || die;
$plugin->version = 2019052200;
$plugin->requires = 2019052000;
$plugin->component = 'block_sms';
$plugin->release = '3.7';
$plugin->maturity = MATURITY_STABLE;