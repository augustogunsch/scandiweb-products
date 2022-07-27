<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="index.css"/>
		<title>Product List</title>
	</head>
	<body>
		<div id="header">
			<h1 id="title">Product List</h1>
			<div id="buttons">
				<button>ADD</button>
				<button id="delete-product-btn">MASS DELETE</button>
			</div>
		</div>
<?php
require 'model/product.php';

$products = Product::selectAll();
// TODO
echo json_encode($products);
?>
	</body>
</html>
