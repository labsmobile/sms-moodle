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
require_once('../../config.php');
require_once('sms_form.php');
require_once("lib.php");
require_login();

$cid = required_param('c_id', PARAM_INT);
$group = optional_param('group', 0, PARAM_INT);

if ($cid && $group) {
    if ($group == '0') {
        $attributes = array('1' => 'No Group');
    } else if ($group == '2') {
        $attributes = get_groups($cid);
    }
} else if ($cid) {
    if (!isGroup_null($cid)) {
        $attributes = array('1' => 'No Group');
    } else {
        $attributes = array('1' => 'No Group', '2' => 'Group');
    }
}

$data = "";
foreach ($attributes as $key => $attrib) {
    $data .= $key . '~' . $attrib . '^';
}
return $data;
