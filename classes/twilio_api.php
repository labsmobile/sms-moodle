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

class TwilioAPI {
    private $uri;
    private $accountsid;
    private $authtoken;
    private $fromnumber;

    public function __construct() {
        global $CFG;
        $this->accountsid = $CFG->block_sms_twilio_accountsid;
        $this->authtoken = $CFG->block_sms_twilio_auth_token;
        $this->fromnumber = $CFG->block_sms_twilio_api_from;
        $this->uri = 'https://api.twilio.com/2010-04-01/Accounts/'.$this->accountsid.'/Messages.json';

        if ($this->fromnumber[0] != '+') {
            $this->fromnumber = "+".$this->fromnumber;
        }
    }

    public function send_sms($to, $text) {
        if ($to[0] != '+') {
            $to = "+$to";
        }

        $result = $this->trigger_api($to, $text);
        $status = false;
        try {
            $result = json_decode($result, true);
            $status = $result["messages"][0]["status"] == 0;
        } catch (Exception $e) {
            ";";
        }
        return $status;
    }

    private function trigger_api($to, $text) {
        $data = array(
            'From' => $this->fromnumber,
            'To' => $to,
            'Body' => urlencode($text)
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uri);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->accountsid . ':' . $this->authtoken);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
