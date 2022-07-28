<?php
namespace ProductList\Model;

use ProductList\Exception\NotFoundException;

abstract class Product implements \JsonSerializable
{
    use Model;
    private $variationId;
    private $SKU;
    private $name;
    private $price;
    private $productId;

    public function __construct(
        $SKU,
        $name,
        $price,
        $productId = null,
        $variationId = null
    ) {
        $this->productId = $productId;
        $this->variationId = $variationId;
        $this->SKU = $SKU;
        $this->name = $name;
        $this->price = $price;
    }

    public function getSKU()
    {
        return $this->SKU;
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
            throw new \Exception("Unable to delete product with id '$id'");
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
        if ($row['size'] !== null) {
            return DVD::fromRow($row);
        } elseif ($row['weight'] !== null) {
            return Book::fromRow($row);
        } elseif ($row['height'] !== null) {
            return Furniture::fromRow($row);
        } else {
            throw new \Exception("Product without a type");
        }
    }

    private static function getSelectAllQuery() : string
    {
        return 'SELECT '.PRODUCT.'.id as product_id,
                    COALESCE('.DVD.'.id, '.BOOK.'.id, '.FURNITURE.'.id) as variation_id,
                    name, sku, price, size, weight, width, height, length
                FROM '.PRODUCT.'
                LEFT JOIN '.DVD.' ON '.PRODUCT.'.id = '.DVD.'.product_id
                LEFT JOIN '.BOOK.' ON '.PRODUCT.'.id = '.BOOK.'.product_id
                LEFT JOIN '.FURNITURE.' ON '.PRODUCT.'.id = '.FURNITURE.'.product_id';
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

        if ($stmt->execute() === true) {
            $this->setProductId($conn->insert_id);
            return $conn->insert_id;
        } else {
            throw new \Exception("Unable to insert object");
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
