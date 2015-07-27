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

/**
 * Settings file for the local_dsv_organization.
 * 
 * @package   local_dsv_organization
 * @copyright 2015 Pavel Sokolov <pavel.m.sokolov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_dsv_organization', new lang_string('pluginname', 'local_dsv_organization'));
    $ADMIN->add('localplugins', $settings);

    $ADMIN->add('accounts', new admin_externalpage('local_dsv_organization_fetch',
            get_string('pluginmenuname', 'local_dsv_organization'),
            new moodle_url('/local/dsv_organization/index.php')));

    $settings->add(new admin_setting_heading('local_dsv_organization', '', get_string('local_dsv_organizationdescription', 'local_dsv_organization')));
    $settings->add(new admin_setting_configtext('local_dsv_organization/restapiurl', get_string('restapiurl', 'local_dsv_organization'), '', '')); 
    $settings->add(new admin_setting_configtext('local_dsv_organization/username', get_string('username'), '', ''));
    $settings->add(new admin_setting_configtext('local_dsv_organization/password', get_string('password'), '', ''));
    $settings->add(new admin_setting_configtext('local_dsv_organization/employeeresource', get_string('employeeresource', 'local_dsv_organization'), '', ''));
}
