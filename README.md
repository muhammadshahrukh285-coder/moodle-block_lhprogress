# Learning Progress

A Moodle block that gives learners a visual overview of their learning journey, including course completion progress, course status, due dates, overdue tracking, and enrollment information.

## Features

- Displays an overall learning progress percentage.
- Shows active and completed course counts.
- Displays courses based on their completion status:
  - Completed
  - In Progress
  - Not Started
  - Due Soon
  - Overdue
- Calculates course progress using Moodle's course completion API.
- Provides a visual course progress bar.
- Provides a time-based learning timeline for courses with due-date tracking enabled.
- Calculates due dates based on the learner's enrollment date.
- Supports configurable due, overdue, and grace-period settings.
- Allows administrators to filter courses by course category.
- Supports including courses from child/subcategories.
- Allows administrators to control which course statuses are displayed.
- Allows administrators to control the display of:
  - Enrollment date
  - Due date
  - Progress percentage
  - Browse Courses link
- Provides a "Continue Learning" action for learners.
- Provides a link to browse all courses.
- Uses Moodle language strings for learner-facing text.
- Implements Moodle's Privacy API with no personal data stored by the plugin.

## Requirements

- Moodle 4.5 or later
- PHP version supported by the installed Moodle version
- Moodle course completion enabled where course progress tracking is required

## Installation

1. Download the plugin.
2. Extract the plugin into:

   ```text
   moodle/blocks/lhprogress

Visit:

Site administration > Notifications
Complete the Moodle upgrade process.
Add the Learning Progress block to the learner's Dashboard/My page.
Configuration

The plugin settings are available under:

Site administration > Plugins > Blocks > Learning Progress
Course Tracking

Administrators can:

Track all course categories.
Select a specific course category.
Include or exclude courses from child categories.
Due Date Settings

Due-date tracking can be enabled or disabled.

When enabled, administrators can configure:

Number of days after enrollment before a course becomes due.
Number of days after enrollment when the overdue period begins.
Additional grace-period days.

The plugin calculates these dates based on the learner's course enrollment date.

Course Status Display

Administrators can control whether the following course statuses are displayed:

Completed
In Progress
Not Started
Due Soon
Overdue
Display Options

Administrators can control whether the following information is displayed:

Enrollment date
Due date
Progress percentage
Browse Courses link
Due-Date Timeline

When due-date tracking is enabled, the course card can display a visual timeline representing the learner's progress through the configured enrollment-to-final-overdue period.

The timeline includes:

Enrollment
Due
Overdue
Final

The timeline is calculated independently for each enrolled course using the learner's enrollment date and the configured due-date settings.

Course Progress

Course completion progress is retrieved using Moodle's course completion API.

Courses with 100% completion are treated as completed. Courses below 100% completion contribute to the active-course count.

The overall progress calculation is based on the learner's tracked courses and is calculated independently of the administrator's course-status display filters.

Privacy

The plugin does not create or store personal data of its own.

Course and learner information displayed by the block is retrieved from Moodle's existing enrolment and course completion data.

The plugin implements Moodle's Privacy API using a null provider.

Compatibility

The plugin is developed for Moodle 4.5 and later Moodle versions compatible with the plugin's declared Moodle requirement.

License

This plugin is licensed under the GNU General Public License v3 or later.

See:

https://www.gnu.org/licenses/

Support

Issues, bug reports, and feature requests can be submitted through the project's GitHub repository:

https://github.com/muhammadshahrukh285-coder/moodle-block_lhprogress
