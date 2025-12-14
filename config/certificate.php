<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Certificate Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for certificate generation
    |
    */

    'dimensions' => [
        'width' => 842,   // A4 landscape width at 72 DPI
        'height' => 595,  // A4 landscape height at 72 DPI
    ],

    'colors' => [
        'primary' => '#1e40af',      // Blue
        'secondary' => '#3b82f6',    // Light Blue
        'accent' => '#f59e0b',       // Orange
        'dark' => '#1e3a8a',         // Dark Blue
        'text' => '#374151',         // Gray
        'light_text' => '#6b7280',   // Light Gray
    ],

    'fonts' => [
        'regular' => public_path('fonts/arial.ttf'),
        'bold' => public_path('fonts/arial-bold.ttf'),
        'italic' => public_path('fonts/arial-italic.ttf'),
    ],

    'signature' => [
        'title_prefix' => 'Mr.', // or 'Ms.', 'Dr.', etc.
        'position' => 'Course Instructor',
        // Name will be dynamically taken from course instructor
    ],

    'storage' => [
        'disk' => 'public',
        'path' => 'certificates',
    ],

    'number_prefix' => 'CERT-WEBACE-',
];