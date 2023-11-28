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
if ($ADMIN->fulltree) {

    /* ========================= nexmo ======================= */

    $settings->add(new admin_setting_configtext("block_sms_nexmo_apikey",
        get_string('sms_api_key', 'block_sms'),
        get_string('sms_api_key', 'block_sms'),
        '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext("block_sms_nexmo_api_secret",
        get_string('sms_api_secret', 'block_sms'),
        get_string('sms_api_secret', 'block_sms'),
        '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext("block_sms_nexmo_api_from",
        get_string('sms_api_from', 'block_sms'),
        get_string('sms_api_from', 'block_sms'),
        '', PARAM_TEXT));
    /* ========================= LabsMobile ======================= */

    $settings->add(new admin_setting_configtext("block_sms_labsmobile_username",
        get_string('sms_api_key', 'block_sms'),
        get_string('sms_api_key', 'block_sms'),
        '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext("block_sms_labsmobile_password",
        get_string('sms_api_secret', 'block_sms'),
        get_string('sms_api_secret', 'block_sms'),
        '', PARAM_TEXT));
      $settings->add(new admin_setting_configtext("block_sms_labsmobile_sender",
        get_string('sms_api_from', 'block_sms'),
        get_string('sms_api_from', 'block_sms'),
        '', PARAM_TEXT));

    /* ========================= clickatell =========================*/
    $settings->add(new admin_setting_configtext("block_sms_clickatell_apikey",
        get_string('sms_api_key', 'block_sms'),
        get_string('sms_api_key', 'block_sms'),
        '', PARAM_TEXT));

    /* ============================== twilio  ====================*/
    $settings->add(new admin_setting_configtext("block_sms_twilio_accountsid",
        get_string('sms_twilio_accountsid', 'block_sms'),
        get_string('sms_twilio_accountsid', 'block_sms'),
        '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext("block_sms_twilio_auth_token",
        get_string('sms_twilio_auth_token', 'block_sms'),
        get_string('sms_twilio_auth_token', 'block_sms'),
        '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext("block_sms_twilio_api_from",
        get_string('sms_api_from', 'block_sms'),
        get_string('sms_api_from', 'block_sms'),
        '', PARAM_TEXT));

    $settings->add(new admin_setting_configselect('block_sms_api', 'SMS API Name', 'Select Api which you are using', 'Clickatell',
        array("clickatell" => "Clickatell", "nexmo" => 'Nexmo SMS', "labsmobile" => 'LabsMobile SMS', "twilio" => 'Twilio SMS')));

    echo '
    <script type="text/javascript" src="//code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
        $(() => {
            let vendors = {"clickatell": ["apikey"],
                            "nexmo": ["apikey", "api_secret", "api_from"],
                            "labsmobile": ["username", "api_token", "sender"],
                            "twilio": ["accountsid", "auth_token", "api_from"]
                            /*"sendpk": ["username", "password", "from_no"]*/};
            const selectedVendor = $("#id_s__block_sms_api option:selected").val();
            Object.keys(vendors).forEach((vendorName) => {
                if(vendorName === selectedVendor) {
                    vendors[vendorName].forEach(param => {
                       $("#admin-block_sms_"+vendorName+"_"+param).show();
                    });
                } else {
                    vendors[vendorName].forEach(param => {
                       $("#admin-block_sms_"+vendorName+"_"+param).hide();
                    });
                }
            });
            $("#id_s__block_sms_api").on("change", e => {
               const selectedVendor = $("#id_s__block_sms_api option:selected").val();
                Object.keys(vendors).forEach((vendorName) => {
                    if(vendorName === selectedVendor) {
                        vendors[vendorName].forEach(param => {
                           $("#admin-block_sms_"+vendorName+"_"+param).show();
                        });
                    }
                    else {
                        vendors[vendorName].forEach(param => {
                           $("#admin-block_sms_"+vendorName+"_"+param).hide();
                        });
                    }
                });
            });
        });
    </script>
    ';
}