<?php

namespace App\Jobs;

class SendEmailJob
{
    public function handle(array $data): void
    {
        // Job logic here
        $to = $data['to'] ?? 'default@example.com';
        $subject = $data['subject'] ?? 'Test Email';
        $body = $data['body'] ?? 'This is a test email';
        
        echo "\n📧 Sending Email...\n";
        echo "==============================\n";
        echo "To: {$to}\n";
        echo "Subject: {$subject}\n";
        echo "Body: {$body}\n";
        echo "==============================\n";
        
        // Simulasi pengiriman email (ganti dengan logika email sebenarnya)
        sleep(2); // Simulasi delay
        
        // Log ke file
        $logFile = __DIR__ . '/../../storage/logs/email.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logMessage = sprintf(
            "[%s] Email sent to: %s | Subject: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject
        );
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        echo "✅ Email sent successfully!\n";
    }
}
