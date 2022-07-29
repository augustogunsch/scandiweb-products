<?php
namespace ProductList\View;

use ProductList\Http\Request;
use ProductList\Model\Product as ProductModel;
use ProductList\Exception\NotFoundException;

class Product extends View
{
    public static function get(Request $request)
    {
        header('Content-Type: application/json');
        echo json_encode(ProductModel::selectAll());
    }

    public static function delete(Request $request)
    {
        $queryParams = $request->getQueryParams();

        if (array_key_exists('id', $queryParams)) {
            $ids = explode(',', $queryParams['id']);
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
                    echo "The selected(s) object(s) is(are) not available anymore.";
                    return;
                }
            }
        } else {
            http_response_code(400);
            echo 'Missing parameter "id".';
        }
    }

    public static function post(Request $request)
    {
        $params= $request->getFormParams();
        $expected = [
            'sku',
            'name',
            'price',
            'productType',
            'weight',
            'size',
            'height',
            'width',
            'length'
        ];
        if (self::expectArgs($expected, $params)) {
            $params['productId'] = null;
            $params['variationId'] = null;

            $product = ProductModel::fromRow($params);

            $product->insert();
        }
    }
}
