<?php

return [
    'notification_channel' => 'email', // email, slack, etc.
    'notification_recipients' => [
        'email' => ['admin@example.com'],
        'slack' => ['https://hooks.slack.com/services/...']
    ],
];
