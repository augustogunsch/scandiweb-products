<?php
namespace ProductList\Model;

use ProductList\Exception\InvalidFieldException;

class Furniture extends Product
{
    private $height;
    private $width;
    private $length;

    public function __construct(
        $sku,
        $name,
        $price,
        $height,
        $width,
        $length,
        $productId = null,
        $variationId = null
    ) {
        parent::__construct($sku, $name, $price, $productId, $variationId);

        if (empty($height) || !is_numeric($height)) {
            throw new InvalidFieldException('Height');
        }

        if (empty($width) || !is_numeric($width)) {
            throw new InvalidFieldException('Width');
        }

        if (empty($length) || !is_numeric($length)) {
            throw new InvalidFieldException('Length');
        }

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
            $row['productId'],
            $row['variationId']
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
        return '
        SELECT
                '.PRODUCT.'.*,
                '.FURNITURE.'.id as variationId,
                height,
                width,
                length
        FROM
                '.PRODUCT.'
        LEFT JOIN '.FURNITURE.' ON
                '.PRODUCT.'.id = '.FURNITURE.'.product_id';
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
            throw new \Exception("Unable to insert object");
        }
    }

    public function delete($conn = null)
    {
        if ($conn === null) {
            $conn = Database::connect();
        }

        $variationId = $this->getVariationId();
        $stmt = $conn->prepare('DELETE FROM '.FURNITURE.' WHERE id = ?');
        $stmt->bind_param('i', $variationId);

        if ($stmt->execute() === false) {
            throw new \Exception("Unable to delete product with id '$variationId'");
        }

        parent::delete($conn);
    }
}
