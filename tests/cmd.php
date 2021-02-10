<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Widevel\SmartlogClient\Log;

Log::error("Oh No!", null, ['Bill Gates']);

$log_instance = new Log;

$log_instance
->setVerbose(true)
->setSenderMethod('cmd')
->setServerCmdPath('D:\htdocs\dev\smartlog\server\includes\command.php')
->sendPendingLogs()
->setInstanceData((object) ['hola' => date('Y-m-d H:i:s')]);

$log_instance->setSessionToken('new_session_token');
$log_instance->setInstanceToken('new_instance_token');


Log::debug("Fix This", null, ['Real Madrid'], (object) ['my_debug_data' => 12345]);
Log::warning("Fire", null, ['Barcelona']);
Log::info("Hello world", null, ['Spain']);
