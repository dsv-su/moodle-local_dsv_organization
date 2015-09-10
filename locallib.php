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
 * DSV organization library code.
 *
 * @package   local_dsv_organization
 * @copyright 2015 Pavel Sokolov <pavel.m.sokolov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/cohort/lib.php');

function extract_user_details($user) {
    $username = get_config('local_dsv_organization', 'username');
    $password = get_config('local_dsv_organization', 'password');
    $apiurl = get_config('local_dsv_organization', 'restapiurl');
    $employeeresource = get_config('local_dsv_organization', 'employeeresource');

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xml'));
    curl_setopt($ch, CURLOPT_URL, $apiurl.$employeeresource.'/username/'.$user->username);
    $contents = curl_exec($ch);
    $headers  = curl_getinfo($ch);
    curl_close($ch);
    if ($headers['http_code'] == 200) {
            if (isset($contents)) {
                    try {
                        $oXML = new SimpleXMLElement($contents);
                    } catch (exception $e) {
                        return false;
                    }
                    return $oXML;
            }
    }
}

function print_user_affiliation() {
    global $DB;

    $users = $DB->get_records('user', array('auth' => 'shibboleth'));

    $table = new html_table();
    $table->head = array('Username', 'Name', 'Units');

    foreach ($users as $user) {
        $details = extract_user_details($user);

        if (!$details) {continue;}

        $units = $details->departments->units;
        $unitnames = array();
        foreach ($units as $unit) {
            $unitnames[] =(string)$unit->designation;
        }

        $table->data[] = array($user->username, $details->person->firstName.' '.$details->person->lastName, implode(", ", $unitnames));
    }

    echo html_writer::table($table);

}

function extract_cohort($unit) {
    global $DB, $unitmap;
    return $DB->get_record('cohort', array('idnumber' => $unit));
}

function extract_cohort_id($cohort) {
    return $cohort->id;
}

function update_cohorts($print = false) {
    global $DB;

    $users = $DB->get_records('user', array('auth' => 'shibboleth'));
    
    $unitmap = array(
            'ACT' =>'act',
            'IDEAL' => 'ideal',
            'SAS' => 'sas',
            'IS' => 'is',
            'SPIDER' => 'spider',
            'MobL' => 'mobilelife',
            'eGOV' => 'egovlab',
            'DSV SA' => 'studadmin',
            'DSV A' => 'admin'
    );

    if ($print) {
        $table = new html_table();
        $table->head = array('Username', 'Name', 'Units');
    }

    $cohorts = cohort_get_cohorts(1);

    foreach ($users as $user) {
        $details = extract_user_details($user);

        if (!$details) {continue;}

        $units = $details->departments->units;
        $userunits = array();
        $unitnames = array();
        $usercohorts = array();

        foreach ($units as $unit) {
            $unitnames[] =(string)$unit->designation;
            $userunits[] = extract_cohort($unitmap[(string)$unit->designation]);
        }

        foreach ($cohorts['cohorts'] as $cohort) {
            if (cohort_is_member($cohort->id, $user->id)) {
                $usercohorts[] = $cohort;
            }
        }

//var_dump($userunits);
//var_dump(array_map("extract_cohort_id", $userunits));
//var_dump(array_map("extract_cohort_id", $usercohorts));

$cohortidstoadd = array_diff($userunits, $usercohorts);
$cohortidstoremove = array_diff($usercohorts, $userunits);
echo "user ".$user->username;
if (count($cohortidstoremove)) {var_dump($cohortidstoremove)}
if (count($cohortidstoadd)) {var_dump($cohortidstoadd)}
/*
            if ($cohort) {
                if (!cohort_is_member($cohort->id, $user->id)) {
                    cohort_add_member($cohort->id, $user->id);
                    if (!$print) {
                        echo "Adding ".fullname($user)." (".$user->username.") to ".$cohort->name."\r\n";
                    }
                }
            }
        }
*/

        if ($print) {
            $table->data[] = array($user->username, $details->person->firstName.' '.$details->person->lastName, implode(", ", $unitnames));
        }
//        break;
    }

    if ($print) {
        echo html_writer::table($table);
    }
}
