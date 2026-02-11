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

/* SMS Notifier LabsMobile Block
 * SMS notifier is a one way SMS messaging block that allows managers, teachers and administrators to
 * send text messages to their student and teacher.
 * @package blocks
 * @author: Waqas Ansari, LabsMobile (https://www.labsmobile.com)
 * @date: 21-May-2019
*/
/**
 * @copyright 2024 LabsMobile <info@labsmobile.com>, 2019 3iLogic <info@3ilogic.com>
 */
defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {

    /* ========================= LabsMobile ======================= */

    // Definimos una clase temporal para inyectar HTML puro sin que Moodle lo escape.
    if (!class_exists('admin_setting_labsmobile_html')) {
        class admin_setting_labsmobile_html extends admin_setting {
            public $html;
            public function __construct($name, $html) {
                parent::__construct($name, '', '', '');
                $this->html = $html;
            }
            public function get_setting() { return true; }
            public function write_setting($data) { return true; }
            public function output_html($data, $query = '') {
                return $this->html;
            }
        }
    }

    $logo_url = $OUTPUT->image_url('icon', 'block_sms');
    $premium_html = '
    <div class="labsmobile-premium-container">
        <div class="labsmobile-header-flex">
            <div class="labsmobile-welcome-text">
                <h3>¡Bienvenido a SMS Notifier!</h3>
                <p>Configura tu conexión con LabsMobile para empezar a enviar notificaciones.</p>
            </div>
            <div class="labsmobile-logo-wrapper">
                <img src="' . $logo_url . '" class="labsmobile-logo-settings" alt="LabsMobile Logo">
            </div>
        </div>
    </div>';
    
    // Inyectamos el HTML usando nuestra clase personalizada.
    $settings->add(new admin_setting_labsmobile_html('labsmobile_premium_header', $premium_html));

    $settings->add(new admin_setting_configtext("labsmobile_username",
        get_string('sms_api_key', 'block_sms'),
        'Email de tu cuenta LabsMobile.',
        '', PARAM_TEXT));
    $settings->add(new admin_setting_configpasswordunmask("labsmobile_password",
        get_string('sms_api_secret', 'block_sms'),
        'Tu API Token secreto obtenido en el panel de LabsMobile.',
        ''));
    $settings->add(new admin_setting_configtext("labsmobile_sender",
        get_string('sms_api_from', 'block_sms'),
        'Remitente numérico o alfanumérico de hasta 11 caracteres',
        '', PARAM_TEXT));

    // Optimized CSS for a Premium look.
    echo '<style>
        .labsmobile-premium-container {
            background: #f8fbfc;
            border-left: 5px solid #2ab3b5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .labsmobile-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .labsmobile-welcome-text h3 {
            color: #3c3c3b;
            margin-top: 0;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .labsmobile-welcome-text p {
            color: #666;
            margin: 0;
            font-size: 0.95rem;
        }
        .labsmobile-welcome-text a {
            color: #2ab3b5;
            font-weight: bold;
            text-decoration: underline;
        }
        .labsmobile-logo-wrapper {
            flex-shrink: 0;
            margin-left: 20px;
        }
        .labsmobile-logo-settings {
            width: 90px;
            height: auto;
            border-radius: 6px;
            display: block;
        }
        /* Resaltar campos de LabsMobile */
        #admin-labsmobile_username, #admin-labsmobile_password, #admin-labsmobile_sender {
            border-bottom: 1px dashed #eee;
            padding: 10px 0;
            margin-bottom: 10px;
        }
        /* Espacio extra para que el botón "Save changes" no choque con la línea */
        #admin-labsmobile_sender {
            margin-bottom: 15px !important;
        }
        .form-description {
            color: #2ab3b5 !important;
            font-style: italic;
            font-size: 0.85rem !important;
        }
        @media (max-width: 768px) {
            .labsmobile-logo-settings {
                position: static;
                float: none;
                display: block;
                margin: 10px auto;
            }
        }
        #admin-labsmobile_username .form-shortname,
        #admin-labsmobile_password .form-shortname,
        #admin-labsmobile_sender .form-shortname {
            display: none !important;
        }
    </style>';
}