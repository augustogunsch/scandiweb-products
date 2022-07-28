<?php
namespace ProductList\View;

use ProductList\Http\Request;
use ProductList\Model\Product as ProductModel;
use ProductList\Exception\NotFoundException;

class Product
{
    public static function list(Request $request)
    {
        echo json_encode(ProductModel::selectAll());
    }

    public static function delete(Request $request)
    {
        $queryString = $request->getQueryString();

        if (array_key_exists('id', $queryString)) {
            $ids = explode(',', $queryString['id']);
            $ids = array_map('intval', $ids);

            foreach($ids as $id) {
                try {
                    $product = ProductModel::fromId($id);

                    try {
                        $product->delete();
                    } catch (\Exception $e) {
                        http_response_code(500);
                        echo $e->getMessage();
                        return;
                    }

                } catch (NotFoundException $e) {
                    http_response_code(404);
                    echo $e->getMessage();
                    return;
                }
            }
        } else {
            http_response_code(400);
            echo 'Missing parameter "id".';
        }
    }

    public static function add(Request $request)
    {
    }
}
