<?php

/**
 * a sample web application using Tuum/Respond.
 */
use Tuum\Respond\Helper\ReqBuilder;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;

if (php_sapi_name() == 'cli-server') {
    $file = __DIR__ . '/' . $_SERVER["REQUEST_URI"];
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}

/** @var Closure $app */

include dirname(__DIR__) . "/app/autoload.php";

/**
 * run web application for the request.
 */
$config = [
    'renderer'      => 'tuum',
    'template-path' => dirname(__DIR__) . '/app/views',
];

$app = include dirname(__DIR__) . '/app/app.php';
$req = ReqBuilder::createFromGlobal($GLOBALS);
$res = (new Response())->withHeader('Content-Type', 'text/html');
$res = $app($req, $res);

$emitter = new SapiEmitter;
$emitter->emit($res);