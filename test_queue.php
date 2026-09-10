<?php

define('BPJS_BASE_PATH', __DIR__);
require_once __DIR__ . '/vendor/autoload.php';

use Bpjs\Framework\Helpers\Queue;

// Load environment
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

loadEnv(__DIR__ . '/.env');

// Push ke queue 'emails'
Queue::push(
    jobClass: 'App\Jobs\SendEmailJob',
    data: [
        'to' => 'user@example.com',
        'subject' => 'Test Email',
        'body' => 'This is a test'
    ],
    queue: 'emails',  // Nama queue
    delay: 0
);

// Push ke queue 'notifications'
Queue::push(
    jobClass: 'App\Jobs\SendNotificationJob',
    data: [
        'user_id' => 123,
        'message' => 'You have a new notification'
    ],
    queue: 'notifications',  // Nama queue berbeda
    delay: 0
);

// Push ke queue 'high-priority'
Queue::push(
    jobClass: 'App\Jobs\ProcessPaymentJob',
    data: [
        'order_id' => 456,
        'amount' => 100000
    ],
    queue: 'high-priority',  // Queue prioritas tinggi
    delay: 0
);

echo "✅ Jobs pushed to different queues!\n";