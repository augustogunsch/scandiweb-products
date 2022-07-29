<?php
require 'autoload.php';
ini_set('display_errors', true);
ini_set('error_log', '/tmp/php.log');

use ProductList\Http\Request;
use ProductList\Http\RequestHandler;
use ProductList\Http\Route;

$request = new Request($_SERVER, $_GET, $_POST);
$handler = new RequestHandler($request);

$handler->registerRoutes([
    new Route('GET', 'test', ['ProductList\View\Product', 'test']),
    new Route('GET', 'product', ['ProductList\View\Product', 'get']),
    new Route('DELETE', 'product', ['ProductList\View\Product', 'delete']),
    new Route('POST', 'product', ['ProductList\View\Product', 'post']),
    new Route('GET', 'add-product', function() { readfile('static/add-product.html'); }),
    new Route('GET', '', function() { readfile('static/index.html'); }),
]);

$handler->handle();
