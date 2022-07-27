<?php

define("PRODUCT", "product");
define("DVD", "dvd");
define("BOOK", "book");
define("FURNITURE", "furniture");

class Database {
	const SERVERNAME = "127.0.0.1";
	const DATABASE = "scandiweb";
	const USERNAME = "root";
	const PASSWORD = "root";

	public static function connect() {
		$conn = new mysqli(self::SERVERNAME, self::USERNAME, self::PASSWORD);
		$conn->select_db(self::DATABASE);
		return $conn;
	}
}

trait Model {
	public abstract static function fromRow($row) : self;
	public abstract function insert($conn = NULL) : int; // should return id
	private abstract static function getSelectAllQuery() : string;

	public static function selectAll($conn = NULL) : array {
		if($conn === NULL) {
			$conn = Database::connect();
		}

		$sql = self::getSelectAllQuery();

		$rows = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

		$products = array();
		foreach($rows as $row) {
			array_push($products, self::fromRow($row));
		}
		return $products;
	}
}
