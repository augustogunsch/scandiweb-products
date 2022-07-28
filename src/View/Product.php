<?php
namespace ProductList\View;

use ProductList\Http\Request;
use ProductList\Model\Product as ProductModel;
use ProductList\Model\DVD;
use ProductList\Model\Furniture;
use ProductList\Model\Book;
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
                    echo $e->getMessage();
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
            $product = null;
            $type = $params['productType'];

            switch($type) {
                case 'dvd':
                    $product = new DVD(
                        $params['sku'],
                        $params['name'],
                        $params['price'],
                        $params['size'],
                    );
                    break;
                case 'furniture':
                    $product = new Furniture(
                        $params['sku'],
                        $params['name'],
                        $params['price'],
                        $params['height'],
                        $params['width'],
                        $params['length'],
                    );
                    break;
                case 'book':
                    $product = new Book(
                        $params['sku'],
                        $params['name'],
                        $params['price'],
                        $params['weight'],
                    );
                    break;
                default:
                    http_response_code(400);
                    echo "Invalid 'productType' value '$type'";
                    return;
            }

            //try {
                $product->insert();
            //} catch (\Exception $e) {

            //}
        }
    }

    public static function test(Request $request)
    {
        $params= $request->getQueryParams();
        if (self::expectArgs(['testarg'], $params)) {
            echo var_dump($params);
        }
    }
}
