<?php
namespace ProductList\Model;

class Furniture extends Product
{
    private $height;
    private $width;
    private $length;

    public function __construct(
        $SKU,
        $name,
        $price,
        $height,
        $width,
        $length,
        $productId = null,
        $variationId = null
    ) {
        parent::__construct($SKU, $name, $price, $productId, $variationId);
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public static function fromRow($row) : self
    {
        return new Furniture(
            $row['sku'],
            $row['name'],
            $row['price'],
            $row['height'],
            $row['width'],
            $row['length'],
            $row['product_id'],
            $row['variation_id']
        );
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getFormatedAttr()
    {
        $height = $this->getHeight();
        $width = $this->getWidth();
        $length = $this->getLength();
        return 'Dimension: '.$height.'x'.$width.'x'.$length;
    }

    private static function getSelectAllQuery() : string
    {
        return 'SELECT '.PRODUCT.'.*, '.FURNITURE.'.id as variation_id, size
            FROM '.PRODUCT.'
            LEFT JOIN '.FURNITURE.' ON '.PRODUCT.'.id = '.FURNITURE.'.product_id';
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
        $height = $this->getHeight();
        $width = $this->getWidth();
        $length = $this->getLength();

        $stmt = $conn->prepare(
            "INSERT INTO ".FURNITURE." (product_id, height, width, length)
            VALUES (?, ?, ?, ?);"
        );
        $stmt->bind_param('iddd', $productId, $height, $width, $length);

        if ($stmt->execute() === true) {
            $this->setVariationId($conn->insert_id);
            return $conn->insert_id;
        } else {
            throw new Exception("Unable to insert object");
        }
    }
}
