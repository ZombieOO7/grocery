<?php

return [
    'websetting' => [
        'logo_extension' => 'jpg,jpeg,png',
        'logo_size' => '2',
        'favicon_extension' => 'svg,png,ico',
        'favicon_size' => '2',
        'url' => true,
        'logo_height' => '100px',
        'logo_width' => '100px',
        'favicon_width' => '100px',
        'favicon_height' => '100px',
        'active_tab' => [
            1 => 'general_tab',
            2 => 'social_media_tab',
            3 => 'meta_tab',
            4 => 'push_notification_tab',
        ],
    ],

    // set oAuthAccessTokens name
    'oAuthAccessTokensName' => 'Ipps',
    'text_length' => 100,
    'content_length' => 500,
    'name_length' => 191,
    'user_name_length' => 50,
    'text_length' => 100,
    'content_length' => 500,
    'password_max_length' => 16,
    'password_min_length' => 6,
    'email_length' => 50,
    'phone_length' => 10,

    // User Type
    'manager' => 1,
    'engineer' => 2,
    'operator' => 3,

    'user' => [
        'folder_name' => 'users',
        'directory_path' => 'public/uploads/user/',
    ],
    'thumb_image_width' => 211,
    'thumb_image_height' => 186,
    'storage_path' => 'public/uploads/',

    // User Type Text
    'user_types' => [
        1 => 'Admin',
        2 => 'Staff',
        3 => 'Operator',
    ],

    //Priority Text
    'priorites' => [
        0 => 'Pending',
        2 => 'Preventive',
        3 => 'Normal',
        4 => 'Urgent',
    ],

    // Job Status
    'job_status_text' => [
        1 => 'JOB REQUEST',
        2 => 'ASSIGNED',
        3 => 'Work Order',
        4 => 'COMPLETED',
        5 => 'DECLINED',
        6 => 'KIV',
        7 => 'Unable To Complete',
    ],

    // Job listing pagination
    'job_page_limit' => 20,

    // FAQ Listing Pagination
    'faq_page_limit' => 5,

    // Email Template Slugs
    'verify_email' => 'verify-your-email',
    'under_review' => 'profile-under-review',
    // status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'print' => 'print',
    // Action
    'delete' => 'Delete',
    'status_inactive_value' => '0',
    'status_active_value' => '1',

    // Account verfied/declined
    'profile_review' => [
        'under_review' => 0,
        'verified' => 1,
        'declined' => 2,
    ],

    // Job image dir
    'job' => [
        'folder_name' => 'jobs',
        'directory_path' => 'public/uploads/jobs',
    ],

    // Job Status list
    'job_status_list' => [
        'job_request' => 1,
        'assigned' => 2,
        'ongoing' => 3,
        'completed' => 4,
        'declined' => 5,
        'kiv' => 6,
        'unable_to_complete' => 7,
    ],

    // Priority Status
    'priorites_text' => [
        0 => 'Pending',
        2 => 'Preventive',
        3 => 'Normal',
        4 => 'Urgent',
    ],
    'priorites_colors' => [
        0 => 'accent',
        2 => 'success',
        3 => 'warning',
        4 => 'danger',
    ],

    'job_status_color' => [
        1 => '#ff7474',
        2 => '#1c216a',
        3 => '#f00',
        4 => '#2c8213',
        5 => '#5a00ff',
        6 => '#065932',
    ],

    'permission_status' => [
        1 => 'On',
        0 => 'Off',
    ],
    'report_type' =>[
        1 => 'By Location Summary',
        2 => 'By Machine Summary',
        3 => 'By Problem Type',
        4 => 'By Every Fitter Summary',
        5 => 'By Individual Fitter',
        6 => 'By Individual Machine',
        7 => 'By Summary of Similar Job',
        // 8 => 'By Decline Work Order',
        // 9 => 'By KIV work Order',
    ],
    'priorites_report_text' => [
        0 => 'Pending',
        2 => 'Preventive',
        3 => 'Normal',
        4 => 'Urgent',
    ],

];
