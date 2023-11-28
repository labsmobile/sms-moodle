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
// Global variable.
global $DB, $OUTPUT, $PAGE, $CFG, $USER;
require_login();
// Plugin variable.
$viewpage = required_param('viewpage', PARAM_INT);
$rem = optional_param('rem', null, PARAM_RAW);
$edit = optional_param('edit', null, PARAM_RAW);
$delete = optional_param('delete', null, PARAM_RAW);
$id = optional_param('id', null, PARAM_INT);
// Page settings.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string("pluginname", 'block_sms'));
$PAGE->set_heading('SMS Notification');
$pageurl = new moodle_url('/blocks/sms/view.php?viewpage=2');
$PAGE->set_url($pageurl);
echo $OUTPUT->header();
?>

    <!-- DataTables code starts-->
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.9/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/tabletools/2.2.4/css/dataTables.tableTools.css">
    <script type="text/javascript" language="javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.9/js/jquery.js"></script>
    <script type="text/javascript" language="javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.9/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript"
            src="//cdn.datatables.net/tabletools/2.2.4/js/dataTables.tableTools.js"></script>
    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function () {
            // fn for automatically adjusting table coulmns
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            $('.display').DataTable({
                dom: 'T<"clear">lfrtip',
                tableTools: {
                    "aButtons": [
                        "copy",
                        "print",
                        {
                            "sExtends": "collection",
                            "sButtonText": "Save",
                            "aButtons": ["xls", "pdf"],
                        }
                    ],
                    "sSwfPath": "public/datatable/copy_csv_xls_pdf.swf",
                }
            });
        });
    </script>
    <!-- DataTables code ends-->

    <!-- Check/Uncheck All Starts -->
    <script type="text/javascript" language="javascript">
        var act = 0;
        function setCheckboxes() {
            if (act == 0) {
                act = 1;
            } else {
                act = 0;
            }
            var e = document.getElementsByClassName('check_list');
            var elts_cnt = (typeof (e.length) != 'undefined') ? e.length : 0;
            if (!elts_cnt) {
                return;
            }
            for (var i = 0; i < elts_cnt; i++) {
                e[i].checked = (act == 1 || act == 0) ? act : (e[i].checked ? 0 : 1);
            }
        }
    </script>

<?php

if ($viewpage == 1) {
    $form = new sms_form();
    if ($table = $form->display_report()) {
        $a = html_writer::table($table);
        echo "<form action='#' method='GET' name='tests'>" . $a . "<input type='submit' name='submit' value='submit'/>
                <input type='hidden' name='viewpage' id='viewpage' value='$viewpage'/></form>";
        if (isset($_GET['submit'])) {
            $user = $_GET['user'];
            if (empty($user)) {
                echo("You didn't select any user.");
            } else {
                $n = count($user);
            }
            for ($i = 0; $i <= $n; $i++) {
                send_sms($user[$i], "SMS sent successfully");
            }
        }
    }
} else if ($viewpage == 2) {
    $form = new sms_send();
    $form->display();
    $table = $form->display_report();
    $a = html_writer::table($table);
    echo "<form action='' method='post' name='tests'><div id='table-change'>" . $a . "</div>
            <input type='submit' style='margin-left:700px;' name='submit' id='smssend' value='Send SMS'/>
            <input type='hidden' name='viewpage' id='viewpage' value='$viewpage'/></form>";
    $n = '';
    if (isset($_REQUEST['submit'])) {

        $msg = $_REQUEST['msg']; // SMS Meassage.
        if (isset($_REQUEST['user']) && $_REQUEST['user'] != '') {
            $user = $_REQUEST['user']; // User ID.
            $n = count($user);
        } else {
            echo("You didn't select any user.");
        }

        $table = new html_table();
        $table->head = array(get_string('serial_no', 'block_sms'),
                                get_string('moodleuser', 'block_sms'),
                                get_string('usernumber', 'block_sms'),
                                get_string('status', 'block_sms'));
        $table->size = array('10%', '40%', '30%', '20%');
        $table->align = array('center', 'left', 'center', 'center');
        $table->width = '100%';

        require_once('classes/sms_notifier.php');
        $gateway = new SMSNotifier($CFG->block_sms_api);
        $table->data = $gateway->process_sms($user, $msg);
        echo html_writer::table($table);

    }
} else if ($viewpage == 3) {
    $form = new template_form();
    if ($rem) {
        if ($delete) {
            global $DB;
            $DB->delete_records('block_sms_template', array('id' => $delete));
            redirect($pageurl);
        } else {
            echo $OUTPUT->confirm(get_string('askfordelete', 'block_sms'),
                '/blocks/sms/view.php?viewpage=3&rem=rem&delete=' . $id, '/blocks/sms/view.php?viewpage=3');
        }
    }
    // Edit Message Template.
    if ($edit) {
        $gettemplate = $DB->get_record('block_sms_template', array('id' => $id), '*');
        $form = new template_form();
        $form->set_data($gettemplate);
    }
    $toform['viewpage'] = $viewpage;
    $form->set_data($toform);
    $form->display();
    $table = $form->display_report();
    echo html_writer::table($table);
}

if ($fromform = $form->get_data()) {
    if ($viewpage == 3) {
        global $DB;
        $chk = ($fromform->id) ? $DB->update_record('block_sms_template', $fromform) :
            $DB->insert_record('block_sms_template', $fromform);
        redirect($pageurl);
    }
}

$params = array($viewpage);
$PAGE->requires->js_init_call('M.block_sms.init', $params);
echo $OUTPUT->footer();
