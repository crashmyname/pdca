<?php

use App\Controllers\TaskController;
use Bpjs\Framework\Helpers\Api;
use Bpjs\Framework\Helpers\Response;
use Bpjs\Framework\Helpers\Session;

Api::get('/tasks/stats',  [TaskController::class, 'stats']);