<?php

declare(strict_types=1);

use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {

    // enables Slim to work with JSON or XML payloads
    $app->addBodyParsingMiddleware();

    $app->add(new JwtAuthentication([
        "ignore" => ["/users", "/login"],
        "secret" => $_ENV['JWT_SECRET']
    ]));

};
