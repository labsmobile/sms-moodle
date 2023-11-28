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

require_once(dirname(__FILE__) . '/../../config.php');
require_login();

// Send SMS pk Api Function.
/**
 * This function will send the SMS using sendsms.pk.API is only for Pakistan's users.
 *
 * @param int   $to  User id
 * @param string $msg  Message Text
 * @return String $status return will shows the status of message.
 */
function yutobo_path($apikey, $from, $to, $text) {

    $url = "https://services.yuboto.com/web2sms/api/v2/smsc.aspx?api_key=" . $apikey .
            "&action=send&from=" . $from . "&to=" . $to . "&text=" . $text;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    return $output;
    curl_close($ch);
}

function bulk_sms($to, $message) {
    global $CFG;
    $numbers = $to;
    $username = $CFG->block_sms_api_username;
    $password = $CFG->block_sms_api_password;
    $message = str_replace("'", "", $message);
    $message = urlencode($message);
    $sender = "3i Logic";
    $url = "http://sendpk.com/api/sms.php?username=" . $username . "&password=" . $password .
            "&mobile=" . $numbers . "&message=" . $message . "&sender=" . $sender;

    $ch = curl_init();
    $timeout = 30;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $output = curl_exec($ch);
    $count = substr_count($output, 'ID');
    curl_close($ch);
    return $output;
}

/**
 * This function will send the SMS using Clickatells API, by this API Users can send international messages.
 *
 * @param int   $to  User id
 * @param string $msg  Message Text
 * @return Call back URL through clickatell
 */
function send_sms_clickatell($to, $message) {
    global $CFG;
    $numbers = '';
    foreach ($to as $num) {
        if ($numbers == '') {
            $numbers = $num;
        } else {
            $numbers .= ',' . $num;
        }
    }

    $username = $CFG->block_sms_api_username;
    $password = $CFG->block_sms_api_password;
    $apiid = $CFG->block_sms_apikey;

    $password = urlencode($password);
    // Send Sms.
    $url = "http://api.clickatell.com/http/sendmsg?user=" . $username . "&password=" . $password .
            "&apiid=" . $apiid . "&to=" . $numbers . "&text=" . $message;

    $ch = curl_init();
    $timeout = 30;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function block_sms_print_page($sms, $return = false) {
    global $OUTPUT, $COURSE;
    $display = $OUTPUT->heading($sms->pagetitle);
    $display .= $OUTPUT->box_start();
    if ($sms->displaydate) {
        $display .= userdate($sms->displaydate);
    }
    if ($return) {
        return $display;
    } else {
        echo $display;
    }
}

/**
 * This function will return the message template.
 *
 * @param int   $to  Message id
 * @return string $result->msg return message template on the base of message id
 */
function get_msg($id) {
    global $DB;

    $result = $DB->get_record_sql('SELECT {competency_job_user}.j_id, {competency_job}.job
                FROM {competency_job}
                INNER JOIN {competency_job_user} ON ({competency_job}.id = {competency_job_user}.j_id)
                WHERE {competency_job_user}.u_id = ?', array($id));

    return $result->msg;
}
