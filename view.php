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
 * @author: Waqas Ansari, LabsMobile (https://www.labsmobile.com)
 * @date: 21-May-2019
*/
/**
 * @copyright 2024 LabsMobile <info@labsmobile.com>, 2019 3iLogic <info@3ilogic.com>
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

// Initialize form early.
$form = null;
if ($viewpage == 1) {
    $form = new sms_form();
} else if ($viewpage == 2) {
    $form = new sms_send();
} else if ($viewpage == 3) {
    $form = new template_form();
}

// Redirect logic MUST happen before any output.
if ($viewpage == 3 && $rem && $delete) {
    $DB->delete_records('block_sms_template', array('id' => $delete));
    redirect(new moodle_url('/blocks/sms/view.php', array('viewpage' => 3)));
}

if ($form && ($fromform = $form->get_data())) {
    if ($viewpage == 3) {
        if (!empty($fromform->template_id)) {
            $fromform->id = $fromform->template_id;
            $DB->update_record('block_sms_template', $fromform);
        } else {
            $DB->insert_record('block_sms_template', $fromform);
        }
        redirect(new moodle_url('/blocks/sms/view.php', array('viewpage' => 3)));
    }
}

// Page settings.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string("pluginname", 'block_sms'));
$PAGE->set_heading('SMS Notifier LabsMobile');
$pageurl = new moodle_url('/blocks/sms/view.php', array('viewpage' => $viewpage));
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
    <style>
        /* Modernize DataTables for Moodle 5.1 / Boost */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            margin-left: 0.75rem;
            transition: border-color 0.15s ease-in-out;
            min-width: 250px;
        }
        
        /* Table Styling - Clean & Professional */
        table.generaltable {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        
        /* Checkbox Styling */
        .check_list {
            cursor: pointer;
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Header Link Styling - Blue like other Moodle links */
        .chkmenu {
            color: #0f6cbf !important;
            text-decoration: underline !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
        }
        
        .chkmenu:hover {
            color: #0a4a83 !important;
        }

        /* Submit Button Container */
        .sms-btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
            padding: 1rem 0;
        }

        #smssend {
            font-weight: 600 !important;
        }
        
        .DTTT_container {
            margin-bottom: 1rem;
        }
        .DTTT_button {
            background: #f8f9fa !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            box-shadow: none !important;
            padding: 0.4rem 0.8rem !important;
            font-size: 0.9rem !important;
        }
        .DTTT_button:hover {
            background: #e2e6ea !important;
        }
    </style>
    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function () {
            // fn for automatically adjusting table coulmns
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            // Only initialize DataTables for the main display tables that are not part of the AJAX user list.
            // This prevents conflicts when the user list is dynamically updated.
            $('.display').not('#table-change .display').DataTable({
                dom: 'lfrtip', 
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                pageLength: 10,
                responsive: true
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
    if ($table = $form->display_report()) {
        $a = html_writer::table($table);
        echo "<form action='#' method='GET' name='tests'>" . $a . "<input type='submit' name='submit' value='submit'/>
                <input type='hidden' name='viewpage' id='viewpage' value='$viewpage'/></form>";
        if (isset($_GET['submit'])) {
            $user = (isset($_GET['user'])) ? $_GET['user'] : array();
            if (empty($user)) {
                echo("You didn't select any user.");
            } else {
                foreach ($user as $u) {
                    send_sms($u, "SMS sent successfully");
                }
            }
        }
    }
} else if ($viewpage == 2) {
    $form->display();
    $table = $form->display_report();
    $a = html_writer::table($table);
    echo "<form action='' method='post' name='tests'>
            <div id='table-change' class='mb-2'>" . $a . "</div>
            <div class='sms-btn-container'>
                <button type='submit' name='submit' id='smssend' class='btn btn-primary'>
                    <i class='fa fa-paper-plane'></i> Send SMS
                </button>
            </div>
            <input type='hidden' name='viewpage' id='viewpage' value='$viewpage'/>
          </form>";
    if (isset($_REQUEST['submit'])) {
        $msg = $_REQUEST['msg']; // SMS Meassage.
        $user = (isset($_REQUEST['user'])) ? $_REQUEST['user'] : ''; // User ID.
        if (empty($user)) {
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
    // Edit Message Template UI preparation.
    if ($edit) {
        $gettemplate = $DB->get_record('block_sms_template', array('id' => $id), '*');
        if ($gettemplate) {
            $gettemplate->template_id = $gettemplate->id;
            $form->set_data($gettemplate);
        }
    }
    // Only set default viewpage if form wasn't already populated by get_data/validation
    if (empty($fromform)) {
        $toform['viewpage'] = $viewpage;
        $form->set_data($toform);
    }
    
    // We will render the table first, then the form inside a modal.
    $table = $form->display_report();
    if (empty($table->data)) {
      $table->data[] = array('—', '—', '—', '—', '—');
    }
    echo html_writer::table($table);

    // Bootstrap Modal for Form.
    echo '
    <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="templateModalLabel">' . ($edit ? "Edit Template" : "New Template") . '</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="form-container">';
            $form->display();
    echo '  </div>
          </div>
        </div>
      </div>
    </div>';

    // Bootstrap Modal for Delete Confirmation.
    echo '
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-danger"><i class="fa fa-exclamation-triangle"></i> Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the template "<span id="delete-template-name"></span>"?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <a href="#" id="confirm-delete-btn" class="btn btn-danger">Delete</a>
          </div>
        </div>
      </div>
    </div>';

    // Javascript to handle modals using Moodle's AMD system.
    $PAGE->requires->js_amd_inline("
    require(['jquery', 'theme_boost/loader'], function($) {
        $(document).ready(function() {
            // Handle Delete Modal
            $('.delete-template-link').on('click', function(e) {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#delete-template-name').text(name);
                $('#confirm-delete-btn').attr('href', 'view.php?viewpage=3&rem=rem&delete=' + id);
            });

            // Handle Edit Modal (No refresh)
            $('.edit-template-link').on('click', function(e) {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var template = $(this).data('template');
                
                // Moodle form field IDs: id_elementname
                $('#id_template_id').val(id);
                $('#id_tname').val(name);
                $('#asd123').val(template);
                
                $('#templateModalLabel').text('Edit Template');
            });

            // Trigger New Template modal and clear form
            $('#btn-new-template').on('click', function() {
                $('#id_template_id').val('');
                $('#id_tname').val('');
                $('#asd123').val('');
                $('#templateModalLabel').text('New Template');
            });

            // Auto-open modal if there are validation errors (server-side check)
            if ('" . (isset($_POST['_qf__template_form']) && !$fromform ? "1" : "0") . "' == '1') {
                require(['theme_boost/loader'], function() {
                    var myModalEl = document.getElementById('templateModal');
                    if (myModalEl) {
                        try {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                var modal = new bootstrap.Modal(myModalEl);
                                modal.show();
                            } else {
                                $(myModalEl).modal('show');
                            }
                        } catch(e) { console.error('Modal failed', e); }
                    }
                });
            }
            
            // Improve form styling for modal
            $('#templateModal .mform').removeClass('box py-3');
        });
    });");
}

    $loadinghtml = '<div class="text-center p-5">' . 
                    $OUTPUT->pix_icon('i/loading', get_string('loading', 'admin'), 'moodle', array('class' => 'icon-size-5')) . 
                    '<div class="mt-2 text-muted fw-bold">' . get_string('loading', 'admin') . '...</div></div>';
    $params = array($viewpage, $loadinghtml);
    $PAGE->requires->js_init_call('M.block_sms.init', $params);
echo $OUTPUT->footer();
