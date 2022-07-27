<?php
require 'autoload.php';

echo json_encode(ProductList\Model\Product::selectAll());
