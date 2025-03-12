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
defined('MOODLE_INTERNAL') || die();
class LabsmobileAPI {
    private $uri;
    private $username;
    private $password;
    private $sender;
    private $test;


    public function __construct() {
        global $CFG;
        $this->uri = "https://api.labsmobile.com/get/send.php";
        $this->username = $CFG->block_sms_labsmobile_username;
        $this->password = $CFG->block_sms_labsmobile_password;
        $this->sender = $CFG->block_sms_labsmobile_sender;
        //$this->test = 1;
    }

    public function send_sms($to, $text) {
        $result = $this->trigger_api($to, $text);
        $status = false;
        if(stripos($result, "<code>") !== FALSE) {
          $initpos = stripos($result, "<code>") + 6;
          $endpos = stripos($result, "</code>");
          $code = substr($result, $initpos, $endpos - $initpos);
          if($code == "0") {
            $status = true;
          }
        }
        return $status;
    }

    private function trigger_api($to, $text) {
        $ch = curl_init();
        $params = "username=" . $this->username . 
          "&password=" . $this->password . 
          "&sender=" . $this->sender . 
          //"&test=" . $this->test .
          "&msisdn={$to}" .
          "&message=" . $text;
        curl_setopt($ch, CURLOPT_URL, $this->uri . "?" . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}