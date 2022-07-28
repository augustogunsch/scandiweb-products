<?php
namespace ProductList\View;

abstract class View
{
    protected static function expectArgs(array $args, array $provided) {
        $keys = array_keys($provided);

        $missing = array_diff($args, $keys);

        if (count($missing) > 0) {
            http_response_code(400);

            $missing = join("', '", $missing);

            echo "Missing parameters '$missing'";

            return false;
        }
        return true;
    }
}
