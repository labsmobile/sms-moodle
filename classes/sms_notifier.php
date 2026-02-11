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

require_once("labsmobile_api.php");

class SMSNotifier {
    private $type = 'labsmobile';
    private $instance;

    public function __construct($type = "labsmobile") {
        $this->instance = new LabsmobileAPI();
    }

    public function get_sms_vendor($type = "labsmobile") {
        $this->instance = new LabsmobileAPI();
    }

    public function process_sms($users, $text) {
        global $CFG;

        $result = [];
        $usersdetail = $this->get_users_detail($users);
        $counter = 0;
        foreach ($usersdetail as $detail) {
            if (!empty($detail->phone)) {

              $replacements = [
                '%VAR_DEPARTMENT%'   => $detail->department,
                '%VAR_FIRSTNAME%'    => $detail->firstname,
                '%VAR_ADDRESS%'      => $detail->address,
                '%VAR_LASTNAME%'     => $detail->lastname,
                '%VAR_EMAIL%'        => $detail->email,
                '%VAR_USERNAME%'     => $detail->username,
                '%VAR_INSTITUTION%'  => $detail->institution,
                '%VAR_CITY%'         => $detail->city,
                '%VAR_COURSE%'       => $detail->course,
              ];
            
              $personalized_text = $text; // Texto original
              foreach ($replacements as $key => $value) {
                  // Reemplazar los espacios por '+'
                  $value = str_replace(' ', '+', $value);
                  // Reemplazar tanto las variables como la versión codificada de ellas
                  $personalized_text = str_replace([$key, rawurlencode($key)], $value, $personalized_text);
              }

              $personalized_text = str_replace('%5C%5Cn', '%0A', $personalized_text);
              $status = $this->instance->send_sms($detail->phone, $personalized_text);

              
              //$status = $this->instance->send_sms($detail->phone, $text);

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
        $sql2 = 'SELECT usr.id, usr.firstname, usr.lastname, usr.email, usr.username, usr.institution, usr.department, usr.address, usr.city, usr.phone2 AS phone
                FROM mdl_user AS usr
                WHERE usr.id IN ('.implode(",", $users).')';
        $sql = 'SELECT 
                  usr.id, 
                  usr.firstname, 
                  usr.lastname, 
                  usr.email, 
                  usr.username, 
                  usr.institution, 
                  usr.department, 
                  usr.address, 
                  usr.city, 
                  usr.phone2 AS phone,
                  c.fullname AS course
                FROM mdl_user AS usr
                LEFT JOIN mdl_user_enrolments AS ue ON usr.id = ue.userid
                LEFT JOIN mdl_enrol AS e ON ue.enrolid = e.id
                LEFT JOIN mdl_course AS c ON e.courseid = c.id
                WHERE usr.id IN ('.implode(",", $users).')';
        $usersdetail = $DB->get_records_sql($sql);
        return $usersdetail;
    }
}