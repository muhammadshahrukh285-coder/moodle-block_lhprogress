<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'block/lhprogress:myaddinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],
];
