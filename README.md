# Moodle Learning Progress Block

A configurable Moodle block that provides learners with a clear, visual overview of their enrolled courses, course completion progress, enrollment dates, due-date timelines, and overdue status.

The block is designed for Moodle environments where administrators need flexible control over which courses are tracked and how learning progress and time-based status information is presented to learners.

## Plugin Information

| Item | Details |
|---|---|
| Plugin Name | Learning Progress |
| Component | `block_lhprogress` |
| Plugin Type | Moodle Block |
| Moodle Version | Moodle 4.5+ |
| PHP | PHP 8.1+ |
| License | GNU GPL v3 or later |

---

## Overview

The Learning Progress block provides a centralized learner-facing view of course progress.

Instead of requiring learners to navigate through multiple Moodle pages to understand their learning status, the block presents relevant information in a compact dashboard-style interface.

The block can display:

- Overall learning progress
- Active courses
- Completed courses
- Individual course progress
- Course enrollment dates
- Calculated due dates
- Due-soon status
- Overdue status
- Overdue grace periods
- Time-based progress toward course deadlines
- Course category filtering
- Subcategory-based tracking
- Configurable status visibility
- Configurable information display
- Direct access to the learner's courses

---

# Features

## 1. Overall Learning Progress

The block calculates and displays an overall progress percentage based on the tracked enrolled courses.

The learner can quickly see:

- Overall progress percentage
- Number of active courses
- Number of completed courses

The overall progress provides a high-level view before the learner reviews individual courses.

---

## 2. Individual Course Progress

Each tracked course displays its Moodle completion progress.

The course progress is calculated using Moodle's course completion API.

Example states include:

- `0%` — Not Started
- Partial completion — In Progress
- `100%` — Completed

The progress percentage and progress bar can be enabled or disabled through the administrator settings.

---

## 3. Enrollment-Based Due Dates

Due dates are calculated individually for each learner based on their enrollment date.

This means the block does not rely on a single fixed calendar deadline for everyone.

For example, if an administrator configures:

