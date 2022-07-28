<?php
require 'autoload.php';

use ProductList\Http\Request;
use ProductList\Http\RequestHandler;
use ProductList\Http\Route;

$request = new Request($_SERVER);
$handler = new RequestHandler($request);

$handler->registerRoutes([
    new Route('GET', 'products', ['ProductList\View\Product', 'list']),
    new Route('DELETE', 'products', ['ProductList\View\Product', 'delete']),
    new Route('GET', 'add-product', function() { readfile('static/add-product.html'); }),
    new Route('GET', '', function() { readfile('static/index.html'); }),
]);

$handler->handle();
