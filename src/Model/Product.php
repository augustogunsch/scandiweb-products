<?php
namespace ProductList\Model;

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

    public function setVariationId($id)
    {
        $this->variationId = $id;
    }

    public function setProductId($id)
    {
        $this->productId = $id;
    }

    abstract public function getFormatedAttr();

    public static function fromRow($row) : self
    {
        if ($row['size'] !== null) {
            return DVD::fromRow($row);
        } elseif ($row['weight'] !== null) {
            return Book::fromRow($row);
        } elseif ($row['height'] !== null) {
            return Furniture::fromRow($row);
        } else {
            throw new Exception("Product without a type");
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
                LEFT JOIN '.FURNITURE.' ON '.PRODUCT.'.id = '.FURNITURE.'.product_id;';
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
            throw new Exception("Unable to insert object");
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
