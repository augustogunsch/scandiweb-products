<?php
namespace ProductList\Model;

use ProductList\Exception\InvalidFieldException;

class Book extends Product
{
    private $weight;

    public function __construct(
        $sku,
        $name,
        $price,
        $weight,
        $productId = null,
        $variationId = null
    ) {
        parent::__construct($sku, $name, $price, $productId, $variationId);

        if (empty($weight) || !is_numeric($weight)) {
            throw new InvalidFieldException('Weight');
        }

        $this->weight = $weight;
    }

    public static function fromRow($row) : self
    {
        return new Book(
            $row['sku'],
            $row['name'],
            $row['price'],
            $row['weight'],
            $row['productId'],
            $row['variationId']
        );
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getFormatedAttr()
    {
        $weight = $this->getWeight();
        return "Weight: $weight KG";
    }

    private static function getSelectAllQuery() : string
    {
        return '
        SELECT
                '.PRODUCT.'.*,
                '.BOOK.'.id as variationId,
                weight
        FROM
                '.PRODUCT.'
        LEFT JOIN
                '.BOOK.' ON '.PRODUCT.'.id = '.BOOK.'.product_id';
    }

    public function insert($conn = null) : int
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        if ($this->getProductId() === null) {
            parent::insert($conn);
        }

        $productId = $this->getProductId();
        $weight = $this->getWeight();

        $stmt = $conn->prepare("INSERT INTO ".BOOK." (product_id, weight) VALUES (?, ?);");
        $stmt->bind_param('id', $productId, $weight);

        if ($stmt->execute() === true) {
            $this->setVariationId($conn->insert_id);
            return $conn->insert_id;
        } else {
            throw new \Exception("Unable to insert object");
        }
    }

    public function delete($conn = null)
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        $variationId = $this->getVariationId();
        $stmt = $conn->prepare('DELETE FROM '.BOOK.' WHERE id = ?');
        $stmt->bind_param('i', $variationId);

        if ($stmt->execute() === false) {
            throw new \Exception("Unable to delete product with id '$variationId'");
        }

        parent::delete($conn);
    }
}
