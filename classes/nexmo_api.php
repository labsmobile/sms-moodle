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
class NexmoAPI {
    private $uri;
    private $apikey;
    private $secretkey;
    private $fromnumber;

    public function __construct() {
        global $CFG;
        $this->uri = "https://rest.nexmo.com/sms/json";
        $this->apikey = $CFG->block_sms_nexmo_apikey;
        $this->secretkey = $CFG->block_sms_nexmo_api_secret;
        $this->fromnumber = $CFG->block_sms_nexmo_api_from;
        $this->uri = $this->uri . "?api_key=" . $this->apikey . "&api_secret=" . $this->secretkey;
    }

    public function send_sms($to, $text) {
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
        $data = "from={$this->fromnumber}&to={$to}&text=".urlencode($text);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded"
            )
        );
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}