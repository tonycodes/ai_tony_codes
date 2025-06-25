<?php

return [
    /*
    |--------------------------------------------------------------------------
    | License Configuration
    |--------------------------------------------------------------------------
    */
    'license_key' => env('GITFLOW_REPORTER_LICENSE_KEY'),
    'license_server' => env('GITFLOW_REPORTER_LICENSE_SERVER', 'https://api.tonycodes.com/licenses'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Integration
    |--------------------------------------------------------------------------
    */
    'github' => [
        'token' => env('GITFLOW_REPORTER_GITHUB_TOKEN'),
        'owner' => env('GITFLOW_REPORTER_GITHUB_OWNER'),
        'repo' => env('GITFLOW_REPORTER_GITHUB_REPO'),
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'position' => env('GITFLOW_REPORTER_POSITION', 'bottom-right'), // bottom-right, bottom-left, top-right, top-left
        'theme' => env('GITFLOW_REPORTER_THEME', 'auto'), // light, dark, auto
        'show_to_guests' => env('GITFLOW_REPORTER_SHOW_TO_GUESTS', false),
        'show_in_development' => env('GITFLOW_REPORTER_SHOW_IN_DEV', true), // Set to false to hide in all non-production environments
    ],

    /*
    |--------------------------------------------------------------------------
    | Features Configuration
    |--------------------------------------------------------------------------
    */
    'features' => [
        'screenshots' => env('GITFLOW_REPORTER_SCREENSHOTS', true), // Set to false for sites with sensitive content
        'context_collection' => env('GITFLOW_REPORTER_CONTEXT', true),
        'priority_selection' => env('GITFLOW_REPORTER_PRIORITY', true),
        'file_attachments' => env('GITFLOW_REPORTER_ATTACHMENTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Issue Templates
    |--------------------------------------------------------------------------
    */
    'templates' => [
        'bug' => [
            'title_prefix' => '🐛',
            'labels' => ['bug', 'auto-generated'],
            'assignees' => [],
        ],
        'feature' => [
            'title_prefix' => '✨',
            'labels' => ['enhancement', 'auto-generated'],
            'assignees' => [],
        ],
        'improvement' => [
            'title_prefix' => '🔧',
            'labels' => ['enhancement', 'improvement', 'auto-generated'],
            'assignees' => [],
        ],
        'question' => [
            'title_prefix' => '❓',
            'labels' => ['question', 'auto-generated'],
            'assignees' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security & Privacy
    |--------------------------------------------------------------------------
    */
    'security' => [
        'max_file_size' => env('GITFLOW_REPORTER_MAX_FILE_SIZE', 5120), // KB
        'allowed_file_types' => ['png', 'jpg', 'jpeg', 'gif', 'webp'],
        'sanitize_data' => true,
        'exclude_sensitive_data' => true,
        'rate_limit' => env('GITFLOW_REPORTER_RATE_LIMIT', 5), // per hour per user
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'success_message' => 'Issue reported successfully! Thank you for your feedback.',
        'error_message' => 'Failed to submit report. Please try again or contact support.',
        'auto_hide_after' => 5000, // milliseconds
    ],
];