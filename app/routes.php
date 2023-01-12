<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\UserController;
use App\Controller\StockQuoteResearchController;
use Slim\App;

return function (App $app) {
    // create new user
    $app->post('/users', UserController::class . ':create');
    
    // login
    $app->post('/login', AuthController::class . ':login');

    // get stock quote
    $app->get('/stock', StockQuoteResearchController::class . ':getInfoByStockCode');

    // get history
    $app->get('/history', StockQuoteResearchController::class . ':getHistory');
};
