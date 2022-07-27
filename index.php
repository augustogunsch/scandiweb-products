<?php
require 'autoload.php';

use ProductList\Http\Request;
use ProductList\Http\RequestHandler;
use ProductList\Http\Route;

$request = new Request($_SERVER);
$handler = new RequestHandler($request);

$handler->registerRoutes([
    new Route('GET', 'products', ['ProductList\View\Product', 'listAll']),
    new Route('GET', 'add-product', function() { readfile('add-product.html'); }),
    new Route('GET', '', function() { readfile('index.html'); }),
]);

$handler->handle();
