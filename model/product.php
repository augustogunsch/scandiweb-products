<?php
require_once 'db.php';

abstract class Product implements JsonSerializable {
	use Model;
	private $variationId;
	private $SKU;
	private $name;
	private $price;
	private $productId;

	public function __construct($SKU, $name, $price, $productId = NULL, $variationId = NULL) {
		$this->productId = $productId;
		$this->variationId = $variationId;
		$this->SKU = $SKU;
		$this->name = $name;
		$this->price = $price;
	}

	public function getSKU() {
		return $this->SKU;
	}

	public function getName() {
		return $this->name;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getProductId() {
		return $this->productId;
	}

	public function setVariationId($id) {
		$this->variationId = $id;
	}

	public function setProductId($id) {
		$this->productId = $id;
	}

	public abstract function getFormatedAttr();

	public static function fromRow($row) : self {
		if($row['size'] !== NULL) {
			return DVD::fromRow($row);
		} elseif($row['weight'] !== NULL) {
			return Book::fromRow($row);
		} elseif($row['height'] !== NULL) {
			return Furniture::fromRow($row);
		} else {
			throw new Exception("Product without a type");
		}
	}

	private static function getSelectAllQuery() : string {
		return 'SELECT '.PRODUCT.'.id as product_id, COALESCE('.DVD.'.id, '.BOOK.'.id, '.FURNITURE.'.id) as variation_id,
				name, sku, price, size, weight, width, height, length
			FROM '.PRODUCT.'
			LEFT JOIN '.DVD.' ON '.PRODUCT.'.id = '.DVD.'.product_id
			LEFT JOIN '.BOOK.' ON '.PRODUCT.'.id = '.BOOK.'.product_id
			LEFT JOIN '.FURNITURE.' ON '.PRODUCT.'.id = '.FURNITURE.'.product_id;';
	}

	public function insert($conn = NULL) : int {
		if($conn === NULL) {
			$conn = Database::connect();
		}

		$SKU = $this->getSKU();
		$name = $this->getName();
		$price = $this->getPrice();

		$stmt = $conn->prepare("INSERT INTO ".PRODUCT." (sku, name, price) VALUES (?, ?, ?);");
		$stmt->bind_param('ssd', $SKU, $name, $price);

		if($stmt->execute() === TRUE) {
			$this->setProductId($conn->insert_id);
			return $conn->insert_id;
		} else {
			throw new Exception("Unable to insert object");
		}
	}

	public function jsonSerialize() : mixed {
		return [
			'sku' => $this->getSKU(),
			'name' => $this->getName(),
			'price' => $this->getPrice(),
			'attribute' => $this->getFormatedAttr()
		];
	}
}

class DVD extends Product {
	private $size;

	public function __construct($sku, $name, $price, $size, $productId = null, $variationId = null) {
		parent::__construct($sku, $name, $price, $productId, $variationId);
		$this->size = $size;
	}

	public static function fromRow($row) : self {
		return new DVD($row['sku'], $row['name'], $row['price'], $row['size'], $row['product_id'], $row['variation_id']);
	}

	public function getSize() {
		return $this->size;
	}

	public function getFormatedAttr() {
		$size = $this->getSize();
		return "Size: $size MB";
	}

	private static function getSelectAllQuery() : string {
		return 'SELECT '.PRODUCT.'.*, '.DVD.'.id as variation_id, size
			FROM '.PRODUCT.' LEFT JOIN '.DVD.' ON '.PRODUCT.'.id = '.DVD.'.product_id';
	}

	public function insert($conn = NULL) : int {
		if($conn === NULL) {
			$conn = Database::connect();
		}

		if($this->getProductId() === NULL) {
			parent::insert($conn);
		}

		$productId = $this->getProductId();
		$size = $this->getSize();

		$stmt = $conn->prepare("INSERT INTO ".DVD." (product_id, size) VALUES (?, ?);");
		$stmt->bind_param('ii', $productId, $size);

		if($stmt->execute() === TRUE) {
			$this->setVariationId($conn->insert_id);
			return $conn->insert_id;
		} else {
			throw new Exception("Unable to insert object");
		}
	}
}

class Book extends Product {
	private $weight;

	public function __construct($sku, $name, $price, $weight, $productId = null, $variationId = null) {
		parent::__construct($sku, $name, $price, $productId, $variationId);
		$this->weight = $weight;
	}

	public static function fromRow($row) : self {
		return new Book($row['sku'], $row['name'], $row['price'], $row['weight'], $row['product_id'], $row['variation_id']);
	}

	public function getWeight() {
		return $this->weight;
	}

	public function getFormatedAttr() {
		$weight = $this->getWeight();
		return "Weight: $weight KG";
	}

	private static function getSelectAllQuery() : string {
		return 'SELECT '.PRODUCT.'.*, '.BOOK.'.id as variation_id, size
			FROM '.PRODUCT.' LEFT JOIN '.BOOK.' ON '.PRODUCT.'.id = '.BOOK.'.product_id';
	}

	public function insert($conn = NULL) : int {
		if($conn === NULL) {
			$conn = Database::connect();
		}

		if($this->getProductId() === NULL) {
			parent::insert($conn);
		}

		$productId = $this->getProductId();
		$weight = $this->getWeight();

		$stmt = $conn->prepare("INSERT INTO ".BOOK." (product_id, weight) VALUES (?, ?);");
		$stmt->bind_param('id', $productId, $weight);

		if($stmt->execute() === TRUE) {
			$this->setVariationId($conn->insert_id);
			return $conn->insert_id;
		} else {
			throw new Exception("Unable to insert object");
		}
	}
}

class Furniture extends Product {
	private $height;
	private $width;
	private $length;

	public function __construct($SKU, $name, $price, $height, $width, $length, $productId = NULL, $variationId = NULL) {
		parent::__construct($SKU, $name, $price, $productId, $variationId);
		$this->height = $height;
		$this->width = $width;
		$this->length = $length;
	}

	public static function fromRow($row) : self {
		return new Furniture($row['sku'], $row['name'], $row['price'], $row['height'],
			$row['width'], $row['length'], $row['product_id'], $row['variation_id']);
	}

	public function getHeight() {
		return $this->height;
	}

	public function getWidth() {
		return $this->width;
	}

	public function getLength() {
		return $this->length;
	}

	public function getFormatedAttr() {
		$height = $this->getHeight();
		$width = $this->getWidth();
		$length = $this->getLength();
		return 'Dimension: '.$height.'x'.$width.'x'.$length;
	}

	private static function getSelectAllQuery() : string {
		return 'SELECT '.PRODUCT.'.*, '.FURNITURE.'.id as variation_id, size
			FROM '.PRODUCT.' LEFT JOIN '.FURNITURE.' ON '.PRODUCT.'.id = '.FURNITURE.'.product_id';
	}

	public function insert($conn = NULL) : int {
		if($conn === NULL) {
			$conn = Database::connect();
		}

		if($this->getProductId() === NULL) {
			parent::insert($conn);
		}

		$productId = $this->getProductId();
		$height = $this->getHeight();
		$width = $this->getWidth();
		$length = $this->getLength();

		$stmt = $conn->prepare("INSERT INTO ".FURNITURE." (product_id, height, width, length) VALUES (?, ?, ?, ?);");
		$stmt->bind_param('iddd', $productId, $height, $width, $length);

		if($stmt->execute() === TRUE) {
			$this->setVariationId($conn->insert_id);
			return $conn->insert_id;
		} else {
			throw new Exception("Unable to insert object");
		}
	}
}
