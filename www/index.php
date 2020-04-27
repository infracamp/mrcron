<?php

namespace App;


use Phore\Log\Logger\PhoreSyslogLoggerDriver;
use Phore\Log\PhoreLogger;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app->activateExceptionErrorHandlers();
$app->setOnExceptionHandler($exh = new JsonExceptionHandler());
$app->acl->addRule(aclRule("*")->ALLOW());
$logger = new NullLogger();
if (CONF_SYSLOG_HOST !== null) {
    $exh->setLogger($logger = new PhoreLogger(new PhoreSyslogLoggerDriver(CONF_SYSLOG_HOST, LogLevel::NOTICE)));
    //$logger->notice("Request from: '{$_SERVER["REMOTE_ADDR"]}' : {$_SERVER["REQUEST_METHOD"]} '{$_SERVER["REQUEST_URI"]}'");

}


require __DIR__ . "/../app/di.php";
require __DIR__ . "/../app/route.php";

/**
 ** Run the application
 **/
$app->serve();
