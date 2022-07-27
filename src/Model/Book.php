<?php
namespace ProductList\Model;

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
        $this->weight = $weight;
    }

    public static function fromRow($row) : self
    {
        return new Book(
            $row['sku'],
            $row['name'],
            $row['price'],
            $row['weight'],
            $row['product_id'],
            $row['variation_id']
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
        return 'SELECT '.PRODUCT.'.*, '.BOOK.'.id as variation_id, size
            FROM '.PRODUCT.' LEFT JOIN '.BOOK.' ON '.PRODUCT.'.id = '.BOOK.'.product_id';
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
            throw new Exception("Unable to insert object");
        }
    }
}
