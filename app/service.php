<?php
use App\Config\Define\GuardConfig;
use App\Config\Define\NotFoundFactory;
use App\Config\Define\ResponderFactory;
use Slim\DefaultServicesProvider;
use Tuum\Slimmed\Container;
use Tuum\Builder\AppBuilder;
use Tuum\Respond\Responder;

/** @var AppBuilder $builder */

/**
 * settings for Slim's Container.
 */

/** @var Container $container */
$container = $builder->get('container');

$container['notFoundHandler'] = new NotFoundFactory();
$container['csrf'] = new GuardConfig();
$container[Responder::class] = new ResponderFactory();

$setting = [
    'httpVersion' => '1.1',
    'responseChunkSize' => 4096,
    'outputBuffering' => 'append',
    'determineRouteBeforeAppMiddleware' => false,
    'displayErrorDetails' => false,
];
if ($builder->debug) {
    $setting['displayErrorDetails'] = true;
}

$container['settings'] = $setting;

$defaults = new DefaultServicesProvider();
$defaults->register($container);