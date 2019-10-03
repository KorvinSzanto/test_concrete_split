<?php

/**
 * -----------------------------------------------------------------------------
 * Generated 2014-12-18T19:51:35+00:00.
 *
 * @item      files.allowed_types
 * @group     conversations
 * @namespace null
 * -----------------------------------------------------------------------------
 */
return [
    'attachments_enabled' => true,
    'attachments_pending_file_set' => 'Conversation Messages (Pending)',
    'attachments_file_set' => 'Conversation Messages',
    'subscription_enabled' => false,
    'files' => [
        'allowed_types' => '*.jpg;*.gif;*.jpeg;*.png;*.doc;*.docx;*.zip',
        'guest' => [
            'max_size' => 1,
            'max' => 3,
        ],
        'registered' => [
            'max_size' => 10,
            'max' => 5,
        ],
    ],
];
