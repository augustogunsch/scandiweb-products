<?php
namespace ProductList\Model;

define("PRODUCT", "product");
define("DVD", "dvd");
define("BOOK", "book");
define("FURNITURE", "furniture");

class Database
{
    public static function connect()
    {
        $conn = new \mysqli(getenv('SERVERNAME'), getenv('USERNAME'), getenv('PASSWORD'));
        $conn->select_db(getenv('DATABASE'));
        return $conn;
    }
}
