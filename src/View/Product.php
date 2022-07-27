<?php
namespace ProductList\View;

use ProductList\Http\Request;
use ProductList\Model\Product as ProductModel;

class Product
{
    public static function listAll(Request $request)
    {
        echo json_encode(ProductModel::selectAll());
    }
}
