<?php
require 'autoload.php';

use ProductList\Http\Request;
use ProductList\Http\RequestHandler;
use ProductList\Http\Route;

$request = new Request($_SERVER);
$handler = new RequestHandler($request);

$handler->registerRoutes([
    new Route('GET', 'products', ['ProductList\View\Product', 'listAll'])
]);

$handler->setIndex('index.html');

$handler->handle();
