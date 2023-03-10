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

// This page is called via AJAX to repopulte the TOC when LMSFinish() is called.

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/svasu/locallib.php');

$id = optional_param('id', '', PARAM_INT);                  // Course Module ID, or
$a = optional_param('a', '', PARAM_INT);                    // svasu ID
$scoid = required_param('scoid', PARAM_INT);                // sco ID
$attempt = required_param('attempt', PARAM_INT);            // attempt number
$mode = optional_param('mode', 'normal', PARAM_ALPHA);      // navigation mode
$currentorg = optional_param('currentorg', '', PARAM_RAW);  // selected organization.

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('svasu', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }
    if (! $svasu = $DB->get_record("svasu", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else if (!empty($a)) {
    if (! $svasu = $DB->get_record("svasu", array("id" => $a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $svasu->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("svasu", $svasu->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

// PARAM_RAW is used for $currentorg, validate it against records stored in the table.
if (!empty($currentorg)) {
    if (!$DB->record_exists('svasu_scoes', array('svasu' => $svasu->id, 'identifier' => $currentorg))) {
        $currentorg = '';
    }
}

$PAGE->set_url('/mod/svasu/prereqs.php', array('scoid' => $scoid, 'attempt' => $attempt, 'id' => $cm->id));

require_login($course, false, $cm);

$svasu->version = strtolower(clean_param($svasu->version, PARAM_SAFEDIR));   // Just to be safe.
if (!file_exists($CFG->dirroot.'/mod/svasu/datamodels/'.$svasu->version.'lib.php')) {
    $svasu->version = 'scorm_12';
}
require_once($CFG->dirroot.'/mod/svasu/datamodels/'.$svasu->version.'lib.php');


if (confirm_sesskey() && (!empty($scoid))) {
    $result = true;
    $request = null;
    if (has_capability('mod/svasu:savetrack', context_module::instance($cm->id))) {
        $result = svasu_get_toc($USER, $svasu, $cm->id, TOCJSLINK, $currentorg, $scoid, $mode, $attempt, true, false);
        echo $result->toc;
    }
}
