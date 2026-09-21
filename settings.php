<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 or later.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    /*
     * Course tracking.
     */
    $settings->add(new admin_setting_heading(
        'block_lhprogress/trackingheading',
        get_string('trackingheading', 'block_lhprogress'),
        ''
    ));

    $categoryoptions = [
        0 => get_string('allcategories', 'block_lhprogress'),
    ];

    $categories = core_course_category::get_all();

    foreach ($categories as $category) {
        $categoryoptions[$category->id] = $category->get_nested_name(false);
    }

    $settings->add(new admin_setting_configselect(
        'block_lhprogress/coursecategory',
        get_string('coursecategory', 'block_lhprogress'),
        get_string('coursecategory_desc', 'block_lhprogress'),
        0,
        $categoryoptions
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/includesubcategories',
        get_string('includesubcategories', 'block_lhprogress'),
        get_string('includesubcategories_desc', 'block_lhprogress'),
        1
    ));

    /*
     * Due and overdue settings.
     */
    $settings->add(new admin_setting_heading(
        'block_lhprogress/dueheading',
        get_string('dueheading', 'block_lhprogress'),
        ''
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/enableduedates',
        get_string('enableduedates', 'block_lhprogress'),
        get_string('enableduedates_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'block_lhprogress/dueafter',
        get_string('dueafter', 'block_lhprogress'),
        get_string('dueafter_desc', 'block_lhprogress'),
        14,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'block_lhprogress/overdueafter',
        get_string('overdueafter', 'block_lhprogress'),
        get_string('overdueafter_desc', 'block_lhprogress'),
        30,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'block_lhprogress/overduegrace',
        get_string('overduegrace', 'block_lhprogress'),
        get_string('overduegrace_desc', 'block_lhprogress'),
        7,
        PARAM_INT
    ));

    /*
     * Course status display.
     */
    $settings->add(new admin_setting_heading(
        'block_lhprogress/statusheading',
        get_string('statusheading', 'block_lhprogress'),
        ''
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showcompleted',
        get_string('showcompleted', 'block_lhprogress'),
        get_string('showcompleted_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showinprogress',
        get_string('showinprogress', 'block_lhprogress'),
        get_string('showinprogress_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/shownotstarted',
        get_string('shownotstarted', 'block_lhprogress'),
        get_string('shownotstarted_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showduesoon',
        get_string('showduesoon', 'block_lhprogress'),
        get_string('showduesoon_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showoverdue',
        get_string('showoverdue', 'block_lhprogress'),
        get_string('showoverdue_desc', 'block_lhprogress'),
        1
    ));

    /*
     * Display options.
     */
    $settings->add(new admin_setting_heading(
        'block_lhprogress/displayheading',
        get_string('displayheading', 'block_lhprogress'),
        ''
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showenrollmentdate',
        get_string('showenrollmentdate', 'block_lhprogress'),
        get_string('showenrollmentdate_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showduedate',
        get_string('showduedate', 'block_lhprogress'),
        get_string('showduedate_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showprogress',
        get_string('showprogress', 'block_lhprogress'),
        get_string('showprogress_desc', 'block_lhprogress'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_lhprogress/showbrowsecourses',
        get_string('showbrowsecourses', 'block_lhprogress'),
        get_string('showbrowsecourses_desc', 'block_lhprogress'),
        1
    ));
}
