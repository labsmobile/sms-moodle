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
/* SMS notifier Block
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

require_once("nexmo_api.php");
require_once("labsmobile_api.php");
require_once("twilio_api.php");
require_once("clickatell_api.php");

class SMSNotifier {
    private $type;
    private $apilist = array("nexmo", "labsmobile", "twilio", "clickatell");
    private $instance;

    public function __construct($type = "") {
        if (!in_array($type, $this->apilist)) {
            return false;
        }
        $this->type = $type;
        switch ($type) {
            case "nexmo":
                $this->instance = new NexmoAPI();
                break;
            case "labsmobile":
                $this->instance = new LabsmobileAPI();
                break;
            case "twilio":
                $this->instance = new TwilioAPI();
                break;
            case "clickatell":
                $this->instance = new ClickatellAPI();
                break;
        }
    }

    public function get_sms_vendor($type = "") {
        if (!in_array($type, $this->apilist)) {
            return false;
        }

        switch ($type) {
            case "nexmo":
                $this->instance = new NexmoAPI();
                break;
            case "labsmobile":
                $this->instance = new LabsmobileAPI();
                break;
            case "twilio":
                $this->instance = new TwilioAPI();
                break;
            case "clickatell":
                $this->instance = new ClickatellAPI();
                break;
        }
    }

    public function process_sms($users, $text) {
        global $CFG;

        $result = [];
        $usersdetail = $this->get_users_detail($users);
        $counter = 0;
        foreach ($usersdetail as $detail) {
            if (!empty($detail->phone)) {
                $status = $this->instance->send_sms($detail->phone, $text);
                if ($status === true) {
                    $status = "<img src=" . $CFG->wwwroot . '/blocks/sms/pic/success.png' . "></img>";
                } else {
                    $status = "<img src=" . $CFG->wwwroot . '/blocks/sms/pic/error.png' . "></img>";
                }
            } else {
                $status = "<img src=" . $CFG->wwwroot . '/blocks/sms/pic/error.png' . "></img>";
            }

            $counter++;
            $row = array();
            $row[] = $counter;
            $row[] = $detail->firstname . ' ' . $detail->lastname;
            $row[] = $detail->phone;
            $row[] = $status;
            $result[] = $row;
        }
        return $result;
    }

    private function get_users_detail($users) {
        global $DB;
        $sql = 'SELECT usr.id, usr.firstname, usr.lastname, usr.email, usr.phone2 AS phone
                FROM prefix_user AS usr
                WHERE usr.id IN ('.implode(",", $users).')';
        $usersdetail = $DB->get_records_sql($sql);
        return $usersdetail;
    }
}