```text
Due After: 14 days

and a learner enrolls on:

1 September

their calculated due date becomes:

15 September

Another learner who enrolls later receives a different calculated due date.

This makes the block suitable for rolling learning programs where learners may enroll at different times.

4. Overdue Tracking

Administrators can configure when the overdue period begins.

For example:

Due After:       14 days
Overdue After:   30 days
Grace Period:     7 days

For a learner enrolled on 1 September:

Enrollment
    ↓
1 September

Due
    ↓
15 September

Overdue period begins
    ↓
1 October

Final overdue date
    ↓
8 October

The final overdue date is calculated using:

Enrollment Date
+ Overdue After
+ Grace Period
5. Overdue Grace Period

The block supports a configurable grace period between the beginning of the overdue period and the final overdue state.

During the grace period, the learner can see a separate visual state.

This provides a distinction between:

Normal learning period
Due period
Grace period
Final overdue state

The grace period can also be configured to zero days when no additional grace period is required.

6. Due Soon Tracking

Courses approaching their calculated due date can be displayed as Due Soon.

The block provides a visual distinction for courses approaching their deadline.

Due-soon tracking is based on the configured due date and the block's due-soon window.

7. Time-Based Learning Timeline

In addition to course completion progress, the block provides a separate timeline showing the learner's progress through the available learning period.

This is different from course completion.

For example:

Course Progress
████████░░░░░░░░ 50%

Time Progress
███████░░░░░░░░░ 40%

This allows learners to understand both:

How much of the course they have completed
How much of their available learning period has elapsed

The timeline can display milestone indicators for:

Enrollment
Due date
Overdue threshold
Final overdue point
8. Course Category Filtering

Administrators can select a specific Moodle course category for tracking.

The block can therefore be configured to track:

All categories
A specific category

This is useful for organizations that have multiple Moodle course categories but only want the Learning Progress block to track a particular learning program or business area.

9. Subcategory Support

Administrators can enable or disable subcategory tracking.

When enabled, courses belonging to child categories of the selected category can also be included.

This allows an administrator to select a parent category and track the learning courses contained within its category hierarchy.

10. Configurable Status Display

Administrators can control which course statuses are displayed.

Available status display options include:

Completed
In Progress
Not Started
Due Soon
Overdue

This allows organizations to customize the learner experience according to their learning-program requirements.

11. Configurable Display Options

Administrators can independently control the information displayed for learners.

Available display settings include:

Enrollment Date
Due Date
Progress Percentage
Browse Courses Link

This makes it possible to create a simplified learner dashboard or a more detailed progress view.

12. Master Due-Date Control

Administrators can enable or disable the complete due-date tracking system.

When due-date tracking is disabled:

Due-date calculations are disabled
Overdue calculations are disabled
Grace-period calculations are disabled
Time-based tracking is disabled
Due/overdue indicators are not displayed
Due/overdue statistics are not displayed

Course completion tracking continues to operate independently.

This allows the block to be used either as:

Progress-only dashboard

or:

Progress + learning timeline dashboard
Administrator Configuration

The block provides an administrator configuration page containing the following sections.

Course Tracking
Course Category

Select the Moodle course category that should be tracked.

Available option:

All categories

or an individual Moodle category.

Include Subcategories

Controls whether courses from child categories are included.

Due Date Settings
Enable Due Dates

Master switch for due-date and overdue tracking.

Due After

Defines the number of days after enrollment when the course becomes due.

Example:

Due After = 14

means the course becomes due 14 days after the learner's enrollment date.

Overdue After

Defines the number of days after enrollment when the overdue period begins.

Example:

Overdue After = 30

means the overdue period begins 30 days after enrollment.

Overdue Grace Period

Defines the additional number of days before the course reaches its final overdue state.

Example:

Overdue After = 30
Grace Period = 7

results in the final overdue point being 37 days after enrollment.

Course Status Display Settings

Administrators can control whether the following course states are displayed:

Completed
In Progress
Not Started
Due Soon
Overdue

This allows the block to be adapted to different learning-program requirements.

Display Settings

Administrators can independently control:

Enrollment Date
Due Date
Progress Percentage
Browse Courses Link

Only enabled information is presented to learners.

Performance and Course Selection

The block is designed to avoid retrieving every Moodle course and filtering the results in PHP.

Course selection is performed using Moodle database queries with category and enrollment conditions.

The course query considers:

User enrollment
Active enrollments
Course visibility
Selected course category
Category hierarchy
Exclusion of the Moodle site course

This approach is intended to reduce unnecessary processing in Moodle environments with a large number of courses.

Course Completion

Course progress is obtained using Moodle's course completion functionality.

The block uses Moodle's completion API to determine the learner's course progress.

Completion states are therefore based on Moodle's configured course completion criteria rather than a separate progress calculation system.

Status Logic

The block evaluates course status using the following general priority:

Completed
    ↓
Overdue
    ↓
Grace Period
    ↓
Due
    ↓
Due Soon
    ↓
In Progress / Not Started

Completed courses take priority over time-based states.

For example, if a learner completes a course after its calculated due date, the course is treated as completed rather than continuing to display an active overdue state.

Time Progress Calculation

Time progress is calculated using the learner's enrollment date and the configured final overdue point.

Conceptually:

Time Progress =
Elapsed Time / Total Available Time × 100

The value is constrained between:

0% – 100%

The timeline also calculates the relative position of:

Due date
Overdue threshold
Final overdue date

This allows the learner interface to display these milestones visually.

Learner Interface

The learner-facing block provides a compact dashboard containing:

Header

Displays the Learning Progress title and learning overview.

Progress Overview

Displays:

Overall progress
Active courses
Completed courses
Course List

Each tracked course can display:

Course name
Course progress
Progress percentage
Enrollment date
Due date
Current status
Time progress
Timeline milestones
Course Navigation

A learner can select the course to continue learning.

The block can also display a Browse Courses link when enabled by the administrator.

Installation
Method 1 — Moodle Plugin Installation

Copy the plugin directory to:

moodle/blocks/lhprogress

The final directory structure should be:

moodle/
└── blocks/
    └── lhprogress/
        ├── block_lhprogress.php
        ├── version.php
        ├── settings.php
        ├── styles.css
        ├── db/
        │   └── access.php
        ├── lang/
        │   └── en/
        │       └── block_lhprogress.php
        └── templates/
            └── progress.mustache

Then visit the Moodle administration interface.

Moodle should detect the new block and perform the required plugin installation or upgrade process.

Adding the Block

After installation:

Log in as an administrator.
Open the desired Moodle dashboard or page.
Turn editing on.
Add a block.
Select Learning Progress.
Configure the block according to the required learner experience.
Administrator Settings

The administrator configuration can be accessed through Moodle's block administration settings.

The configuration allows administrators to define:

Course category
Include subcategories
Enable due dates
Due after
Overdue after
Overdue grace period

Show completed
Show in progress
Show not started
Show due soon
Show overdue

Show enrollment date
Show due date
Show progress percentage
Show browse courses link
Example Configuration

A rolling learning program could use:

Course Category:
Leadership Development

Include Subcategories:
Yes

Enable Due Dates:
Yes

Due After:
14 days

Overdue After:
30 days

Grace Period:
7 days

A learner enrolled on:

1 September

would have:

Enrollment:
1 September

Due:
15 September

Overdue threshold:
1 October

Final overdue:
8 October
Example Learner States
Not Started
Course Progress: 0%

Status:
Not Started
In Progress
Course Progress: 50%

Status:
In Progress
Due Soon
Course Progress: 60%

Time Progress:
Approaching due date

Status:
Due Soon
Grace Period
Course Progress: 75%

Time Progress:
Grace Period

Status:
Grace Period
Overdue
Course Progress: 80%

Time Progress:
Overdue

Status:
Overdue
Completed
Course Progress: 100%

Status:
Completed

Completed courses do not continue displaying the time-tracking progress bar.

Technical Architecture

The plugin follows the standard Moodle block architecture.

Main component:

block_lhprogress

Primary class:

block_lhprogress

The block extends Moodle's:

block_base

The learner interface is rendered using a Moodle Mustache template.

File Structure
lhprogress/
│
├── block_lhprogress.php
│
├── db/
│   └── access.php
│
├── lang/
│   └── en/
│       └── block_lhprogress.php
│
├── templates/
│   └── progress.mustache
│
├── styles.css
│
├── settings.php
│
└── version.php
block_lhprogress.php

Contains the main block class and course tracking logic.

Responsible for:

Retrieving configuration
Selecting tracked courses
Calculating course progress
Calculating enrollment-based dates
Determining course states
Calculating time progress
Preparing template data
settings.php

Defines the administrator configuration options.

templates/progress.mustache

Contains the learner-facing HTML structure.

styles.css

Contains the visual presentation and responsive styling.

lang/en/block_lhprogress.php

Contains Moodle language strings.

db/access.php

Defines the block capability required for adding the block.

version.php

Contains Moodle plugin version and compatibility information.

Security Considerations

The plugin uses Moodle's existing authentication and user context.

Course information is retrieved for the currently authenticated Moodle user.

The block does not require storing external credentials or API keys.

The plugin does not introduce a separate user database.

Data and Privacy

The block uses Moodle's existing course enrollment and completion information.

It does not require a separate external database.

The information displayed to a learner is based on that learner's Moodle enrollment and course completion data.

Organizations deploying the plugin should configure Moodle's existing privacy and data-retention policies according to their requirements.

Compatibility

The plugin is developed for modern Moodle environments and is currently intended for:

Moodle 4.5+
PHP 8.1+

Compatibility with other Moodle versions should be tested before deployment.

Use Cases

The Learning Progress block can be used for:

Employee learning programs
Mandatory training
Compliance learning
New-joiner programs
Leadership development
Product knowledge programs
Certification pathways
Structured learning journeys
Internal academies
Rolling training programs
Self-paced eLearning
Department-specific learning programs
Example Organizational Workflow

An organization may create a category:

Employee Development

with subcategories:

Employee Development
├── New Joiner
├── Leadership
├── Compliance
├── Product Knowledge
└── Professional Skills

The administrator can select the parent category and enable:

Include Subcategories

The Learning Progress block can then track courses across the category hierarchy while applying the configured progress and time-based rules.

Development

The plugin is maintained as a standalone Moodle block.

Repository structure follows Moodle's standard block plugin architecture.

Component:

block_lhprogress

Repository:

moodle-block_lhprogress
Contributing

Contributions, improvements, bug reports, and suggestions are welcome.

When submitting an issue or pull request, please include:

Moodle version
PHP version
Plugin version
Steps to reproduce the issue
Expected behavior
Actual behavior
Relevant error messages or logs where applicable

Avoid including:

User passwords
API keys
SMTP credentials
Personal learner information
Production database exports
Reporting Issues

When reporting an issue, provide enough information to reproduce the problem without exposing sensitive Moodle data.

A useful issue report should include:

Moodle Version:
PHP Version:
Plugin Version:

Configuration:

Steps to Reproduce:

Expected Result:

Actual Result:

Error Message:
Roadmap

Potential future improvements may include:

Additional progress visualization options
More configurable timeline states
Additional reporting capabilities
Expanded administrator controls
Additional language translations
Further performance optimization
Additional Moodle compatibility testing

The roadmap may change as the plugin develops.

License

This project is intended to be distributed under the GNU General Public License version 3 or later.

See the LICENSE file for the full license text.

Author

Developed by Muhammad Shahrukh.

The project focuses on Moodle LMS development, learning technology, eLearning, training automation, and digital learning platforms.
