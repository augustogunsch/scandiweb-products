<?php
namespace ProductList\Model;

use ProductList\Exception\NotFoundException;
use ProductList\Exception\InvalidFieldException;
use ProductList\Exception\DuplicateException;

abstract class Product implements \JsonSerializable
{
    use Model;
    private $variationId;
    private $sku;
    private $name;
    private $price;
    private $productId;

    public function __construct(
        $sku,
        $name,
        $price,
        $productId = null,
        $variationId = null
    ) {
        if (empty($sku)) {
            throw new InvalidFieldException('SKU');
        }

        if (empty($name)) {
            throw new InvalidFieldException('Name');
        }

        if (empty($price) || !is_numeric($price)) {
            throw new InvalidFieldException('Price');
        }

        $this->productId = $productId;
        $this->variationId = $variationId;


        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
    }

    public function getSKU()
    {
        return $this->sku;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getVariationId()
    {
        return $this->variationId;
    }

    public function setVariationId($id)
    {
        $this->variationId = $id;
    }

    public function setProductId($id)
    {
        $this->productId = $id;
    }

    abstract public function getFormatedAttr();

    public function delete($conn = null)
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        $productId = $this->getProductId();
        $stmt = $conn->prepare('DELETE FROM '.PRODUCT.' WHERE id = ?');
        $stmt->bind_param('i', $productId);

        if ($stmt->execute() === false) {
            throw new \Exception("Unable to delete product with id '$productId'");
        }
    }

    public static function fromId($id, $conn = null) : self
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        $stmt = $conn->prepare(self::getSelectAllQuery().' WHERE '.PRODUCT.'.id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute() === true) {
            $row = $stmt->get_result()->fetch_assoc();

            if($row === null) {
                throw new NotFoundException("No product with id '$id'");
            }

            return self::fromRow($row);
        } else {
            throw new \Exception("Unable to select object");
        }
    }

    public static function fromRow($row) : self
    {
        switch ($row['productType']) {
            case 'dvd':
                return DVD::fromRow($row);
            case 'book':
                return Book::fromRow($row);
            case 'furniture':
                return Furniture::fromRow($row);
            default:
                throw new \Exception("Product without a type");
        }
    }

    private static function getSelectAllQuery() : string
    {
        return '
        SELECT
                '.PRODUCT.'.id as productId,
                name,
                sku,
                price,
                size,
                weight,
                width,
                height,
                length,
                COALESCE('.DVD.'.id, '.BOOK.'.id, '.FURNITURE.'.id) as variationId,
                CASE
                        WHEN '.DVD.'.id IS NOT NULL THEN "dvd"
                        WHEN '.BOOK.'.id IS NOT NULL THEN "book"
                        WHEN '.FURNITURE.'.id IS NOT NULL THEN "furniture"
                END as productType
        FROM
                '.PRODUCT.'
        LEFT JOIN '.DVD.' ON
                '.PRODUCT.'.id = '.DVD.'.product_id
        LEFT JOIN '.BOOK.' ON
                '.PRODUCT.'.id = '.BOOK.'.product_id
        LEFT JOIN '.FURNITURE.' ON
                '.PRODUCT.'.id = '.FURNITURE.'.product_id';
    }

    public function insert($conn = null) : int
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        $SKU = $this->getSKU();
        $name = $this->getName();
        $price = $this->getPrice();

        $stmt = $conn->prepare(
            'INSERT INTO '.PRODUCT.' (sku, name, price)
            VALUES (?, ?, ?);'
        );
        $stmt->bind_param('ssd', $SKU, $name, $price);

        try {
            if ($stmt->execute() === true) {
                $this->setProductId($conn->insert_id);
                return $conn->insert_id;
            } else {
                throw new \Exception("Unable to insert object");
            }
        } catch (\mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                throw new DuplicateException('The provided SKU is already registered.');
            } else {
                throw $e;
            }
        }
    }

    public function jsonSerialize() : mixed
    {
        return [
            'id' => $this->getProductId(),
            'sku' => $this->getSKU(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'attribute' => $this->getFormatedAttr()
        ];
    }
}
