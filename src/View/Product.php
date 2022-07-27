<?php
require '../../Autoload.php';

echo json_encode(ProductList\Model\Product::selectAll());
