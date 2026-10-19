<?php
// test_mac.php

function getMacAddress(string $ip): ?string
{
    if (PHP_OS_FAMILY === 'Windows') {
        $cmd = 'arp -a ' . escapeshellarg($ip);
    } else {
        $cmd = 'arp -n ' . escapeshellarg($ip);
    }

    $output = @shell_exec($cmd);
    if (!$output) return null;

    if (preg_match('/([0-9a-f]{2}[:\-]){5}[0-9a-f]{2}/i', $output, $m)) {
        return strtoupper(str_replace('-', ':', $m[0]));
    }

    return null;
}

// ================== TEST ==================
header('Content-Type: text/plain');

echo "OS         : " . PHP_OS_FAMILY . "\n";
echo "shell_exec : " . (function_exists('shell_exec') ? 'ENABLED' : 'DISABLED') . "\n";

$disabled = ini_get('disable_functions');
echo "disable_fn : " . ($disabled ?: '(none)') . "\n\n";

// IP test — bisa dari GET ?ip=xxx
$ip = $_GET['ip'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

echo "Client IP  : " . $ip . "\n";
echo "MAC result : " . (getMacAddress($ip) ?? '(null)') . "\n\n";

// Tampilkan raw ARP output biar kelihatan
echo "--- RAW ARP OUTPUT ---\n";
if (PHP_OS_FAMILY === 'Windows') {
    $cmd = 'arp -a ' . escapeshellarg($ip);
} else {
    $cmd = 'arp -n ' . escapeshellarg($ip);
}
echo "CMD: $cmd\n";
echo @shell_exec($cmd) ?: "(no output)\n";

// Bonus: tampilkan seluruh ARP table untuk lihat IP mana saja yang ada
echo "\n--- FULL ARP TABLE ---\n";
$fullCmd = PHP_OS_FAMILY === 'Windows' ? 'arp -a' : 'arp -n';
echo @shell_exec($fullCmd) ?: "(no output)\n";