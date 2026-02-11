M.block_sms = {};
M.block_sms.init = function (Y, viewpage, loadinghtml) {

    // Variables.
    var showuser = Y.one("#btnajax");
    var sms_send = Y.one('#smssend');
    var action = Y.one('#id_rid');
    var action1 = Y.one('#id_mid');
    var action2 = Y.one('#id_cid');
    var userlist = Y.one("#table-change");
    var img = Y.one('#load');
    var msg_body = Y.one('#id_sms_body');

    // Load message initially if elements exist.
    if (action1 && msg_body && img) {
        var m_id = action1.get('value');
        if (m_id) {
            Y.io('load_message.php?m_id=' + m_id, {
                on: {
                    start: function (id, args) {
                        msg_body.hide();
                        img.show();
                    },
                    complete: function (id, e) {
                        var json = e.responseText;
                        img.hide();
                        msg_body.show();
                        msg_body.set('value', json);
                    }
                }
            });
        }
    }

    // Image default setting.
    if (img) img.hide();
    if (sms_send) sms_send.hide();

    // Event occurs after click on show user button.
    if (showuser && action && action2 && userlist) {
        showuser.on('click', function () {
            var content = Y.one('#id_sms_body');
            var c_id = action2.get('value');
            var r_id = action.get('value');
            var msg = content ? content.get('value') : '';

            Y.io('user_list.php?msg=' + msg + '&c_id=' + c_id + '&r_id=' + r_id, {
                on: {
                    start: function (id, args) {
                        userlist.set('innerHTML', loadinghtml);
                    },
                    complete: function (id, e) {
                        var json = e.responseText;
                        userlist.set('innerHTML', json);
                        if (sms_send) sms_send.show();
                        
                        // Safely initialize DataTable only if the table exists and has the 'display' class
                        if (typeof $ !== 'undefined' && $.fn.dataTable) {
                            var targetTable = $('#table-change table.display');
                            if (targetTable.length > 0 && !$.fn.DataTable.isDataTable(targetTable)) {
                                try {
                                    targetTable.DataTable({ 
                                        paging: false,
                                        info: false,
                                        searching: false
                                    });
                                } catch (err) {
                                    console.log('DataTable initialization skipped: ' + err.message);
                                }
                            }
                        }
                    }
                }
            });
        });
    }

    // If viewpage is 2 means send sms page.
    if (viewpage == '2') {
        if (action) {
            action.on('change', function () {
                var b = this.get('text');
            });
        }

        // Select Message Template.
        if (action1 && img) {
            action1.on('change', function () {
                var content = Y.one('#id_sms_body');
                var m_id = action1.get('value');
                if (content) {
                    Y.io('load_message.php?m_id=' + m_id, {
                        on: {
                            start: function (id, args) {
                                content.hide();
                                img.show();
                            },
                            complete: function (id, e) {
                                var json = e.responseText;
                                img.hide();
                                content.show();
                                content.set('value', json);
                            }
                        }
                    });
                }
            });
        }
    }
};