<?php

/**
 *------------------------------------------------------------------------------
 * Start your Engines
 *------------------------------------------------------------------------------
 *
 * This is the start of the app. We create a new instance, with its base path at
 * the root of the project.
 *
 * Then we bootstrap the Kernels and the Exception handlers.
 *
 */

$app = new Core\Application\Application(
    realpath(__DIR__ . '/../')
);

/**
 *------------------------------------------------------------------------------
 * Bind Important Interfaces
 *------------------------------------------------------------------------------
 *
 * Next, we need to bind some important interfaces into the container so
 * we will be able to resolve them when needed. The kernels serve the
 * incoming requests to this application from both the web and CLI.
 *
 */

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    Core\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Core\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Core\Exceptions\Handler::class
);

/**
 *------------------------------------------------------------------------------
 * Return The Application
 *------------------------------------------------------------------------------
 *
 * This script returns the application instance. The instance is given to
 * the calling script so we can separate the building of the instances
 * from the actual running of the application and sending responses.
 *
 */

return $app;
