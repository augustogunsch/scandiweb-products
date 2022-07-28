<?php
namespace ProductList\Model;

trait Model
{
    abstract public static function fromRow($row) : self;
    abstract public function insert($conn = null) : int; // should return id
    abstract public function delete($conn = null);
    abstract private static function getSelectAllQuery() : string;

    public static function selectAll($conn = null) : array
    {
        if ($conn === null) {
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
