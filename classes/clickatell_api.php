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

class ClickatellAPI {
    private $uri;
    private $apikey;

    public function __construct() {
        global $CFG;
        $this->uri = "https://platform.clickatell.com/messages/http/send";
        $this->apikey = $CFG->block_sms_clickatell_apikey;
        $this->uri = $this->uri . "?apiKey=" . $this->apikey;
    }

    public function send_sms($to, $text) {
        $result = $this->trigger_api($to, $text);
        $status = false;
        try {
            $result = json_decode($result, true);
            $status = $result["messages"][0]["accepted"] == "true";
        } catch (Exception $e) {
            ";";
        }
        return $status;
    }

    private function trigger_api($to, $text) {
        $data = "&to={$to}&content=" . urlencode($text);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uri . $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
