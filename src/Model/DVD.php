<?php
namespace ProductList\Model;

class DVD extends Product
{
    private $size;

    public function __construct(
        $sku,
        $name,
        $price,
        $size,
        $productId = null,
        $variationId = null
    ) {
        parent::__construct($sku, $name, $price, $productId, $variationId);
        $this->size = $size;
    }

    public static function fromRow($row) : self
    {
        return new DVD(
            $row['sku'],
            $row['name'],
            $row['price'],
            $row['size'],
            $row['product_id'],
            $row['variation_id']
        );
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getFormatedAttr()
    {
        $size = $this->getSize();
        return "Size: $size MB";
    }

    private static function getSelectAllQuery() : string
    {
        return 'SELECT '.PRODUCT.'.*, '.DVD.'.id as variation_id, size
            FROM '.PRODUCT.' LEFT JOIN '.DVD.' ON '.PRODUCT.'.id = '.DVD.'.product_id';
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
        $size = $this->getSize();

        $stmt = $conn->prepare("INSERT INTO ".DVD." (product_id, size) VALUES (?, ?);");
        $stmt->bind_param('ii', $productId, $size);

        if ($stmt->execute() === true) {
            $this->setVariationId($conn->insert_id);
            return $conn->insert_id;
        } else {
            throw new Exception("Unable to insert object");
        }
    }

    public function delete($conn = null)
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        $variationId = $this->getVariationId();
        $stmt = $conn->prepare('DELETE FROM '.DVD.' WHERE id = ?');
        $stmt->bind_param('i', $variationId);

        if ($stmt->execute() === false) {
            throw new \Exception("Unable to delete product with id '$id'");
        }

        parent::delete($conn);
    }
}
