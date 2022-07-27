<?php
require '../model/product.php';

echo json_encode(Product::selectAll());
