<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core/includes/autoload.php';
include_once 'config.php';

use src\Routing\Request;
use src\Routing\Router;
use src\Products\Product;
use src\Controllers\ProductController;

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");

    exit(0);
}

$router = new Router(new Request);

$router->get('/', function () {
    return "This is api";
});

$router->get('/products', function () {
    return ProductController::index();
});

$router->get('/productTypes', function () {
    return ProductController::productTypes();
});

$router->post('/checkIsSkuUnique', function ($request) {
    $body = $request->getBody();
    $sku = $body['sku'];
    return ProductController::checkIsSkuUnique($sku);
});

$router->post('/saveProduct', function ($request) {
    $body = $request->getBody();
    $result =  ProductController::store($body);
    return $result;
});

$router->post('/deleteProducts', function ($request) {
    $body = $request->getBody();
    $result =  ProductController::massDelete($body);
    return $result;
});
