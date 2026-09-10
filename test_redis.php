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

echo "🔍 Testing Redis Queue\n";
echo str_repeat('=', 50) . "\n\n";

// Cek engine
$engine = Queue::engine();
echo "✅ Queue engine: {$engine}\n\n";

if ($engine !== 'redis') {
    echo "❌ Engine bukan Redis. Cek .env QUEUE_ENGINE=redis\n";
    exit(1);
}

// Test Redis connection
try {
    // Push job ke Redis
    echo "📤 Pushing test job...\n";
    Queue::push(
        jobClass: 'App\Jobs\SendEmailJob',
        data: [
            'to' => 'test@example.com',
            'subject' => 'Redis Test',
            'body' => 'Testing Redis queue'
        ],
        queue: 'emails',
        delay: 0
    );
    echo "✅ Job pushed to Redis\n\n";
    
    // Cek queue size
    echo "📊 Queue size:\n";
    $size = Queue::size('emails');
    echo "   Pending: {$size['pending']}\n";
    echo "   Processing: {$size['processing']}\n";
    echo "   Done: {$size['done']}\n";
    echo "   Failed: {$size['failed']}\n\n";
    
    // Pop job
    echo "📥 Popping job...\n";
    $job = Queue::pop('emails');
    
    if ($job) {
        echo "✅ Job popped:\n";
        echo "   ID: {$job->id}\n";
        echo "   Queue: {$job->queue}\n";
        echo "   Status: {$job->status}\n";
        echo "   Attempts: {$job->attempts}\n\n";
        
        // Process job
        echo "🔄 Processing job...\n";
        $payload = json_decode($job->payload, true);
        echo "   Job Class: {$payload['job']}\n";
        echo "   Data: " . json_encode($payload['data']) . "\n\n";
        
        // Mark as done
        Queue::done($job->id);
        echo "✅ Job marked as done\n";
    } else {
        echo "❌ No job available\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Redis error: " . $e->getMessage() . "\n";
    echo "\n💡 Pastikan Redis sudah berjalan:\n";
    echo "   Windows: Cek Redis di Laragon atau service\n";
    echo "   Linux: sudo systemctl start redis\n";
}