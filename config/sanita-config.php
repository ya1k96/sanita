<?php

return [
    'notification_channel' => 'email', // email, slack, etc.
    'notification_recipients' => [
        'email' => ['admin@example.com'],
        'slack' => ['https://hooks.slack.com/services/...']
    ],

     // Configuración del remitente
     'from_email' => env('SANITA_FROM_EMAIL', 'noreply@example.com'),
     'from_name' => env('SANITA_FROM_NAME', 'Sanita Alerts'),
];
