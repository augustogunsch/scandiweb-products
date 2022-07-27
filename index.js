const productsList = document.getElementById('products');

const loadItems = () => {
	const xhttp = new XMLHttpRequest();

	xhttp.onload = function() {
		const products = JSON.parse(this.responseText);

		const boxes = products.map(product =>
			`<div class="product">
				<input type="checkbox" class="delete-checkbox" value="${product.id}">
				<p>
					${product.sku}<br>
					${product.name}<br>
					${product.price} $<br>
					${product.attribute}
				</p>
			 </div>
		`)

		productsList.innerHTML = boxes.join('\n');
	}

	xhttp.open('GET', 'view/products.php', true);
	xhttp.send();
}

loadItems();
