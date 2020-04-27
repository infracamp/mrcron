<?php


namespace App;

use Phore\MicroApp\Response\JsonResponse;
use PHPUnit\Util\Json;

/* @var $app \Phore\MicroApp\App */


$app->router->onGet("/mock/mrcron.json", function () {

   return new JsonResponse([
       "jobs" => [
            [
                "id" => "testdrive",
                "cron" => "* * * * *",
                "urls" => [
                    "http://localhost/mock/test"
                ]
            ]
       ]
   ]);
});


$app->router->onGet("/mock/test", function () {
    file_put_contents("/tmp/_test_run", time());
    return new JsonResponse(["success"=>true]);
});

$app->router->onGet("/", function () {
    return new JsonResponse(["success"=>true, "msg" => "Mr. Cron ready", "version" => VERSION_INFO]);
});
