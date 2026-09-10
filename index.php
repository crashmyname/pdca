<?php

use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\ErrorHandler;
/**
 * ---------------------------------------------------------------
 *  Bpjs Framework - Front Controller
 * ---------------------------------------------------------------
 *  Semua request masuk ke sini dan diteruskan ke Kernel.
 */
define('BPJS_START', microtime(true));
define('BPJS_VERSION','0.2.2');

// ---------------------------------------------------------------
//  Path Definition
// ---------------------------------------------------------------
$baseDir = realpath(__DIR__.'/');
define('BPJS_BASE_PATH',$baseDir);

// ---------------------------------------------------------------
//  Register The Composer Autoloader
// ---------------------------------------------------------------
require BPJS_BASE_PATH . '/vendor/autoload.php';

ErrorHandler::register();
// ---------------------------------------------------------------
//  Bootstrap The Application
// ---------------------------------------------------------------
$app = require BPJS_BASE_PATH . '/bootstrap/app.php';

// ---------------------------------------------------------------
// RUNTIME MODE
// ---------------------------------------------------------------

$runtime = env('APP_RUNTIME','fpm');
if($runtime === 'octane' && php_sapi_name() !== 'cli') {
    $runtime = 'fpm';
}

if($runtime === 'octane'){
    $kernel = $app->make(\Bpjs\Framework\Core\Kernel::class);
    while(true){
        $request = Request::capture();
        try{
            $response = $kernel->handle($request);
            $response->send();
            $kernel->terminate();
        } catch (\Exception $e){
            echo "Fatal Error: ".$e->getMessage();
        }

        Request::flush();
        $kernel->reset();

        // optional
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
    return;
}

// ---------------------------------------------------------------
//  Handle The Incoming Request
// ---------------------------------------------------------------
$kernel = $app->make(\Bpjs\Framework\Core\Kernel::class);

$response = $kernel->handle(
    \Bpjs\Framework\Core\Request::capture()
);

$response->send();

$kernel->terminate();
