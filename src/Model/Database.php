<?php
namespace ProductList\Model;

define("PRODUCT", "product");
define("DVD", "dvd");
define("BOOK", "book");
define("FURNITURE", "furniture");

class Database
{
    const SERVERNAME = "127.0.0.1";
    const DATABASE = "scandiweb";
    const USERNAME = "root";
    const PASSWORD = "root";

    public static function connect()
    {
        $conn = new \mysqli(self::SERVERNAME, self::USERNAME, self::PASSWORD);
        $conn->select_db(self::DATABASE);
        return $conn;
    }
}
