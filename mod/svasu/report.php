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

// This script uses installed report plugins to print svasu reports.

require_once("../../config.php");
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/mod/svasu/locallib.php');
require_once($CFG->dirroot.'/mod/svasu/reportsettings_form.php');
require_once($CFG->dirroot.'/mod/svasu/report/reportlib.php');
require_once($CFG->libdir.'/formslib.php');

define('SVASU_REPORT_DEFAULT_PAGE_SIZE', 20);
define('SVASU_REPORT_ATTEMPTS_ALL_STUDENTS', 0);
define('SVASU_REPORT_ATTEMPTS_STUDENTS_WITH', 1);
define('SVASU_REPORT_ATTEMPTS_STUDENTS_WITH_NO', 2);

$id = required_param('id', PARAM_INT);// Course Module ID, or ...
$download = optional_param('download', '', PARAM_RAW);
$mode = optional_param('mode', '', PARAM_ALPHA); // Report mode.

$cm = get_coursemodule_from_id('svasu', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$svasu = $DB->get_record('svasu', array('id' => $cm->instance), '*', MUST_EXIST);

$contextmodule = context_module::instance($cm->id);
$reportlist = svasu_report_list($contextmodule);

$url = new moodle_url('/mod/svasu/report.php');

$url->param('id', $id);
if (empty($mode)) {
    $mode = reset($reportlist);
} else if (!in_array($mode, $reportlist)) {
    print_error('erroraccessingreport', 'svasu');
}
$url->param('mode', $mode);

$PAGE->set_url($url);

require_login($course, false, $cm);
$PAGE->set_pagelayout('report');

require_capability('mod/svasu:viewreport', $contextmodule);

if (count($reportlist) < 1) {
    print_error('erroraccessingreport', 'svasu');
}

// Trigger a report viewed event.
$event = \mod_svasu\event\report_viewed::create(array(
    'context' => $contextmodule,
    'other' => array(
        'svasuid' => $svasu->id,
        'mode' => $mode
    )
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('svasu', $svasu);
$event->trigger();

$userdata = null;
if (!empty($download)) {
    $noheader = true;
}
// Print the page header.
if (empty($noheader)) {
    $strreport = get_string('report', 'svasu');
    $strattempt = get_string('attempt', 'svasu');

    $PAGE->set_title("$course->shortname: ".format_string($svasu->name));
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($strreport, new moodle_url('/mod/svasu/report.php', array('id' => $cm->id)));

    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($svasu->name));
    $currenttab = 'reports';
    require($CFG->dirroot . '/mod/svasu/tabs.php');
}

// Open the selected Scorm report and display it.
$classname = "svasureport_{$mode}\\report";
$legacyclassname = "svasu_{$mode}_report";
$report = class_exists($classname) ? new $classname() : new $legacyclassname();
$report->display($svasu, $cm, $course, $download); // Run the report!

// Print footer.

if (empty($noheader)) {
    echo $OUTPUT->footer();
}
