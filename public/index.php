<?php
/**
 * Crowfeather - A PHP Management Systems Framework
 *
 * @package  codrasil/crowfeather
 * @author   John Lioneil P. Dionisio <lioneil@codrasil.io>
 *
 */

define('PLUMA_START', microtime(true));

/**
 *------------------------------------------------------------------------------
 * Register The Auto Loader
 *------------------------------------------------------------------------------
 *
 * Composer provides a convenient, automatically generated class loader for
 * our application. Code below will require it into the script here so that
 * we don't have to worry about manual loading any of our classes later on.
 *
 */

require __DIR__.'/../bootstrap/autoload.php';

/**
 *------------------------------------------------------------------------------
 * Turn On The Lights
 *------------------------------------------------------------------------------
 *
 * We need to illuminate PHP development, so let us turn on the lights.
 * This bootstraps the framework and gets it ready for use, then it
 * will load up this application so that we can run it and send
 * the responses back to the browser and delight our users.
 *
 */

$app = require_once __DIR__.'/../bootstrap/app.php';

/**
 *------------------------------------------------------------------------------
 * Application
 *------------------------------------------------------------------------------
 *
 * Once we have the application, we can handle the incoming request
 * through the kernel, and send the associated response back to
 * the client's browser allowing them to enjoy the creative
 * and wonderful application we have prepared for them.
 *
 */

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
